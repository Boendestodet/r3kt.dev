<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'slug' => $this->faker->slug(),
            'status' => $this->faker->randomElement(['draft', 'building', 'ready', 'error']),
            'is_public' => $this->faker->boolean(20), // 20% chance of being public
            'settings' => [],
        ];
    }
}
