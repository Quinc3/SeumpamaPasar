<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Checkout;
use App\Models\DigitalTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Policies\CheckoutPolicy;
use App\Policies\DigitalTransactionPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Checkout::class, CheckoutPolicy::class);
        Gate::policy(DigitalTransaction::class, DigitalTransactionPolicy::class);
    }
}
