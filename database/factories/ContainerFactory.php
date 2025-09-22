<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Container>
 */
class ContainerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'container_id' => $this->faker->uuid(),
            'name' => $this->faker->slug(2),
            'status' => $this->faker->randomElement(['starting', 'running', 'stopped', 'error']),
            'port' => $this->faker->numberBetween(3000, 9999),
            'url' => $this->faker->url(),
            'environment' => [
                'NODE_ENV' => 'development',
                'PORT' => $this->faker->numberBetween(3000, 9999),
            ],
            'logs' => $this->faker->paragraph(),
            'started_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
            'stopped_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
