<?php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use App\Services\TenantPaymentGatewayManager;
use Illuminate\Http\Request;

class TestController extends Controller
{
    protected $paymentManager;

    public function __construct(TenantPaymentGatewayManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Get test setup data
     */
    public function getTestSetup()
    {
        try {
            $activeGateways = $this->paymentManager->getActiveGateways();
            
            return response()->json([
                'success' => true,
                'active_gateways' => $activeGateways,
                'test_cards' => [
                    'stripe' => [
                        'success' => '4242424242424242',
                        'decline' => '4000000000000002',
                        'authentication' => '4000002500003155',
                    ]
                ],
                'test_amounts' => [
                    'minimum' => 0.50,
                    'recommended' => 1.00,
                    'maximum' => 100.00,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testCreateCustomer()
    {
        try {
            $gateway = $this->paymentManager->getGateway('stripe');
            
            $customer = $gateway->createCustomer([
                'email' => 'testcustomer@example.com',
                'name' => 'Test Customer',
                'metadata' => [
                    'tenant_id' => tenant('id'),
                    'test' => true,
                    'created_at' => now()->toISOString(),
                ]
            ]);
            
            if ($customer['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test customer created successfully in Stripe',
                    'customer_id' => $customer['customer_id'],
                    'customer_data' => $customer['data'],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $customer['error'],
                    'error_type' => $customer['error_type'],
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testProcessPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5|max:100',
            'gateway' => 'sometimes|string',
        ]);

        try {
            $gateway = $this->paymentManager->getGateway($request->gateway);
            
            $payment = $gateway->createPayment([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'description' => 'Test Payment - Tenant System',
                'metadata' => [
                    'tenant_id' => tenant('id'),
                    'test' => true,
                    'purpose' => 'system_test',
                ],
            ]);

            if ($payment['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test payment intent created successfully',
                    'payment_intent_id' => $payment['payment_intent_id'],
                    'client_secret' => $payment['client_secret'],
                    'status' => $payment['status'],
                    'requires_action' => !empty($payment['next_action']),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $payment['error'],
                    'error_type' => $payment['error_type'],
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}