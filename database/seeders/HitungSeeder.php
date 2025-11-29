<?php

namespace Database\Seeders;

use App\Models\Hitung;
use Illuminate\Database\Seeder;

class HitungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hitung::create([
            'action' => 'Hitung Persediaan',
            'note' => 'Initial hitung for testing',
            'status' => 'Draft',
            'hitung_date' => now(),
            'hitung_date_finish' => now()->addDays(7),
            'is_start' => false,
            'is_finish' => false,

            'created_at' => now(),
        ]);
        Hitung::create([
            'action' => 'Catat Persediaan Rusak',
            'note' => 'Hitung for damaged inventory',
            'status' => 'Sedang Diproses',
            'hitung_date' => now(),
            'hitung_date_finish' => now()->addDays(7),
            'is_start' => true,
            'is_finish' => false,

            'created_at' => now()->addDays(1),
        ]);
        Hitung::create([
            'action' => 'Catat Persediaan Hilang',
            'note' => 'Hitung for lost inventory',
            'status' => 'Selesai',
            'hitung_date' => now(),
            'hitung_date_finish' => now()->addDays(7),
            'is_start' => true,
            'is_finish' => true,

            'created_at' => now()->addDays(2),
        ]);
    }
}
