<?php

namespace App\Livewire;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount()
    {
        View::share('title', 'Dashboard');
    }
    public function render()
    {
        return view('dashboard');
    }
}
