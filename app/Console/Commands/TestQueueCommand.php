<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\UserInvitationNotification;

class TestQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test queue dengan mengirim dummy notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Membuat job di queue...');

        // Ambil user pertama untuk test
        $user = User::first();

        if (!$user) {
            $this->error('Tidak ada user di database untuk test!');
            return;
        }

        // Dispatch notification ke queue
        $user->notify(new UserInvitationNotification('Test Store'));

        $this->info('✓ Job berhasil ditambahkan ke queue!');
        $this->info('Cek terminal queue worker untuk melihat job diproses.');
        $this->newLine();
        $this->line('Query untuk cek jobs:');
        $this->line('SELECT * FROM jobs;');
    }
}
