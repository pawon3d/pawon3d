<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class NoRole extends Component
{
    public function mount(): void
    {
        View::share('title', 'Menunggu Peran');
        View::share('mainTitle', 'Dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard.no-role');
    }
}
