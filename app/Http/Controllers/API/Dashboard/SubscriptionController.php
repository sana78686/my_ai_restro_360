<?php
namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Customer;
use App\Models\Central\Plan as CentralPlan;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|uuid|exists:tenants,id',
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $plan = Plan::findOrFail($validated['plan_id']);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create customer on Stripe if not exists
            if (!$tenant->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $tenant->email,
                    'name' => $tenant->name,
                ]);
                $tenant->update(['stripe_customer_id' => $customer->id]);
            }

            // Create subscription on Stripe
            $stripeSubscription = StripeSubscription::create([
                'customer' => $tenant->stripe_customer_id,
                'items' => [['price' => $plan->stripe_price_id]],
            ]);

            // Store locally
            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(), // simple example, can use plan interval
            ]);

            $tenant->update([
                'subscription_status' => 'active',
                'stripe_subscription_id' => $stripeSubscription->id,
                'subscription_ends_at' => $subscription->ends_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully.',
                'subscription' => $subscription,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

//  public function show(Request $request)
//     {
//         $tenant = $request->user(); // assuming tenant is authenticated

//         $subscription = Subscription::with('plan')
//             ->where('tenant_id', $tenant->id)
//             ->latest()
//             ->first();

//         if (!$subscription) {
//             return response()->json(['message' => 'No subscription found'], 404);
//         }

//         return response()->json([
//             'plan_name' => $subscription->plan->name,
//             'status' => $subscription->status,
//             'starts_at' => $subscription->starts_at,
//             'ends_at' => $subscription->ends_at,
//             'is_expired' => now()->greaterThan($subscription->ends_at),
//         ]);
//     }

   public function renew(Request $request)
{
    $tenant = $request->user();
    $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();

    if (!$subscription) {
        return response()->json(['error' => 'No active subscription found'], 404);
    }

    $plan = $subscription->plan;

    // Free plan → extend locally
    if ($plan->price == 0) {
        $subscription->update([
            'starts_at' => now(),
            'ends_at' => $plan->interval === 'year'
                ? now()->addYear()
                : now()->addMonth(),
        ]);

        return response()->json([
            'message' => 'Free plan renewed successfully!',
            'new_expiry' => $subscription->ends_at,
        ]);
    }

    // Paid plan → generate Stripe checkout
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    $checkoutSession = \Stripe\Checkout\Session::create([
        'customer' => $tenant->stripe_customer_id,
        'payment_method_types' => ['card'],
        'mode' => 'subscription',
        'line_items' => [[
            'price' => $plan->stripe_price_id,
            'quantity' => 1,
        ]],
        'success_url' => config('app.frontend_url') . '/dashboard?renewal=success',
        'cancel_url' => config('app.frontend_url') . '/dashboard?renewal=cancelled',
    ]);

    return response()->json([
        'checkout_url' => $checkoutSession->url,
    ]);
}


 public function show()
    {
        try {
            $tenant = tenant();
            if (! $tenant) {
                return response()->json([
                    'message' => 'No tenant context.',
                    'plan' => null,
                ]);
            }

            $tenantId = $tenant->id;

            $subscription = Subscription::with('plan')
                ->where('tenant_id', $tenantId)
                ->latest()
                ->first();

            if (! $subscription) {
                return response()->json([
                    'message' => 'No active subscription found.',
                    'plan' => null,
                ]);
            }

            return response()->json([
                'plan_name' => $subscription->plan?->name,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at,
                'ends_at' => $subscription->ends_at,
            ]);
        } catch (\Throwable $e) {
            Log::warning('tenant my-subscription failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Unable to load subscription.',
                'plan' => null,
            ]);
        }
    }

    /**
     * Display a listing of all subscriptions with tenant and plan info.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['tenant', 'plan']);

        // Filter by tenant
        if ($request->has('tenant_id') && $request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Filter by plan
        if ($request->has('plan_id') && $request->plan_id) {
            $query->where('plan_id', $request->plan_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by tenant name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            })->orWhereHas('plan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $subscriptions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    /**
     * Get subscription statistics.
     */
    public function getStats(Request $request)
    {
        $query = Subscription::query();

        // Apply date filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Total subscriptions
        $totalSubscriptions = (clone $query)->count();

        // Active subscriptions
        $activeSubscriptions = (clone $query)->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->count();

        // Pending subscriptions
        $pendingSubscriptions = (clone $query)->where('status', 'pending_payment')->count();

        // Expired subscriptions
        $expiredSubscriptions = (clone $query)->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->where('status', 'active')
                     ->where('ends_at', '<', now());
              });
        })->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_subscriptions' => $totalSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
                'pending_subscriptions' => $pendingSubscriptions,
                'expired_subscriptions' => $expiredSubscriptions,
            ]
        ]);
    }

}
