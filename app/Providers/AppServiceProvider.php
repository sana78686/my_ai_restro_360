<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Services\DynamicGatewayFactory;
use Illuminate\Support\ServiceProvider;
use App\Services\TenantPaymentGatewayManager;
use App\Services\PaymentGateways\StripeGateway;
use App\Helpers\AppNameHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantPaymentGatewayManager::class, function ($app) {
            return new TenantPaymentGatewayManager();
        });

        // stripe service provider registration
        $this->app->singleton(StripeGateway::class, function ($app) {
            $gatewayManager = $app->make(TenantPaymentGatewayManager::class);
            $stripeGateway = $gatewayManager->getGateway('stripe');

            return $stripeGateway;
        });
        // $this->app->register(StripeGateway::class);

        // Register the gateway factory
        $this->app->singleton('gateway.factory', function ($app) {
            return new DynamicGatewayFactory();
        });
        
        // Or you can also bind it with the class name
        $this->app->bind( DynamicGatewayFactory::class, function ($app) {
            return new  DynamicGatewayFactory();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $caBundle = env('AWS_CA_BUNDLE');
        $caBundlePath = '';
        if (is_string($caBundle) && $caBundle !== '' && is_readable($caBundle)) {
            $caBundlePath = $caBundle;
        } elseif (is_readable(storage_path('app/aws-cacert.pem'))) {
            $caBundlePath = storage_path('app/aws-cacert.pem');
        }
        if ($caBundlePath !== '') {
            /** @see https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#configuring-a-custom-ca-bundle */
            putenv('AWS_CA_BUNDLE='.$caBundlePath);
        }

        // Share app name with app.blade.php view + optional tenant theme logging for debugging
        View::composer('app', function ($view) {
            $view->with('appName', AppNameHelper::getAppName());

            $shouldLog = config('app.debug')
                || filter_var(env('LOG_TENANT_THEME', false), FILTER_VALIDATE_BOOLEAN);

            if (! $shouldLog || ! function_exists('tenancy')) {
                return;
            }

            if (tenancy()->initialized && tenant()) {
                /** @var \App\Models\Tenant $t */
                $t = tenant();
                $resolved = strtolower(trim((string) ($t->theme ?? 'classic'))) ?: 'classic';

                Log::info('[Tenant SPA] Resolved storefront theme', [
                    'host' => request()->getHost(),
                    'tenant_id' => $t->getTenantKey(),
                    'central_theme_column' => $t->theme,
                    'injected_WINDOW_TENANT_THEME' => in_array($resolved, ['classic', 'modern', 'minimal', 'blaze'], true)
                        ? $resolved
                        : 'classic (fallback — invalid slug)',
                    'main_domain_config' => config('app.main_domain'),
                ]);

                return;
            }

            if (filter_var(env('LOG_CENTRAL_SPA_THEME', false), FILTER_VALIDATE_BOOLEAN)) {
                Log::debug('[Central SPA] No tenant context — main-domain router branch', [
                    'host' => request()->getHost(),
                ]);
            }
        });
    }
}
