<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['general', 'code_review', 'suggestion', 'question']),
            'metadata' => null,
            'parent_id' => null,
            'is_resolved' => false,
        ];
    }

    /**
     * Indicate that the comment is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_resolved' => true,
        ]);
    }

    /**
     * Indicate that the comment is a reply.
     */
    public function reply(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Comment::factory(),
        ]);
    }

    /**
     * Indicate that the comment is a code review.
     */
    public function codeReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'code_review',
            'metadata' => [
                'line_number' => $this->faker->numberBetween(1, 100),
                'file' => 'index.html',
            ],
        ]);
    }
}