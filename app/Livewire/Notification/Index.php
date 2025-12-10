<?php

namespace App\Livewire\Notification;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Index extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;

    public string $filter = '';

    public function mount()
    {
        View::share('title', 'Notifikasi');
        View::share('mainTitle', 'Notifikasi');

        $user = Auth::user();
        if ($user->hasAnyPermission([
            'inventori.persediaan.kelola',
            'inventori.laporan.kelola',
            'inventori.produk.kelola',
            'inventori.belanja.rencana.kelola',
            'inventori.toko.kelola',
            'inventori.belanja.mulai',
            'inventori.hitung.kelola',
            'inventori.alur.lihat'
        ])) {
            $this->filter = 'inventori';
        } elseif ($user->hasAnyPermission(['produksi.rencana.kelola', 'produksi.laporan.kelola', 'produksi.mulai'])) {
            $this->filter = 'produksi';
        } elseif ($user->hasAnyPermission(['kasir.pesanan.kelola', 'kasir.laporan.kelola'])) {
            $this->filter = 'kasir';
        } else {
            $this->filter = '';
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = \App\Models\Notification::find($notificationId);
        if ($notification && ! $notification->is_read) {
            $notification->update(['is_read' => true]);

            return redirect()->intended(route('notifikasi'))->with('notification', 'Notifikasi telah dibaca');
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        return redirect()->intended(route('notifikasi'))->with('notification', 'Semua notifikasi telah dibaca');
    }

    public function render()
    {
        return view('livewire.notification.index');
    }
}
