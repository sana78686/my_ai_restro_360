<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\TenantPaymentGatewayManager;

class CheckoutController extends Controller
{
    protected TenantPaymentGatewayManager $paymentManager;

    public function __construct(TenantPaymentGatewayManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Create a test payment intent for checkout - UPDATED FOR DYNAMIC GATEWAYS
     */
    public function createTestPayment(Request $request): JsonResponse
    {
        Log::info('=== createTestPayment START ===');
        Log::info('Request data:', $request->all());
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'sometimes|string|size:3',
            'gateway' => 'required|string', // Made required
        ]);

        try {
            // Get gateway with dynamic credentials from database
            $gatewayName = $request->gateway;
            Log::info('Getting gateway for:', ['gateway' => $gatewayName]);
            
            $gateway = $this->paymentManager->getGateway($gatewayName);
            Log::info('Gateway obtained successfully');

            // DEBUG: Check what credentials are being used
            if (method_exists($gateway, 'getCredentials')) {
                $usedCredentials = $gateway->getCredentials();
                Log::info('Credentials being used by gateway:', array_keys($usedCredentials));
            }

            $paymentData = [
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => $request->currency ?? 'usd',
                'description' => 'Test payment - Restaurant Order',
                'metadata' => [
                    'test' => true,
                    'order_type' => 'test_checkout',
                    'customer_ip' => $request->ip(),
                    'tenant_id' => tenant('id'),
                    'gateway' => $gatewayName,
                ],
            ];

            Log::info('Creating payment with data:', $paymentData);
            
            // Use generic createPayment method that works with all gateways
            $result = $gateway->createPayment($paymentData);
            
            Log::info('Payment creation result:', $result);

            if ($result['success']) {
                // Build dynamic response based on gateway type
                $response = [
                    'success' => true,
                    'gateway' => $gatewayName,
                    'status' => $result['status'],
                    'message' => 'Payment created successfully',
                ];

                // Add gateway-specific response fields
                if ($gatewayName === 'stripe') {
                    $response['payment_intent'] = $result['payment_intent_id'];
                    $response['client_secret'] = $result['client_secret'];
                } elseif ($gatewayName === 'paypal') {
                    $response['order_id'] = $result['order_id'];
                    $response['approval_url'] = $result['approval_url'];
                } elseif ($gatewayName === 'razorpay') {
                    $response['order_id'] = $result['order_id'];
                    $response['amount'] = $result['amount'];
                } else {
                    // Generic fallback for other gateways
                    $response['transaction_id'] = $result['transaction_id'] ?? $result['payment_intent_id'] ?? $result['order_id'] ?? null;
                    $response['client_secret'] = $result['client_secret'] ?? null;
                }
                
                Log::info('Payment created successfully:', $response);
                Log::info('=== createTestPayment END (SUCCESS) ===');
                
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Payment creation failed',
                    'error_type' => $result['error_type'] ?? 'payment_error',
                    'gateway' => $gatewayName,
                ];
                
                Log::error('Payment creation failed:', $response);
                Log::info('=== createTestPayment END (FAILED) ===');
                
                return response()->json($response, 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment setup failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway ?? 'unknown'
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Payment setup failed: ' . $e->getMessage(),
                'gateway' => $request->gateway ?? 'unknown',
            ];
            
            Log::info('=== createTestPayment END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }

    /**
     * Process payment with payment details - UPDATED FOR DYNAMIC GATEWAYS
     */
    public function processPayment(Request $request): JsonResponse
    {
        Log::info('=== processPayment START ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'transaction_id' => 'required|string', // Generic field name
            'payment_method_data' => 'required|array', // Generic payment data
            'gateway' => 'required|string',
        ]);

        try {
            $gatewayName = $request->gateway;
            $gateway = $this->paymentManager->getGateway($gatewayName);

            $paymentData = [
                'transaction_id' => $request->transaction_id,
                'payment_method_data' => $request->payment_method_data,
                'gateway' => $gatewayName,
            ];

            Log::info('Processing payment with data:', $paymentData);

            // Use generic confirmPayment method
            $result = $gateway->confirmPayment($paymentData);
            
            Log::info('Payment processing result:', $result);

            if ($result['success']) {
                $status = $result['status'];
                
                if (in_array($status, ['succeeded', 'completed', 'approved', 'processing', 'captured'])) {
                    $response = [
                        'success' => true,
                        'message' => 'Payment completed successfully!',
                        'payment_status' => $status,
                        'transaction_id' => $result['transaction_id'] ?? $request->transaction_id,
                        'order_reference' => 'TEST-' . time(),
                        'gateway' => $gatewayName,
                    ];

                    // Add gateway-specific success data
                    if (isset($result['capture_id'])) {
                        $response['capture_id'] = $result['capture_id'];
                    }
                    if (isset($result['payment_id'])) {
                        $response['payment_id'] = $result['payment_id'];
                    }
                } else if ($status === 'requires_action') {
                    $response = [
                        'success' => true,
                        'message' => 'Additional action required',
                        'payment_status' => $status,
                        'client_secret' => $result['client_secret'] ?? null,
                        'next_action' => $result['next_action'] ?? null,
                        'gateway' => $gatewayName,
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => "Payment status: {$status}",
                        'payment_status' => $status,
                        'gateway' => $gatewayName,
                    ];
                }
                
                Log::info('Payment processed:', $response);
                Log::info('=== processPayment END ===');
                
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Payment processing failed',
                    'gateway' => $gatewayName,
                ];
                
                Log::error('Payment processing failed:', $response);
                Log::info('=== processPayment END (FAILED) ===');
                
                return response()->json($response, 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment processing failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'gateway' => $request->gateway,
            ];
            
            Log::info('=== processPayment END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }

    /**
     * Confirm and process test payment - UPDATED FOR DYNAMIC GATEWAYS
     */
    public function confirmTestPayment(Request $request): JsonResponse
    {
        Log::info('=== confirmTestPayment START ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'transaction_id' => 'required|string', // Generic field name
            'gateway' => 'required|string',
        ]);

        try {
            $gatewayName = $request->gateway;
            $gateway = $this->paymentManager->getGateway($gatewayName);

            // Retrieve payment status using generic method
            $result = $gateway->retrievePayment($request->transaction_id);
            
            Log::info('Payment retrieval result:', $result);

            if ($result['success']) {
                $status = $result['status'];
                
                if (in_array($status, ['succeeded', 'completed', 'approved', 'processing', 'captured'])) {
                    $response = [
                        'success' => true,
                        'message' => 'Test payment completed successfully!',
                        'payment_status' => $status,
                        'transaction_id' => $request->transaction_id,
                        'order_reference' => 'TEST-' . time(),
                        'amount' => $result['amount'] ?? $request->amount ?? 0,
                        'currency' => $result['currency'] ?? 'usd',
                        'gateway' => $gatewayName,
                    ];

                    // Add gateway-specific data
                    if (isset($result['capture_id'])) {
                        $response['capture_id'] = $result['capture_id'];
                    }
                    if (isset($result['payment_id'])) {
                        $response['payment_id'] = $result['payment_id'];
                    }
                    
                    Log::info('Payment confirmed successfully:', $response);
                    Log::info('=== confirmTestPayment END (SUCCESS) ===');
                    
                    return response()->json($response);
                } else {
                    $response = [
                        'success' => false,
                        'message' => "Payment status: {$status}",
                        'payment_status' => $status,
                        'gateway' => $gatewayName,
                    ];
                    
                    Log::warning('Payment not in success state:', $response);
                    Log::info('=== confirmTestPayment END (FAILED) ===');
                    
                    return response()->json($response, 400);
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to retrieve payment status',
                    'gateway' => $gatewayName,
                ];
                
                Log::error('Payment retrieval failed:', $response);
                Log::info('=== confirmTestPayment END (RETRIEVAL_FAILED) ===');
                
                return response()->json($response, 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Payment confirmation failed: ' . $e->getMessage(),
                'gateway' => $request->gateway,
            ];
            
            Log::info('=== confirmTestPayment END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }

    /**
     * Get available payment methods for checkout
     */
    public function getPaymentMethods(): JsonResponse
    {
        Log::info('=== getPaymentMethods START ===');
        
        try {
            $activeGateways = $this->paymentManager->getActiveGateways();
            
            Log::info('Active gateways found:', $activeGateways);
            
            $response = [
                'success' => true,
                'payment_methods' => $activeGateways,
            ];
            
            Log::info('=== getPaymentMethods END (SUCCESS) ===');
            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error getting payment methods:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $response = [
                'success' => false,
                'message' => 'No payment methods available',
                'error' => $e->getMessage(),
            ];
            
            Log::info('=== getPaymentMethods END (ERROR) ===');
            return response()->json($response, 400);
        }
    }

    /**
     * Refund test payment (for cleanup) - UPDATED FOR DYNAMIC GATEWAYS
     */
    public function refundTestPayment(Request $request): JsonResponse
    {
        Log::info('=== refundTestPayment START ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'transaction_id' => 'required|string', // Generic field name
            'gateway' => 'required|string',
        ]);

        try {
            $gatewayName = $request->gateway;
            $gateway = $this->paymentManager->getGateway($gatewayName);

            $result = $gateway->refundPayment($request->transaction_id);
            
            Log::info('Refund result:', $result);

            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => 'Test payment refunded successfully',
                    'refund_id' => $result['refund_id'] ?? $result['transaction_id'] ?? null,
                    'refund_status' => $result['status'],
                    'gateway' => $gatewayName,
                ];
                
                Log::info('Refund successful:', $response);
                Log::info('=== refundTestPayment END (SUCCESS) ===');
                
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Refund failed',
                    'gateway' => $gatewayName,
                ];
                
                Log::error('Refund failed:', $response);
                Log::info('=== refundTestPayment END (FAILED) ===');
                
                return response()->json($response, 400);
            }

        } catch (\Exception $e) {
            Log::error('Refund failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage(),
                'gateway' => $request->gateway,
            ];
            
            Log::info('=== refundTestPayment END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }

    /**
     * Redirect user to gateway checkout (for gateways that support redirect flow)
     */
    public function redirectToGatewayCheckout(Request $request): JsonResponse
    {
        // dd($request->all());
        Log::info('=== redirectToGatewayCheckout START ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'amount' => 'required|numeric|min:1', // Minimum 50 cents ($0.50)
            'currency' => 'sometimes|string|size:3',
            'gateway' => 'sometimes|string', // Made optional, will default based on route
            'success_url' => 'required|string', // Changed from 'url' to allow placeholders
            'cancel_url' => 'required|string', // Changed from 'url' to allow placeholders
            'order_id' => 'sometimes', // Allow any type, will convert to string
            'customer_email' => 'sometimes|email',
        ]);
        
        // Validate URLs manually to allow placeholders
        $successUrl = $request->success_url;
        $cancelUrl = $request->cancel_url;
        
        // Replace placeholder temporarily for validation
        $successUrlForValidation = str_replace('{CHECKOUT_SESSION_ID}', 'test', $successUrl);
        $cancelUrlForValidation = str_replace('{CHECKOUT_SESSION_ID}', 'test', $cancelUrl);
        
        if (!filter_var($successUrlForValidation, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid success URL format',
                'errors' => ['success_url' => ['The success URL must be a valid URL']]
            ], 422);
        }
        
        if (!filter_var($cancelUrlForValidation, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cancel URL format',
                'errors' => ['cancel_url' => ['The cancel URL must be a valid URL']]
            ], 422);
        }

        try {
            // Default to 'stripe' if gateway not provided (for stripe-specific route)
            $gatewayName = $request->gateway ?? 'stripe';
            $gateway = $this->paymentManager->getGateway($gatewayName);

            // Amount is already in cents from frontend, don't convert again
            $amount = is_numeric($request->amount) ? (int)$request->amount : $request->amount;
            
            // Convert order_id to string if it's a number
            $orderId = $request->order_id ? (string)$request->order_id : null;

            $checkoutData = [
                'amount' => $amount, // Already in cents from frontend
                'currency' => $request->currency ?? 'usd',
                'success_url' => $request->success_url,
                'cancel_url' => $request->cancel_url,
                'order_id' => $orderId,
                'customer_email' => $request->customer_email,
                'customer_name' => $request->customer_name,
                'metadata' => [
                    'tenant_id' => tenant('id'),
                    'order_id' => $request->order_id,
                ],
            ];

            Log::info('Creating checkout session with data:', $checkoutData);

            $result = $gateway->createCheckoutSession($checkoutData);
            
            Log::info('Checkout session result:', $result);

            if ($result['success']) {
                $response = [
                    'success' => true,
                    'session_url' => $result['session_url'] ?? $result['approval_url'] ?? $result['url'] ?? null,
                    'session_id' => $result['session_id'] ?? $result['order_id'] ?? null,
                    'gateway' => $gatewayName,
                ];
                
                Log::info('Checkout session created:', $response);
                Log::info('=== redirectToGatewayCheckout END (SUCCESS) ===');
                
                return response()->json($response);
            } else {
                $response = [
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to create checkout session',
                    'gateway' => $gatewayName,
                ];
                
                Log::error('Checkout session creation failed:', $response);
                Log::info('=== redirectToGatewayCheckout END (FAILED) ===');
                
                return response()->json($response, 400);
            }

        } catch (\Exception $e) {
            Log::error('Checkout session creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Failed to create checkout session: ' . $e->getMessage(),
                'gateway' => $request->gateway,
            ];
            
            Log::info('=== redirectToGatewayCheckout END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }

    /**
     * Verify gateway checkout order
     */
    public function verifyGatewayCheckout(Request $request): JsonResponse
    {
        Log::info('=== verifyGatewayCheckout START ===');
        Log::info('Request data:', $request->all());
        
        $request->validate([
            'session_id' => 'required|string',
            'gateway' => 'sometimes|string', // Optional, defaults to 'stripe'
            'order_id' => 'sometimes|integer', // Order ID to update
        ]);

        try {
            // Default to 'stripe' if gateway not provided
            $gatewayName = $request->gateway ?? 'stripe';
            $gateway = $this->paymentManager->getGateway($gatewayName);

            $result = $gateway->verifyCheckoutSession($request->session_id);
            
            Log::info('Checkout verification result:', $result);

            if ($result['success'] && $result['payment_status'] === 'paid') {
                // Update order if order_id is provided
                $orderUpdated = false;
                if ($request->order_id) {
                    $orderUpdated = $this->updateOrderPaymentStatus(
                        $request->order_id,
                        $result['payment_intent'] ?? $result['session_data']['payment_intent'] ?? null,
                        $result['session_data'] ?? $result
                    );
                }
                
                $response = [
                    'success' => true,
                    'session' => $result['session_data'] ?? $result,
                    'gateway' => $gatewayName,
                    'order_updated' => $orderUpdated,
                ];
                
                Log::info('Payment verified successfully:', $response);
                Log::info('=== verifyGatewayCheckout END (SUCCESS) ===');
                
                return response()->json($response);
            }
            
            $response = [
                'success' => false,
                'message' => $result['error'] ?? 'Payment not completed or verification failed',
                'gateway' => $gatewayName,
            ];
            
            Log::warning('Payment verification failed:', $response);
            Log::info('=== verifyGatewayCheckout END (FAILED) ===');
            
            return response()->json($response, 400);
            
        } catch (\Exception $e) {
            Log::error('Payment verification failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'gateway' => $request->gateway
            ]);
            
            $response = [
                'success' => false,
                'message' => 'Failed to verify payment: ' . $e->getMessage(),
                'gateway' => $request->gateway,
            ];
            
            Log::info('=== verifyGatewayCheckout END (EXCEPTION) ===');
            return response()->json($response, 500);
        }
    }
}