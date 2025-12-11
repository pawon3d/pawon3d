<?php

use App\Livewire\Transaction\TanggalSiapBeli;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(TanggalSiapBeli::class)
        ->assertStatus(200);
});
