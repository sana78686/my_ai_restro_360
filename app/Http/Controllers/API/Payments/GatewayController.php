<?php
// app/Http/Controllers/API/Payments/GatewayController.php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GatewayController extends Controller
{
    /**
     * Get all available gateways with current status
     */
    public function index()
    {
        // dd(123);
        $availableGateways = collect(config('payment_gateways.available_gateways', []))
            ->map(function ($gatewayName) {
                $config = config("payment_gateways.gateways.{$gatewayName}");
                $model = PaymentGateway::where('name', $gatewayName)->first();
                
                return [
                    'name' => $gatewayName,
                    'display_name' => $config['display_name'],
                    'category' => $config['category'],
                    'description' => $config['description'] ?? '',
                    'is_configured' => $model && $model->isConfigured(),
                    'is_active' => $model ? $model->is_active : false,
                    'is_default' => $model ? $model->is_default : false,
                    'status' => $model ? $model->status : 'not_configured',
                    'supported_features' => $config['supported_features'] ?? [],
                    'supported_currencies' => $config['supported_currencies'] ?? ['USD'],
                ];
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $availableGateways
        ]);
    }
    
    /**
     * Get gateway configuration schema for Vue form
     */
    public function getGatewaySchema($gatewayName)
    {
        $availableGateways = config('payment_gateways.available_gateways', []);
        if (!in_array($gatewayName, $availableGateways)) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not available'
            ], 404);
        }
        
        $gatewayConfig = config("payment_gateways.gateways.{$gatewayName}");
        $gatewayModel = PaymentGateway::where('name', $gatewayName)->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'gateway_name' => $gatewayName,
                'display_name' => $gatewayConfig['display_name'],
                'credentials_schema' => $gatewayConfig['credentials_schema'] ?? [],
                'config_schema' => $gatewayConfig['config_schema'] ?? [],
                'current_config' => $gatewayModel ? [
                    'credentials' => $this->getSafeCredentials($gatewayModel->credentials ?? [], $gatewayConfig),
                    'config' => $gatewayModel->config ?? [],
                    'is_active' => $gatewayModel->is_active,
                    'is_default' => $gatewayModel->is_default,
                ] : null
            ]
        ]);
    }
    
    /**
     * Get current gateway configuration
     */
    public function getGatewayConfig($gatewayName)
    {
        $gateway = PaymentGateway::where('name', $gatewayName)->first();
        
        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not configured'
            ], 404);
        }
        
        $gatewayConfig = config("payment_gateways.gateways.{$gatewayName}");
        
        return response()->json([
            'success' => true,
            'data' => [
                'credentials' => $this->getSafeCredentials($gateway->credentials ?? [], $gatewayConfig),
                'config' => $gateway->config ?? [],
                'is_active' => $gateway->is_active,
                'is_default' => $gateway->is_default,
                'status' => $gateway->status,
            ]
        ]);
    }
    
    /**
     * Store gateway credentials
     */
    public function storeCredentials(Request $request, $gatewayName)
    {
        // dd(123);
        $availableGateways = config('payment_gateways.available_gateways', []);
        if (!in_array($gatewayName, $availableGateways)) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not available'
            ], 404);
        }
        
        $gatewayConfig = config("payment_gateways.gateways.{$gatewayName}");
        $validationRules = $this->getValidationRules($gatewayConfig);
        
        $validated = $request->validate($validationRules);
        $encryptedCredentials = $this->encryptCredentials($validated, $gatewayConfig);
        
        $gateway = PaymentGateway::updateOrCreate(
            ['name' => $gatewayName],
            [
                'display_name' => $gatewayConfig['display_name'],
                'provider' => $gatewayName,
                'credentials' => $encryptedCredentials,
                'config' => $this->extractConfig($validated, $gatewayConfig),
                'is_active' => false, // Don't auto-activate - must test connection first
                'category' => $gatewayConfig['category'],
                'supported_features' => $gatewayConfig['supported_features'] ?? [],
                'supported_currencies' => $gatewayConfig['supported_currencies'] ?? ['USD'],
                'status' => 'configured', // Set status to configured, not active
            ]
        );
        
        // Set as default if first gateway
        if (PaymentGateway::where('is_default', true)->doesntExist()) {
            $gateway->update(['is_default' => true]);
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$gatewayConfig['display_name']} configured successfully!",
            'data' => $gateway
        ]);
    }
    
    /**
     * Test gateway connection
     */
    public function testConnection(Request $request, $gatewayName)
    {
        $gateway = PaymentGateway::where('name', $gatewayName)->first();
        
        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway not configured'
            ], 404);
        }
        
        try {
            $gatewayInstance = app('gateway.factory')->make($gateway);
            $testResult = $gatewayInstance->testConnection();
            
            if ($testResult['success']) {
                // Activate gateway only if test passes
                $gateway->update([
                    'is_active' => true,
                    'status' => 'active', 
                    'verified_at' => now()
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Connection test successful! Gateway has been activated.'
                ]);
            } else {
                // Ensure gateway is deactivated if test fails
                $gateway->update([
                    'is_active' => false,
                    'status' => 'configured'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $testResult['error'] ?? 'Connection test failed'
                ], 400);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Toggle gateway active status
     */
    public function toggleActive($gatewayName)
    {
        $gateway = PaymentGateway::where('name', $gatewayName)->firstOrFail();
        $gateway->update(['is_active' => !$gateway->is_active]);
        
        $status = $gateway->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Gateway {$status} successfully."
        ]);
    }
    
    /**
     * Set default gateway
     */
    public function setDefault($gatewayName)
    {
        PaymentGateway::query()->update(['is_default' => false]);
        PaymentGateway::where('name', $gatewayName)->update(['is_default' => true]);
        
        return response()->json([
            'success' => true,
            'message' => "Default gateway set to {$gatewayName}."
        ]);
    }
    
    /**
     * Get safe credentials for display (hide sensitive data)
     */
    protected function getSafeCredentials(array $credentials, array $gatewayConfig): array
    {
        $safeCredentials = [];
        $credentialsSchema = $gatewayConfig['credentials_schema'] ?? [];
        
        foreach ($credentials as $field => $value) {
            if (($credentialsSchema[$field]['type'] ?? '') === 'password' && !empty($value)) {
                $safeCredentials[$field] = '••••••••'; // Mask passwords
            } else {
                $safeCredentials[$field] = $value;
            }
        }
        
        return $safeCredentials;
    }
    
    // ... keep the helper methods from previous implementation
/**
 * Build validation rules dynamically from the gateway configuration schema
 */
protected function getValidationRules(array $gatewayConfig): array
{
    $rules = [];

    // Credentials schema
    if (!empty($gatewayConfig['credentials_schema'])) {
        foreach ($gatewayConfig['credentials_schema'] as $field => $schema) {
            $fieldRules = [];

            // Required rule
            if (!empty($schema['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Field type rule
            switch ($schema['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                default:
                    $fieldRules[] = 'string';
            }

            $rules[$field] = implode('|', $fieldRules);
        }
    }

    // Additional config schema
    if (!empty($gatewayConfig['config_schema'])) {
        foreach ($gatewayConfig['config_schema'] as $field => $schema) {
            $fieldRules = [];

            if (!empty($schema['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($schema['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                default:
                    $fieldRules[] = 'string';
            }

            $rules[$field] = implode('|', $fieldRules);
        }
    }

    return $rules;
}
/**
 * Encrypt credentials based on schema
 */
protected function encryptCredentials(array $credentials, array $gatewayConfig): array
{
    $processed = [];
    $credentialsSchema = $gatewayConfig['credentials_schema'] ?? [];

    foreach ($credentialsSchema as $field => $schema) {
        if (isset($credentials[$field]) && !empty($credentials[$field])) {
            if (($schema['encrypted'] ?? false)) {
                $processed[$field] = Crypt::encrypt($credentials[$field]);
            } else {
                $processed[$field] = $credentials[$field]; // ← This line was missing!
            }
        }
    }

    return $processed; 
}  
/**
 * Extract additional config from validated data
 */
protected function extractConfig(array $validatedData, array $gatewayConfig): array
{
    $config = [];
    $configSchema = $gatewayConfig['config_schema'] ?? [];
    foreach ($configSchema as $field => $schema) {
        if (isset($validatedData[$field])) {
            $config[$field] = $validatedData[$field];
        }
    }
    return $config;
}
}