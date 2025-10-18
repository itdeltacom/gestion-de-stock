<?php

namespace App\Providers;

use App\Services\ImageService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}