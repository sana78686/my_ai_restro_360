<?php
// app/Services/DynamicGatewayFactory.php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Tenant\PaymentGateway as TenantPaymentGateway;
use Illuminate\Support\Str;
use Stripe\Stripe;

class DynamicGatewayFactory
{
    protected array $gatewayInstances = [];
    
    public function make(TenantPaymentGateway $gatewayModel): PaymentGateway
    {
        $cacheKey = $gatewayModel->id;
        
        if (isset($this->gatewayInstances[$cacheKey])) {
            return $this->gatewayInstances[$cacheKey];
        }
        
        $provider = $gatewayModel->provider;
        $credentials = $gatewayModel->getCredentials();
        $config = $gatewayModel->config ?? [];
        
        // Dynamic class resolution
        $gatewayClass = $this->resolveGatewayClass($provider);
        
        if (!class_exists($gatewayClass)) {
            throw new \Exception("Gateway provider {$provider} is not supported.");
        }
        
        $gatewayInstance = new $gatewayClass($credentials, $config);
        
        $this->gatewayInstances[$cacheKey] = $gatewayInstance;
        
        return $gatewayInstance;
    }
    
    protected function resolveGatewayClass(string $provider): string
    {
        // Convert provider name to class name
        // stripe → StripeGateway, paypal → PayPalGateway, etc.
        $className = Str::studly($provider) . 'Gateway';
        
        return "App\\Services\\PaymentGateways\\{$className}";
    }
    
    /**
     * Get all available gateway providers from config
     */
    public function getAvailableProviders(): array
    {
        return array_keys(config('payment_gateways.gateways', []));
    }
    
    /**
     * Create a new gateway model instance for a provider
     */
    public function createGatewayModel(string $provider ): TenantPaymentGateway
    {
        $gatewayConfig = config("payment_gateways.gateways.{$provider}");

        if (!$gatewayConfig) {
            throw new \Exception("Gateway provider {$provider} not found in configuration.");
        }
        
        return new TenantPaymentGateway([
            'name' => $provider,
            'display_name' => $gatewayConfig['display_name'],
            'provider' => $provider,
            'category' => $gatewayConfig['category'],
            'supported_features' => $gatewayConfig['supported_features'] ?? [],
            'supported_currencies' => $gatewayConfig['supported_currencies'] ?? ['USD'],
            'status' => 'inactive',
        ]);
    }
}