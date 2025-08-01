<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Hitung;
use App\Models\IngredientCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Material;
use App\Models\MaterialBatch;
use App\Models\PaymentChannel;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Buat role jika belum ada
        Permission::create(['name' => 'Inventori']);
        Permission::create(['name' => 'Produksi']);
        Permission::create(['name' => 'Kasir']);
        Permission::create(['name' => 'Manajemen Sistem']);

        $role = Role::firstOrCreate(['name' => 'Manajemen Sistem'])->givePermissionTo([
            'Manajemen Sistem',
            'Inventori',
            'Produksi',
            'Kasir',
        ]);

        // Buat user default
        $user = User::create([
            'name' => 'Pemilik',
            'email' => 'pemilik@pawon3d.local',
            'phone' => '08123456789',
            'password' => bcrypt('1234'),
            'image' => null,
        ]);

        // Assign role
        $user->assignRole($role);

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

        $material1 = Material::create([
            'name' => 'Bahan Baku 1',
        ]);

        $material1->material_details()->create([
            'supply_quantity' => 100,
            'supply_price' => 10000,
            'quantity' => 1,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
            'is_main' => true,
        ]);

        MaterialBatch::create([
            'batch_number' => 'BB-001',
            'date' => now()->subDays(30),
            'batch_quantity' => 100,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
            'material_id' => $material1->id,
        ]);


        $material2 = Material::create([
            'name' => 'Bahan Baku 2',
        ]);
        $material2->material_details()->create([
            'supply_quantity' => 50,
            'supply_price' => 20000,
            'quantity' => 1,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
            'is_main' => true,
        ]);
        $material2->batches()->create([
            'batch_number' => 'BB-002',
            'date' => now()->subDays(30),
            'batch_quantity' => 50,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
        ]);
        $material3 = Material::create([
            'name' => 'Bahan Baku 3',
        ]);
        $material3->material_details()->create([
            'supply_quantity' => 20,
            'supply_price' => 25000,
            'quantity' => 1,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
            'is_main' => true,
        ]);
        $material3->batches()->create([
            'batch_number' => 'BB-003',
            'date' => now()->subDays(30),
            'batch_quantity' => 20,
            'unit_id' => Unit::where('alias', 'kg')->first()->id,
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

        $this->call([
            HitungSeeder::class,
        ]);

        Product::create([
            'name' => 'Product 1',
            'pcs' => 1,
            'price' => 10000,
            'stock' => 15,
            'method' => 'pesanan-reguler',
        ]);
        Product::create([
            'name' => 'Product 2',
            'pcs' => 1,
            'price' => 20000,
            'stock' => 10,
            'method' => 'pesanan-reguler',
        ])->product_compositions()->create([
            'material_id' => Material::first()->id,
        ]);
        Product::create([
            'name' => 'Product 3',
            'pcs' => 1,
            'price' => 30000,
            'stock' => 5,
            'method' => 'pesanan-reguler',
        ])->product_compositions()->create([
            'material_id' => Material::skip(1)->first()->id,
        ]);
        Product::create([
            'name' => 'Product 4',
            'pcs' => 1,
            'price' => 40000,
            'stock' => 20,
            'method' => 'pesanan-reguler',
        ])->product_compositions()->create([
            'material_id' => Material::skip(2)->first()->id,
        ]);
        Product::create([
            'name' => 'Product 5',
            'pcs' => 1,
            'price' => 50000,
            'stock' => 8,
            'method' => 'pesanan-reguler',
        ])->product_compositions()->create([
            'material_id' => Material::first()->id,
        ]);

        PaymentChannel::create([
            'bank_name' => 'BRI',
            'type' => 'transfer',
            'account_number' => '0912389103',
            'account_name' => 'Pawon 3D BRI',
            'is_active' => true,
        ]);
        PaymentChannel::create([
            'bank_name' => 'BCA',
            'type' => 'transfer',
            'account_number' => '0912389103',
            'account_name' => 'Pawon 3D BCA',
            'is_active' => true,
        ]);
        PaymentChannel::create([
            'bank_name' => 'Mandiri',
            'type' => 'transfer',
            'account_number' => '0912389103',
            'account_name' => 'Pawon 3D Mandiri',
            'is_active' => true,
        ]);
        PaymentChannel::create([
            'bank_name' => 'QRIS',
            'type' => 'qris',
            'qris_image' => 'https://example.com/qris-image.png',
            'is_active' => false,
        ]);
    }
}