<?php

namespace App\Providers;

use App\Console\Commands\BackfillPointsUsage;
use App\Models\StoreProfile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackfillPointsUsage::class,
            ]);
        }

        if ($envPath = env('PUBLIC_PATH')) {
            $this->app->instance('path.public', $envPath);
        } else {
            $this->app->bind('path.public', function () {
                return base_path('public');
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('store_profiles')) {
            View::share('storeProfile', StoreProfile::first());
        }

        Livewire::component('livewire-alert', \Jantinnerezo\LivewireAlert\LivewireAlert::class);
    }
}
