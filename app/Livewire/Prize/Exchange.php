<?php

namespace App\Livewire\Prize;

use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Exchange extends Component
{
    use LivewireAlert;

    public $code;

    public $message;

    public function mount()
    {
        View::share('title', 'Penukaran Hadiah');
    }

    public function exchange()
    {
        // $this->validate([
        //     'code' => 'required|exists:prizes,code',
        // ]);

        $prize = \App\Models\Prize::where('code', $this->code)->first();
        $this->reset('code');
        if (! $prize) {
            $this->alert('error', 'Kode tidak valid');

            return;
        }
        if (! $prize->is_get) {
            $this->alert('error', 'Kode ini belum dapat ditukar');

            return;
        }
        if ($prize->is_redeemed) {
            $this->alert('error', 'Kode ini sudah ditukar');

            return;
        }

        $prize->update(['is_redeem' => true, 'redeemed_at' => now()]);
        $this->message = 'Berhasil menukar hadiah 1 '.$prize->product->name;

        $this->alert('success', 'Hadiah berhasil ditukar', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'confirmButtonText' => 'Tutup',
            'text' => "$this->message",
        ]);
    }

    public function render()
    {
        return view('livewire.prize.exchange');
    }
}
