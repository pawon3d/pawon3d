<?php

namespace App\Livewire\Landing;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Pesan extends Component
{
    public $caraPesan = 'whatsapp';

    #[Layout('components.layouts.landing.layout')]
    public function render()
    {
        return view('livewire.landing.pesan');
    }
}
