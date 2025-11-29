<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'body' => $this->faker->sentence(1),
            'is_read' => false,
            'user_id' => 'a051b98b-1066-485d-887c-0c4cf2e3c321',
            'status' => rand(0, 2),
            'created_at' => now(),
        ];
    }
}
