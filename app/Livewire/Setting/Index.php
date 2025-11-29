<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        // Set the title for the settings page
        View::share('title', 'Pengaturan');
        View::share('mainTitle', 'Pengaturan');
    }

    public function render()
    {
        return view('livewire.setting.index');
    }
}
