<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Referral\ReferralRepositoryInterface::class,
            \App\Repositories\Referral\EloquentReferralRepository::class
        );
    }

    public function boot(): void {}
}
