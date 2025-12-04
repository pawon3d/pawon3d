<?php

namespace App\Livewire\Notification;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dropdown extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public function markAsRead($id): void
    {
        $notification = Notification::find($id);

        if ($notification && $notification->user_id === Auth::id()) {
            $notification->update(['is_read' => true]);
            $this->alert('success', 'Notifikasi ditandai sudah dibaca.');
        }
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->alert('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function getNotificationsProperty()
    {
        return Auth::user()->notifications()->latest()->take(5)->get();
    }

    public function getUnreadCountProperty()
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notification.dropdown');
    }
}
