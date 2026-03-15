<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        
        // Share app name with app.blade.php view
        View::composer('app', function ($view) {
            $view->with('appName', AppNameHelper::getAppName());
        });
    }
}
