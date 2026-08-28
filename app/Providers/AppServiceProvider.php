<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Services\Payments\SandboxPaymentGateway;
use App\Services\Payments\UnsupportedPaymentGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, function (): PaymentGateway {
            return config('payments.driver') === 'sandbox'
                ? new SandboxPaymentGateway
                : new UnsupportedPaymentGateway;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('checkout', fn (Request $request): Limit => Limit::perMinute(10)->by((string) $request->user()?->id));
        RateLimiter::for('cart', fn (Request $request): Limit => Limit::perMinute(60)->by((string) $request->user()?->id));
        RateLimiter::for('favorites', fn (Request $request): Limit => Limit::perMinute(30)->by((string) $request->user()?->id));
    }
}
