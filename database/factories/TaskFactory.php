<?php

namespace Database\Factories;

use App\Models\Project;
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
            'project_id'  => Project::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status'      => 'todo',
            'priority'    => 'medium',
            'due_date'    => fake()->dateTimeBetween('now', '+30 days'),
            'assigned_to' => null,
        ];
    }
}
