<?php

namespace Database\Seeders;

use App\Models\Padan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PadanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Padan::create([
            'action' => 'Hitung Persediaan',
            'note' => 'Initial padan for testing',
            'status' => 'Draft',
            'padan_date' => now(),
            'padan_date_finish' => now()->addDays(7),
            'is_start' => false,
            'is_finish' => false,

            'created_at' => now(),
        ]);
        Padan::create([
            'action' => 'Catat Persediaan Rusak',
            'note' => 'Padan for damaged inventory',
            'status' => 'Sedang Diproses',
            'padan_date' => now(),
            'padan_date_finish' => now()->addDays(7),
            'is_start' => true,
            'is_finish' => false,

            'created_at' => now()->addDays(1),
        ]);
        Padan::create([
            'action' => 'Catat Persediaan Hilang',
            'note' => 'Padan for lost inventory',
            'status' => 'Selesai',
            'padan_date' => now(),
            'padan_date_finish' => now()->addDays(7),
            'is_start' => true,
            'is_finish' => true,

            'created_at' => now()->addDays(2),
        ]);
    }
}
