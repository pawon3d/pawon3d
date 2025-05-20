<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Material;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pemilik',
            'username' => 'pemilik',
            'role' => 'pemilik',
        ]);

        Material::create([
            'name' => 'Bahan Baku 1',
            'quantity' => 1000,
            'unit' => 'gram',
        ]);

        Material::create([
            'name' => 'Bahan Baku 2',
            'quantity' => 50,
            'unit' => 'kg',
        ]);

        Material::create([
            'name' => 'Bahan Baku 3',
            'quantity' => 20,
            'unit' => 'butir',
        ]);

        Category::create([
            'name' => 'Makanan',
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Minuman',
            'is_active' => true,
        ]);
    }
}
