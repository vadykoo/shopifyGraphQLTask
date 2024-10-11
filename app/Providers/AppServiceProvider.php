<?php

namespace App\Providers;

use App\Services\ShopifyService;
use App\Services\ShopifyServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShopifyServiceInterface::class, ShopifyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
