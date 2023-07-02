<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
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
            'description' => fake()->text(50),
            'status' => 'to-do',
            'date' => Carbon::today(),
            'start_time' => '00:00:00',
            'end_time' => '00:00:00',
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }
}
