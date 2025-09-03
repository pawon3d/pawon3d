<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::factory()->count(2)->create();
        // Notification::create([
        //     'title' => 'Transaksi sebesar Rp145.000 untuk pesanan OR-250512-0001',
        //     'body' => 'telah berhasil diproses.',
        //     'status' => 2,
        //     'user_id' => "a051b98b-1066-485d-887c-0c4cf2e3c321",
        //     'is_read' => false,
        // ]);
    }
}
