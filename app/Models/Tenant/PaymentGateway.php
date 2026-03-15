<?php
// app/Models/Tenant/PaymentGateway.php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'provider',
        'is_active',
        'is_default',
        'priority',
        'credentials',
        'config',
        'webhook_config',
        'supported_features',
        'version',
        'category',
        'supported_currencies',
        'supported_countries',
        'status',
        'last_used_at',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'credentials' => 'array',
        'config' => 'array',
        'webhook_config' => 'array',
        'supported_features' => 'array',
        'supported_currencies' => 'array',
        'supported_countries' => 'array',
        'last_used_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected $table = 'payment_gate_ways';

    /**
     * Get gateway configuration from registry
     */
    public function getGatewayConfig(): ?array
    {
        return config("payment_gateways.gateways.{$this->provider}") ?? null;
    }

    /**
     * Check if gateway is properly configured
     */
    public function isConfigured(): bool
    {
        $gatewayConfig = $this->getGatewayConfig();

        // dd($gatewayConfig);
        if (!$gatewayConfig) {
            return false;
        }

        $credentialsSchema = $gatewayConfig['credentials_schema'] ?? [];
        // dd($credentialsSchema);
        $credentials = $this->credentials ?? [];
        // dd($credentials);
        foreach ($credentialsSchema as $field => $schema) {
            if (($schema['required'] ?? false)) {
                if (!isset($credentials[$field]) || empty($credentials[$field])) {
                    // dd('hello');
                    return false;
                }

                // Check if it's masked placeholder
                if ($credentials[$field] === '••••••••' || $credentials[$field] === '****') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get credentials with optional decryption
     */
    public function getCredentials(bool $decrypt = true): array
    {
        $credentials = $this->credentials ?? [];

        if (!$decrypt) {
            return $credentials;
        }

        $gatewayConfig = $this->getGatewayConfig();
        $credentialsSchema = $gatewayConfig['credentials_schema'] ?? [];

        foreach ($credentialsSchema as $field => $schema) {
            if (($schema['encrypted'] ?? false) && isset($credentials[$field])) {
                try {
                    // Skip decryption for masked values
                    if ($credentials[$field] !== '••••••••' && $credentials[$field] !== '****') {
                        $credentials[$field] = decrypt($credentials[$field]);
                    }
                } catch (\Exception $e) {
                    // Keep encrypted value if decryption fails
                }
            }
        }


        return $credentials;
    }

    /**
     * Check if gateway supports a specific feature
     */
    public function supports(string $feature): bool
    {
        $supportedFeatures = $this->supported_features ?? [];
        $gatewayConfig = $this->getGatewayConfig();

        return in_array($feature, $supportedFeatures) ||
               in_array($feature, $gatewayConfig['supported_features'] ?? []);
    }

    /**
     * Validate credentials against schema
     */
    public function validateCredentials(array $credentials): array
    {
        $errors = [];
        $gatewayConfig = $this->getGatewayConfig();
        $credentialsSchema = $gatewayConfig['credentials_schema'] ?? [];

        foreach ($credentialsSchema as $field => $schema) {
            if (($schema['required'] ?? false) && empty($credentials[$field])) {
                $errors[$field] = "The {$schema['label']} field is required.";
            }
        }

        return $errors;
    }

    /**
     * Get form fields for gateway configuration
     */
    public function getFormFields(): array
    {
        $gatewayConfig = $this->getGatewayConfig();
        return $gatewayConfig['credentials_schema'] ?? [];
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return $this->supported_currencies ??
               $this->getGatewayConfig()['supported_currencies'] ??
               ['USD'];
    }

    /**
     * Scope to active and configured gateways
     */
    public function scopeActiveAndConfigured($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'active');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by supported feature
     */
    public function scopeWithFeature($query, string $feature)
    {
        return $query->whereJsonContains('supported_features', $feature);
    }

    /**
     * Mark gateway as used
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if any gateway is active
     */
    public static function active(): bool
    {
        return static::where('is_active', true)->exists();
    }

    /**
     * Verify gateway configuration
     */
    public function verify(): bool
    {
        // Implement your gateway verification logic here
        // Test API connection, validate credentials, etc.

        $isValid = $this->testConnection();

        if ($isValid) {
            $this->update([
                'verified_at' => now(),
                'status' => 'active'
            ]);
        } else {
            $this->update([
                'verified_at' => null,
                'status' => 'suspended'
            ]);
        }

        return $isValid;
    }

    /**
     * Test connection to gateway API
     */
    protected function testConnection(): bool
    {
        // Implement actual API connection test based on provider
        try {
            // Example for Stripe:
            if ($this->provider === 'stripe') {
                $credentials = $this->getCredentials();
                // Test Stripe API connection here
                return !empty($credentials['secret_key']);
            }

            // Add other providers...

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get default gateway
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Set as default gateway
     */
    public function setAsDefault(): void
    {
        // Remove default from other gateways
        static::where('id', '!=', $this->id)->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }












}
