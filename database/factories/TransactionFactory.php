<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'invoice_number' => 'OR-'.date('ymd').'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
            'start_date' => now(),
            'payment_status' => fake()->randomElement(['Lunas', 'Belum Lunas']),
            'status' => fake()->randomElement(['Draft', 'Belum Diproses', 'Sedang Diproses', 'Dapat Diambil', 'Selesai', 'Gagal']),
            'method' => fake()->randomElement(['pesanan-reguler', 'pesanan-kotak', 'siap-beli']),
            'total_amount' => fake()->numberBetween(10000, 500000),
            'points_used' => 0,
            'points_discount' => 0,
        ];
    }
}
