<?php

namespace App\Services;

use InvalidArgumentException;
use App\Services\PaymentGateways\StripeGateway;
use App\Contracts\PaymentGateway;
use App\Models\Tenant\PaymentGateway as TenantPaymentGateway;

class TenantPaymentGatewayManager
{
    protected array $gatewayInstances = [];
    
    public function getGateway(?string $gatewayName = null): PaymentGateway
    {
        // If no gateway specified, get default active gateway
        if (!$gatewayName) {
            $gatewayName = $this->getDefaultGatewayName();
        }
        
        // Return cached instance if exists
        if (isset($this->gatewayInstances[$gatewayName])) {
            return $this->gatewayInstances[$gatewayName];
        }
        
        // Get gateway configuration from tenant database - FIXED FIELD NAME
        $tenantGateway = TenantPaymentGateway::where('is_active', true)
            ->where('name', $gatewayName) // Changed from 'gateway_name' to 'name'
            ->first();
            // DD($tenantGateway);
        if (!$tenantGateway) {
            // dd('here');
            throw new InvalidArgumentException("Gateway [{$gatewayName}] is not active or not configured for this tenant.");
        }
        
        if (!$tenantGateway->isConfigured()) {
            // dd('here');
            throw new InvalidArgumentException("Gateway [{$gatewayName}] is not properly configured for this tenant.");
        }
        
        // Create gateway instance with tenant-specific credentials
        $gatewayInstance = $this->createGatewayInstance(
            $gatewayName, 
            $tenantGateway->getCredentials()
        );
        
        // Cache the instance
        $this->gatewayInstances[$gatewayName] = $gatewayInstance;
        // dd('hrer');
        return $gatewayInstance;
    }
    
    protected function createGatewayInstance(string $gatewayName, array $credentials): PaymentGateway
    {
        return match($gatewayName) {
            'stripe' => new StripeGateway($credentials),
            // 'paypal' => new PayPalGateway($credentials),
            // 'razorpay' => new RazorpayGateway($credentials),
            default => throw new InvalidArgumentException("Unsupported gateway: {$gatewayName}"),
        };
    }
    
    public function getDefaultGatewayName(): string
    {
        $defaultGateway = TenantPaymentGateway::where('is_active', true)
            ->where('is_default', true)
            ->first();
            
        if ($defaultGateway) {
            return $defaultGateway->name; // Changed from 'gateway_name' to 'name'
        }
        
        // Fallback to first active gateway
        $firstActive = TenantPaymentGateway::where('is_active', true)
            ->where('status', 'active')
            ->orderBy('priority')
            ->first();
            
        if ($firstActive) {
            return $firstActive->name; // Changed from 'gateway_name' to 'name'
        }
        
        throw new InvalidArgumentException("No active payment gateways configured for this tenant.");
    }
    
    public function getActiveGateways(): array
    {
        return TenantPaymentGateway::where('is_active', true)
            ->where('status', 'active')
            ->get()
            // ->filter(function ($gateway) {
            //     return $gateway->isConfigured();
            // })
            ->map(function ($gateway) {
                return [
                    'name' => $gateway->name, // Changed from 'gateway_name' to 'name'
                    'display_name' => $gateway->display_name, // Use direct field instead of method
                    'is_active' => $gateway->is_active,
                    'is_default' => $gateway->is_default,
                    'category' => $gateway->category,
                ];
            })
            ->toArray();
    }
    
    public function isGatewayActive(string $gatewayName): bool
    {
        $gateway = TenantPaymentGateway::where('name', $gatewayName) // Changed from 'gateway_name' to 'name'
            ->where('is_active', true)
            ->first();
            
        return $gateway && $gateway->isConfigured();
    }

  
}