<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'status' => 'todo',
            'user_id' => 1,
            'project_id' => 1,
            'deadline' => fake()->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d H:i:s'),
        ];
    }
}
