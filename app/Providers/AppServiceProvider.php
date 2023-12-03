<?php

namespace App\Providers;

use App\Services\AggregatorService;
use App\Services\GuardianApiService;
use App\Services\Interfaces\NewsServiceInterface;
use App\Services\NewsApiService;
use App\Services\NyTimesApiService;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(AggregatorService::class)
            ->needs(NewsServiceInterface::class)
            ->give(function () {
                return [
                    new GuardianApiService(),
                    new NyTimesApiService(),
                    new NewsApiService(),
                ];
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
