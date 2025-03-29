<?php

namespace App\Providers;

use App\Models\StoreSetting;
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
        $storeSetting = StoreSetting::first();
        View::share('storeSetting', $storeSetting);
        Livewire::component('livewire-alert', \Jantinnerezo\LivewireAlert\LivewireAlert::class);
    }
}