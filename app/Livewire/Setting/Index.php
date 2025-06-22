<?php

namespace App\Livewire\Setting;

use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        // Set the title for the settings page
        \Illuminate\Support\Facades\View::share('title', 'Pengaturan');
    }
    public function render()
    {
        return view('livewire.setting.index');
    }
}
