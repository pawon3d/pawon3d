<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $storeName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $storeName)
    {
        $this->storeName = $storeName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $activationUrl = url("/aktivasi-akun/{$notifiable->invitation_token}");

        return (new MailMessage)
            ->subject("Undangan Bergabung - {$this->storeName}")
            ->greeting("Halo {$notifiable->name}!")
            ->line("Anda telah diundang untuk bergabung sebagai pekerja di **{$this->storeName}**.")
            ->line('Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda dan membuat kata sandi.')
            ->action('Aktifkan Akun Saya', $activationUrl)
            ->line('Link ini akan berlaku selama 7 hari.')
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.')
            ->salutation('Salam, Tim ' . $this->storeName);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Undangan aktivasi akun dikirim ke {$notifiable->email}",
        ];
    }
}