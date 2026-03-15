<?php
// app/Services/PaymentGateways/StripeGateway.php

namespace App\Services\PaymentGateways;

use Stripe\StripeClient;
use App\Contracts\PaymentGateway;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGateway
{
    protected StripeClient $stripe;
    protected array $config;
    
    public function __construct(array $config = [])
    {
        Log::info('Initializing StripeGateway with config keys:', array_keys($config));
        
        // Validate that we have the required credentials from database
        if (empty($config['secret_key'])) {
            throw new \InvalidArgumentException('Stripe secret key is required. Make sure credentials are properly saved in database.');
        }
        
        $this->config = $config;
        
        // Use ONLY the database credentials, never fall back to config
        $this->stripe = new StripeClient($config['secret_key']);
        
        Log::info('StripeGateway initialized successfully with dynamic credentials');
    }
    
    /**
     * Get credentials for this gateway
     */
    public function getCredentials(): array
    {
        return $this->config;
    }
    
    public function createCustomer(array $data): array
    {
        try {
            Log::info('Creating Stripe customer with dynamic credentials');

            $customerData = [
                'email' => $data['email'],
                'name' => $data['name'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ];

            if (isset($data['phone'])) {
                $customerData['phone'] = $data['phone'];
            }
            if (isset($data['description'])) {
                $customerData['description'] = $data['description'];
            }

            $customer = $this->stripe->customers->create($customerData);
            
            return [
                'success' => true,
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'data' => $customer->toArray(),
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe customer creation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }
    
    /**
     * Create payment intent - USING DYNAMIC CREDENTIALS
     */
    public function createPayment(array $data): array
    {
        try {
            Log::info('Creating Stripe payment with dynamic credentials');

            $paymentData = [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'confirmation_method' => 'automatic',
                'confirm' => false, // Don't confirm immediately
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => $data['metadata'] ?? [],
            ];

            // Add optional parameters
            if (isset($data['customer_id'])) {
                $paymentData['customer'] = $data['customer_id'];
            }
            if (isset($data['payment_method_id'])) {
                $paymentData['payment_method'] = $data['payment_method_id'];
            }
            if (isset($data['return_url'])) {
                $paymentData['return_url'] = $data['return_url'];
            }
            if (isset($data['description'])) {
                $paymentData['description'] = $data['description'];
            }

            $paymentIntent = $this->stripe->paymentIntents->create($paymentData);
            
            Log::info('Stripe payment intent created with dynamic credentials');
            
            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
                'next_action' => $paymentIntent->next_action ?? null,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment creation failed with dynamic credentials:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * Alternative: Create payment with specific payment method types
     */
    public function createCardPayment(array $data): array
    {
        try {
            Log::info('Creating Stripe card payment with dynamic credentials');

            $paymentData = [
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'usd',
                'confirmation_method' => 'automatic',
                'confirm' => false,
                'payment_method_types' => ['card'],
                'metadata' => $data['metadata'] ?? [],
            ];

            // Add optional parameters
            if (isset($data['customer_id'])) {
                $paymentData['customer'] = $data['customer_id'];
            }
            if (isset($data['return_url'])) {
                $paymentData['return_url'] = $data['return_url'];
            }
            if (isset($data['description'])) {
                $paymentData['description'] = $data['description'];
            }

            $paymentIntent = $this->stripe->paymentIntents->create($paymentData);
            
            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe card payment creation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * Confirm a payment intent with payment method
     */
    public function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId): array
    {
        try {
            Log::info('Confirming Stripe payment intent with dynamic credentials');

            $paymentIntent = $this->stripe->paymentIntents->update($paymentIntentId, [
                'payment_method' => $paymentMethodId,
            ]);

            $confirmedIntent = $this->stripe->paymentIntents->confirm($paymentIntentId);
            
            return [
                'success' => true,
                'payment_intent_id' => $confirmedIntent->id,
                'status' => $confirmedIntent->status,
                'client_secret' => $confirmedIntent->client_secret,
                'next_action' => $confirmedIntent->next_action ?? null,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment confirmation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }
    
    public function createSubscription(array $data): array
    {
        try {
            $subscriptionData = [
                'customer' => $data['customer_id'],
                'items' => [['price' => $data['price_id']]],
                'payment_behavior' => $data['payment_behavior'] ?? 'default_incomplete',
                'metadata' => $data['metadata'] ?? [],
            ];

            $subscriptionData['expand'] = ['latest_invoice.payment_intent'];

            if (isset($data['trial_period_days'])) {
                $subscriptionData['trial_period_days'] = $data['trial_period_days'];
            }
            if (isset($data['default_payment_method'])) {
                $subscriptionData['default_payment_method'] = $data['default_payment_method'];
            }

            $subscription = $this->stripe->subscriptions->create($subscriptionData);
            
            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'client_secret' => $subscription->latest_invoice->payment_intent->client_secret ?? null,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription creation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }
    
    public function refundPayment(string $paymentId, ?int $amount = null, array $options = []): array
    {
        try {
            Log::info('Processing Stripe refund with dynamic credentials');

            $refundData = ['payment_intent' => $paymentId];
            
            if ($amount) {
                $refundData['amount'] = $amount;
            }

            if (isset($options['reason'])) {
                $refundData['reason'] = $options['reason'];
            }

            if (isset($options['metadata'])) {
                $refundData['metadata'] = $options['metadata'];
            }
            
            $refund = $this->stripe->refunds->create($refundData);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status,
                'amount' => $refund->amount,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }
    
    public function cancelSubscription(string $subscriptionId, array $options = []): array
    {
        try {
            $cancelData = [];
            
            if (isset($options['invoice_now'])) {
                $cancelData['invoice_now'] = $options['invoice_now'];
            }
            if (isset($options['prorate'])) {
                $cancelData['prorate'] = $options['prorate'];
            }

            $subscription = $this->stripe->subscriptions->cancel($subscriptionId, $cancelData);
            
            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription cancellation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    public function testConnection(): array
    {
        try {
            Log::info('Testing Stripe connection with dynamic credentials');

            $balance = $this->stripe->balance->retrieve();
            
            Log::info('Stripe connection test successful with dynamic credentials');
            
            return [
                'success' => true,
                'message' => 'Stripe connection successful',
                'data' => [
                    'available' => $balance->available[0]->amount ?? 0,
                    'pending' => $balance->pending[0]->amount ?? 0,
                    'currency' => $balance->available[0]->currency ?? 'usd',
                ]
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe connection test failed with dynamic credentials:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => 'stripe_api_error',
            ];
        }
    }

    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            
            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent retrieval failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    public function createSetupIntent(array $data): array
    {
        try {
            $setupIntentData = [
                'customer' => $data['customer_id'] ?? null,
                'payment_method_types' => $data['payment_method_types'] ?? ['card'],
                'metadata' => $data['metadata'] ?? [],
            ];

            $setupIntent = $this->stripe->setupIntents->create($setupIntentData);
            
            return [
                'success' => true,
                'setup_intent_id' => $setupIntent->id,
                'client_secret' => $setupIntent->client_secret,
                'status' => $setupIntent->status,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe setup intent creation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * Confirm payment (wrapper for confirmPaymentIntent with array data)
     */
    public function confirmPayment(array $data): array
    {
        $paymentIntentId = $data['payment_intent_id'] ?? $data['transaction_id'] ?? null;
        $paymentMethodId = $data['payment_method_id'] ?? $data['payment_method_data']['payment_method_id'] ?? null;

        if (!$paymentIntentId || !$paymentMethodId) {
            return [
                'success' => false,
                'error' => 'payment_intent_id/transaction_id and payment_method_id are required',
                'error_type' => 'InvalidArgumentException',
            ];
        }

        return $this->confirmPaymentIntent($paymentIntentId, $paymentMethodId);
    }

    /**
     * Retrieve payment (alias for retrievePaymentIntent)
     */
    public function retrievePayment(string $paymentId): array
    {
        return $this->retrievePaymentIntent($paymentId);
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createCheckoutSession(array $data): array
    {
        try {
            Log::info('Creating Stripe checkout session with dynamic credentials');

            $sessionData = [
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $data['currency'] ?? 'usd',
                        'product_data' => [
                            'name' => $data['description'] ?? 'Order Payment',
                        ],
                        'unit_amount' => $data['amount'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $data['success_url'],
                'cancel_url' => $data['cancel_url'],
                'metadata' => $data['metadata'] ?? [],
            ];

            if (isset($data['customer_email'])) {
                $sessionData['customer_email'] = $data['customer_email'];
            }

            $session = $this->stripe->checkout->sessions->create($sessionData);
            
            return [
                'success' => true,
                'session_id' => $session->id,
                'session_url' => $session->url,
                'url' => $session->url,
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session creation failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * Verify Stripe Checkout Session
     */
    public function verifyCheckoutSession(string $sessionId): array
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent'],
            ]);

            return [
                'success' => true,
                'payment_status' => $session->payment_status,
                'session_data' => [
                    'id' => $session->id,
                    'payment_status' => $session->payment_status,
                    'status' => $session->status,
                    'payment_intent' => $session->payment_intent->id ?? null,
                    'amount_total' => $session->amount_total,
                    'currency' => $session->currency,
                ],
            ];
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe checkout session verification failed:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ];
        }
    }

    /**
     * REMOVED: redirectUserToStripeCheckout method - This should be in controller
     * REMOVED: retrieveCheckoutSession method - Use verifyCheckoutSession instead
     * REMOVED: getOrCreateCustomer method - Use createCustomer instead
     */
}