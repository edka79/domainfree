<?php

namespace App\Providers;

use App\Services\FavoriteService;
use App\Services\NobodyService;
use App\Services\SearchService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FavoriteService::class, function ($app) {
            return new FavoriteService();
        });
        $this->app->bind(SearchService::class, function ($app) {
            return new SearchService();
        });
        $this->app->bind(NobodyService::class, function ($app) {
            return new NobodyService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
