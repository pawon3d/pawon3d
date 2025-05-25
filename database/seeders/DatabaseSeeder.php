<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\IngredientCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Material;
use App\Models\Unit;
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
        ]);

        Material::create([
            'name' => 'Bahan Baku 2',
        ]);

        Material::create([
            'name' => 'Bahan Baku 3',
        ]);

        Category::create([
            'name' => 'Makanan',
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Minuman',
            'is_active' => true,
        ]);

        IngredientCategory::create([
            'name' => 'Bahan Kering',
            'is_active' => true,
        ]);
        IngredientCategory::create([
            'name' => 'Bahan Halus',
            'is_active' => true,
        ]);

        Unit::create([
            'name' => 'Gram',
            'alias' => 'g',
        ]);
        Unit::create([
            'name' => 'Kilogram',
            'alias' => 'kg',
        ]);
        Unit::create([
            'name' => 'Liter',
            'alias' => 'l',
        ]);
        Unit::create([
            'name' => 'Pcs',
            'alias' => 'pcs',
        ]);
    }
}
