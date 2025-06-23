<?php

namespace App\Livewire\Setting;

use Illuminate\Support\Facades\View;
use Livewire\Component;

class UserManual extends Component
{
    public function mount()
    {
        View::share('mainTitle', 'Pengaturan');
        View::share('title', 'Manual Pengguna');
    }
    public function render()
    {
        return view('livewire.setting.user-manual');
    }
}
