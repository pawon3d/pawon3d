<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Console\Command;

class TestSendInvitation extends Command
{
    protected $signature = 'app:test-send-invitation {email}';

    protected $description = 'Test kirim email undangan ke alamat email tertentu';

    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info("Mengirim email undangan ke: {$email}");

        // Cek apakah email sudah ada
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $this->warn("User dengan email {$email} sudah ada.");
            $this->info("ID: {$existingUser->id}");
            $this->info('Token: '.($existingUser->invitation_token ?? 'N/A'));

            // Generate token jika belum ada
            if (! $existingUser->invitation_token) {
                $existingUser->invitation_token = bin2hex(random_bytes(32));
                $existingUser->invitation_sent_at = now();
                $existingUser->save();
                $this->info("Token baru: {$existingUser->invitation_token}");
            }

            // Kirim langsung (sync) untuk testing
            $storeName = 'Test Store';
            $this->info('Mengirim email sekarang (sinkron)...');

            $existingUser->notifyNow(new UserInvitationNotification($storeName));

            $this->info('✓ Email berhasil dikirim!');

            return self::SUCCESS;
        }

        // Buat user baru untuk test
        $user = User::factory()->create([
            'email' => $email,
            'name' => 'Test Worker',
            'is_active' => false,
        ]);

        $user->invitation_token = bin2hex(random_bytes(32));
        $user->invitation_sent_at = now();
        $user->save();

        // Kirim langsung (sync) untuk testing
        $storeName = 'Test Store';
        $this->info('Mengirim email sekarang (sinkron)...');

        $user->notifyNow(new UserInvitationNotification($storeName));

        $this->info('✓ Email berhasil dikirim!');
        $this->info("Token: {$user->invitation_token}");

        // Hapus user test
        if ($this->confirm('Hapus user test ini?', true)) {
            $user->forceDelete();
            $this->info('User test dihapus.');
        }

        return self::SUCCESS;
    }
}
