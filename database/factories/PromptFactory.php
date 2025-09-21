<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prompt>
 */
class PromptFactory extends Factory
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
            'prompt' => $this->faker->sentence(10),
            'response' => null,
            'status' => 'pending',
            'metadata' => null,
            'tokens_used' => null,
            'processed_at' => null,
            'auto_start_container' => false,
        ];
    }
}
