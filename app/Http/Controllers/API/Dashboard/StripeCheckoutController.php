<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionCheckoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

class StripeCheckoutController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:central.plans,id',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $plan = Plan::findOrFail($validated['plan_id']);
        $user = Auth::user();

        try {
            $session = StripeCheckoutSession::create([
                'payment_method_types' => [
                    'card',
                    // 'us_bank_account'
                ],
                'mode' => 'subscription',
                'customer_email' => $user->email ?? null,
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'tenant_id' => tenant('id'),
                    'plan_id' => (string) $plan->id,
                ],
                // 'success_url' => config('app.url') . '/stripe/success?session_id={CHECKOUT_SESSION_ID}',
                'success_url' => config('app.url') . '/dashboard/stripesuccess?session_id={CHECKOUT_SESSION_ID}',

                'cancel_url' => config('app.url') . '/stripe/cancel?session_id={CHECKOUT_SESSION_ID}',
            ]);

            SubscriptionCheckoutSession::updateOrCreate(
                ['stripe_session_id' => $session->id],
                [
                    'tenant_id' => tenant('id'),
                    'plan_id' => $plan->id,
                    'status' => 'pending',
                    'completed_at' => null,
                    'canceled_at' => null,
                ]
            );

            return response()->json([
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe checkout session', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
                'plan_id' => $plan->id ?? null,
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->redirectToFrontend('missing-session');
        }

        $checkout = SubscriptionCheckoutSession::where('stripe_session_id', $sessionId)->first();

        if (!$checkout) {
            return $this->redirectToFrontend('session-not-found');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeCheckoutSession::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error('Unable to retrieve Stripe session', ['session_id' => $sessionId, 'error' => $e->getMessage()]);
            return $this->redirectToFrontend('stripe-error');
        }

        if (!in_array($session->payment_status, ['paid']) && $session->status !== 'complete') {
            return $this->redirectToFrontend('payment-pending');
        }

        $plan = Plan::find($checkout->plan_id);

        if (!$plan) {
            Log::warning('Plan not found for checkout session', ['checkout_id' => $checkout->id]);
            return $this->redirectToFrontend('plan-missing');
        }

        Subscription::updateOrCreate(
            ['tenant_id' => $checkout->tenant_id],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $plan->interval === 'year' ? now()->addYear() : now()->addMonth(),
                'stripe_subscription_id' => $session->subscription,
            ]
        );

        $checkout->update([
            'status' => 'completed',
            'completed_at' => now(),
            'canceled_at' => null,
        ]);

        return $this->redirectToFrontend('success');
    }

    public function handleCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $checkout = SubscriptionCheckoutSession::where('stripe_session_id', $sessionId)->first();

            if ($checkout) {
                $checkout->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                ]);
            }
        }

        return $this->redirectToFrontend('canceled');
    }

    protected function redirectToFrontend(string $status)
    {
        $baseUrl = rtrim(config('app.frontend_url', config('app.url')), '/');

        return redirect()->to("{$baseUrl}/dashboard/pricing?checkout={$status}");
    }
public function verifyCheckoutSession(Request $request)
{
    $sessionId = $request->query('session_id');

    if (!$sessionId) {
        return response()->json([
            'success' => false,
            'message' => 'Missing session_id'
        ], 400);
    }

    // Get stored checkout info
    $checkout = SubscriptionCheckoutSession::where('stripe_session_id', $sessionId)->first();

    if (!$checkout) {
        return response()->json([
            'success' => false,
            'message' => 'Checkout session not found'
        ], 404);
    }

    Stripe::setApiKey(config('services.stripe.secret'));

    try {
        $session = StripeCheckoutSession::retrieve($sessionId);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Stripe session fetch failed',
            'error'   => $e->getMessage()
        ], 500);
    }

    // Check status
    if ($session->payment_status !== 'paid') {
        return response()->json([
            'success' => false,
            'message' => 'Payment not completed yet'
        ], 200);
    }

    // Now retrieve Stripe subscription
    $subscription = \Stripe\Subscription::retrieve($session->subscription);

    // Save into DB
    $stored = Subscription::updateOrCreate(
        ['stripe_subscription_id' => $subscription->id],
        [
            'tenant_id' => $checkout->tenant_id,
            'plan_id'   => $checkout->plan_id,
            'status'    => $subscription->status,
            'starts_at' => date('Y-m-d H:i:s', $subscription->current_period_start),
            'ends_at'   => date('Y-m-d H:i:s', $subscription->current_period_end),
        ]
    );

    // Update checkout record
    $checkout->update([
        'status'       => 'completed',
        'completed_at' => now(),
        'canceled_at'  => null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Subscription activated',
        'subscription' => $stored
    ], 200);
}


}
