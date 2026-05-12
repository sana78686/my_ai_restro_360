<?php

namespace App\Http\Controllers\API\Dashboard;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Payment;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Mail\NewSubscriberMail;
use Illuminate\Validation\Rule;
use App\Mail\SubscriptionCodeMail;
use App\Models\tenant\Subscribtion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionVerifiedMail;
use Illuminate\Support\Facades\Validator;
use App\Helpers\TenantHost;
use App\Helpers\FileUpload;
use App\Mail\OwnerAccountApprovedMail;
use Stripe\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use Stripe\Subscription as StripeSubscription;








class TenantController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Tenant::query();

    //     if ($request->has('search')) {
    //         $query->search($request->search);
    //     }

    //     if ($request->has('status')) {
    //         $query->status($request->status);
    //     }

    //     $tenants = $query->orderBy('created_at', 'desc')->get();
    //     // dd($tenants);
    //     Log::info('Tenants fetched successfully', ['tenants' => $tenants]);
    //     return response()->json([
    //         'success' => true,
    //         'data' => $tenants
    //     ]);
    // }


    public function index(Request $request)
    {
        $query = Tenant::query();

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            $query->status($request->status);
        }

        $tenants = $query->orderBy('created_at', 'desc')->get();

        // 🔹 Fetch logo from each tenant's database
        $tenantData = [];
        foreach ($tenants as $tenant) {
            $ownerApproved = false;
            try {
                // Switch to tenant DB
                tenancy()->initialize($tenant);

                // Try to get logo from settings.media (S3 public_url)
                $logoUrl = DB::table('media')
                    ->where('model_type', 'App\\Models\\Setting')
                    ->value('custom_properties->public_url');

                $ownerUser = User::query()->where('email', $tenant->owner_email)->first();
                $ownerApproved = $ownerUser && $ownerUser->is_verified_by_admin;

                $tenantData[] = [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'subdomain' => $tenant->subdomain,
                    'status' => $tenant->status,
                    'owner_name' => $tenant->owner_name,
                    'owner_email' => $tenant->owner_email,
                    'is_paid' => $tenant->is_paid,
                    'logo' => $logoUrl,
                    'logo_url' => $logoUrl,
                    'created_at' => $tenant->created_at,
                    'owner_account_approved' => $ownerApproved,
                ];
            } catch (\Exception $e) {
                // Fallback if logo cannot be fetched
                Log::error("Error fetching logo for tenant {$tenant->id}: " . $e->getMessage());
                $tenantData[] = [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'subdomain' => $tenant->subdomain,
                    'status' => $tenant->status,
                    'owner_name' => $tenant->owner_name,
                    'owner_email' => $tenant->owner_email,
                    'is_paid' => $tenant->is_paid,
                    'logo' => null,
                    'logo_url' => null,
                    'created_at' => $tenant->created_at,
                    'owner_account_approved' => $ownerApproved,
                ];
            } finally {
                tenancy()->end(); // always return to landlord DB
            }
        }

        return response()->json([
            'success' => true,
            'data' => $tenantData
        ]);
    }


    public function store(Request $request)
    {

        try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:tenants',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive', 'pending'])],
            'plan_id' => 'nullable|exists:plans,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $data = collect($validator->validated())->except(['logo'])->all();

        // ✅ Create Tenant
        $tenant = Tenant::create($data);

        if ($request->hasFile('logo')) {
            $result = FileUpload::upload($request->file('logo'), 'tenant_logos', null, true, (string) $tenant->id);
            if (! empty($result['paths'][0])) {
                $tenant->logo = $result['paths'][0];
                $tenant->save();
            }
        }
    
       
            // ✅ Create Stripe Customer
            Stripe::setApiKey(config('services.stripe.secret'));
    
            $customer = Customer::create([
                'email' => $tenant->owner_email,
                'name' => $tenant->owner_name ?? ('Tenant ' . $tenant->id),
            ]);
    
            $tenant->update(['stripe_customer_id' => $customer->id]);
    
            // ✅ Assign Plan (Free by default)
            $plan = $request->filled('plan_id')
                ? Plan::find($request->plan_id)
                : Plan::where('price', 0)->first(); // default free plan
    
            if ($plan) {
                if ($plan->price == 0) {
                    // Free plan — no payment required
                    Subscription::create([
                        'tenant_id' => $tenant->id,
                        'plan_id' => $plan->id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => $plan->interval === 'year'
                            ? now()->addYear()
                            : now()->addMonth(),
                    ]);
    
                    $tenant->update([
                        'subscription_status' => 'active',
                        'status' => 'active',
                    ]);
                } else {
                    // Paid plan — create Stripe subscription if stripe_price_id exists
                    if ($plan->stripe_price_id) {
                        $stripeSubscription = StripeSubscription::create([
                            'customer' => $customer->id,
                            'items' => [['price' => $plan->stripe_price_id]],
                        ]);
                        
                        // ✅ Create subscription record in database
                        Subscription::create([
                            'tenant_id' => $tenant->id,
                            'plan_id' => $plan->id,
                            'stripe_subscription_id' => $stripeSubscription->id,
                            'status' => 'pending_payment',
                            'starts_at' => now(),
                        ]);
                    } else {
                        // Plan has no stripe_price_id - create subscription without Stripe
                        Subscription::create([
                            'tenant_id' => $tenant->id,
                            'plan_id' => $plan->id,
                            'status' => 'pending_payment',
                            'starts_at' => now(),
                        ]);
                    }
    
                    // Mark as pending payment until checkout is completed
                    $tenant->update([
                        'subscription_status' => 'pending_payment',
                        'status' => 'inactive',
                    ]);
                }
            }
    
        } catch (\Exception $e) {
            // Rollback or handle error gracefully
            return response()->json([
                'success' => false,
                'message' => 'Tenant created, but Stripe integration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully.',
            'data' => $tenant,
        ], 201);
    }

    public function show(Tenant $tenant)
    {
        return response()->json([
            'success' => true,
            'data' => $tenant
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => ['required', 'string', 'max:255', Rule::unique('tenants')->ignore($tenant->id)],
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => ['required', Rule::in(['active', 'inactive', 'pending'])]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('logo')) {
            $result = FileUpload::upload($request->file('logo'), 'tenant_logos', $tenant->logo, true, (string) $tenant->id);
            $data['logo'] = $result['paths'][0] ?? $tenant->logo;
        }

        $tenant->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tenant updated successfully',
            'data' => $tenant
        ]);
    }

    public function destroy(Tenant $tenant)
    {
        $tenantKey = (string) $tenant->getTenantKey();

        try {
            foreach ($tenant->media()->get() as $media) {
                $media->delete();
            }

            FileUpload::deleteStored($tenant->logo);
            FileUpload::deleteTenantS3Prefix($tenantKey);

            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Restaurant and all associated data were removed.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Tenant delete failed', [
                'tenant_id' => $tenantKey,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete restaurant. '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'trial', 'active', 'inactive', 'suspended'])]
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->status = $request->status;
        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Tenant status updated successfully',
            'data' => $tenant
        ]);
    }

    /**
     * Allow the restaurant owner to sign in (and skip email OTP by marking email verified).
     */
    public function approveOwnerAccount(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        try {
            tenancy()->initialize($tenant);

            /** @var \App\Models\User|null $owner */
            $owner = User::query()->where('email', $tenant->owner_email)->first();

            if (! $owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner user not found for this restaurant.',
                ], 404);
            }

            $owner->is_verified_by_admin = true;
            if (! $owner->email_verified_at) {
                $owner->email_verified_at = Carbon::now();
            }
            $owner->otp = null;
            $owner->otp_expires_at = null;
            $owner->save();
        } finally {
            tenancy()->end();
        }

        Tenant::query()->whereKey($tenant->id)->update([
            'account_verification_token' => null,
            'account_verification_token_expires_at' => null,
        ]);

        $loginUrl = TenantHost::loginUrl((string) ($tenant->subdomain ?? ''), (string) $tenant->id);
        try {
            Mail::to($tenant->owner_email)->send(new OwnerAccountApprovedMail($tenant, $loginUrl));
        } catch (\Throwable $e) {
            Log::error('Owner approval email failed: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Owner account approved. They can sign in without waiting for email verification.',
        ]);
    }


    public function requestSubscription(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $tenant = Tenant::where(
            ['owner_email' => $request->email],
        )->first();

        $code = $tenant->id;
        // dd($code);
        // $tenant->update([
        //     'subscription_code' => $code,
        //     'subscription_code_expires_at' => now()->addMinutes(10),
        // ]);

        Mail::to($tenant->owner_email)->send(new SubscriptionCodeMail($code));

        return response()->json(
            ['message' => 'Verification code sent to email',
             'code' => $code]);
    }


    /**
     * Step 2: User submits code to verify
     */
    public function verifySubscription(Request $request)
    {
        $request->validate([
            'code'  => 'required'
        ]);

        $tenant = Tenant::where('id', $request->code)
            ->first();
        //  dd($tenant->is_paid);
        if (!$tenant) {
            return response()->json(['message' => 'Invalid code or email'], 422);
        }

        $tenant->status = 'active';
        $tenant->is_paid = 1;
        $tenant->last_subscription_date = Carbon::today()->toDateString();
        $tenant->save();

        // Send verification success email to the tenant owner
    try {
        Mail::to($tenant->owner_email)->send(new SubscriptionVerifiedMail($tenant));

        // mail to the super admin for new subscriber
        $super = User::find(1);
        Mail::to($super->email)->send(new NewSubscriberMail($tenant));

    } catch (\Exception $e) {
        // Log the error but don't fail the request
        Log::error('Failed to send subscription verification email: ' . $e->getMessage());
    }

        return response()->json(
            ['message' => 'Subscription verified successfully',
        'tenant' => $tenant]);
    }




     public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        // Find tenant by email
        $tenant = Tenant::where('owner_email', $request->email)->first();
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found for this email'
            ], 404);
        }

        // Convert amount to cents
        $amountInCents = $request->amount * 100;

        $intent = PaymentIntent::create([
            'amount' => $amountInCents,
            'currency' => $request->currency,
            'metadata' => [
                'email' => $request->email,
                'plan_id' => $request->plan_id,
                'tenant_id' => $tenant->id,
            ],
        ]);

        // Save in DB with tenant_id
        $payment = Payment::create([
            'email' => $request->email,
            'tenant_id' => $tenant->id,
            'plan_id' => (int)$request->plan_id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'stripe_payment_intent_id' => $intent->id,
            'status' => 'pending',
            'payment_type' => 'one_time',
        ]);

        return response()->json([
            'clientSecret' => $intent->client_secret
        ]);
    }

    // Step 2: Stripe Webhook (auto called by Stripe)
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            if ($payment) {
                $payment->status = 'succeeded';
                // Update stripe_charge_id if available
                if (isset($paymentIntent->charges->data[0]->id)) {
                    $payment->stripe_charge_id = $paymentIntent->charges->data[0]->id;
                }
                $payment->save();
            }
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $paymentIntent = $event->data->object;
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            if ($payment) {
                $payment->status = 'failed';
                $payment->save();
            }
        }

        // Handle recurring subscription invoice payments
        if ($event->type === 'invoice.payment_succeeded') {
            $invoice = $event->data->object;
            
            // Find subscription by stripe_subscription_id
            $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
            
            if ($subscription) {
                // Create payment record for recurring payment
                Payment::create([
                    'email' => $invoice->customer_email ?? $subscription->tenant->owner_email,
                    'tenant_id' => $subscription->tenant_id,
                    'plan_id' => $subscription->plan_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $invoice->amount_paid / 100, // Convert from cents
                    'currency' => $invoice->currency,
                    'stripe_payment_intent_id' => $invoice->payment_intent ?? null,
                    'stripe_invoice_id' => $invoice->id,
                    'stripe_charge_id' => $invoice->charge ?? null,
                    'status' => 'succeeded',
                    'payment_type' => 'recurring',
                ]);
            }
        }

        return response('Webhook handled', 200);
    }

    public function activateSubscription(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'email' => 'required|email',
        'plan_id' => 'required|string',
        'amount' => 'required|numeric',
        'stripe_payment_intent_id' => 'required|string',
    ]);

    // Find tenant
    $tenant = Tenant::where('owner_email', $request->email)->first();
    if (!$tenant) {
        return response()->json([
            'message' => 'Tenant not found'
        ], 404);
    }

    // Find or create subscription
    $subscription = Subscription::where('tenant_id', $tenant->id)
        ->where('plan_id', $request->plan_id)
        ->latest()
        ->first();

    // Update or create a payment record with tenant_id
    $payment = Payment::updateOrCreate(
        ['stripe_payment_intent_id' => $request->stripe_payment_intent_id],
        [
            'email' => $request->email,
            'tenant_id' => $tenant->id,
            'plan_id' => (int)$request->plan_id,
            'subscription_id' => $subscription ? $subscription->id : null,
            'amount' => $request->amount,
            'status' => 'succeeded',
            'payment_type' => $subscription ? 'recurring' : 'one_time',
        ]
    );

    // Update tenant subscription status
    $tenant->is_paid = 1;
    $tenant->save();

    // Update subscription status if exists
    if ($subscription) {
        $subscription->status = 'active';
        $subscription->save();
    }

    return response()->json([
        'message' => 'Subscription activated successfully',
        'payment' => $payment,
    ]);
}




// Controller Method
public function findByEmail(Request $request)
{
    // dd('here');
    $request->validate([
        'email' => 'required|email'
    ]);

    $tenant = Tenant::where('owner_email', $request->email)
        // ->where('is_active', true)
        ->first(['id', 'business_name', 'subdomain', 'owner_email']);

    if (!$tenant) {
        return response()->json([
            'success' => false,
            'message' => 'No restaurant found with this email address'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'tenant' => $tenant
    ]);
}

}
