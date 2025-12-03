<?php

namespace App\Providers;

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
        //
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
