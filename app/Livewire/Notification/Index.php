<?php

namespace App\Livewire\Notification;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Index extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert;
    public function mount()
    {
        View::share('title', 'Notifikasi');
        View::share('mainTitle', 'Notifikasi');
    }

    public function markAsRead($notificationId)
    {
        $notification = \App\Models\Notification::find($notificationId);
        if ($notification && !$notification->is_read) {
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
