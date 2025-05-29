<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\IngredientCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Material;
use App\Models\Supplier;
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

        Material::create([
            'name' => 'Bahan Baku 1',
        ])->material_details()->create([
            'supply_quantity' => 100,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
            'is_main' => true,
        ]);

        Material::create([
            'name' => 'Bahan Baku 2',
        ])->material_details()->create([
            'supply_quantity' => 50,
            'unit_id' => Unit::where('alias', 'l')->first()->id,
            'is_main' => true,
        ]);

        Material::create([
            'name' => 'Bahan Baku 3',
        ]);

        $supplier = Supplier::create([
            'name' => 'Supplier 1',
            'contact_name' => 'Sarini',
            'phone' => '081234567890',
        ]);

        Expense::create([
            'expense_date' => now(),
            'supplier_id' => $supplier->id,
            'status' => 'selesai',
            'is_start' => true,
            'is_finish' => true,
        ])->expenseDetails()->createMany([
            [
                'material_id' => Material::first()->id,
                'quantity_expect' => 10,
                'price_expect' => 10000,
                'quantity_get' => 9,
                'price_get' => 10000,
                'is_quantity_get' => true,
            ],
            [
                'material_id' => Material::skip(1)->first()->id,
                'quantity_expect' => 5,
                'price_expect' => 20000,
                'quantity_get' => 4,
                'price_get' => 20000,
            ],
            [
                'material_id' => Material::skip(2)->first()->id,
                'quantity_expect' => 2,
                'price_expect' => 25000,
                'quantity_get' => 2,
                'price_get' => 25000,
            ],
        ])->each(function ($expenseDetail) {
            $expenseDetail->update([
                'total_expect' => $expenseDetail->quantity_expect * $expenseDetail->price_expect,
                'total_actual' => $expenseDetail->quantity_get * $expenseDetail->price_get,
            ]);
            $expenseDetail->expense->update([
                'grand_total_expect' => $expenseDetail->expense->expenseDetails->sum(function ($detail) {
                    return $detail->total_expect;
                }),
                'grand_total_actual' => $expenseDetail->expense->expenseDetails->sum(function ($detail) {
                    return $detail->total_actual;
                }),
            ]);
        });
    }
}
