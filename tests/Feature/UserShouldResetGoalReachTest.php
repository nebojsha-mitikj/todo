<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserShouldResetGoalReachTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_should_reset_goal_reach(): void
    {
        $user = User::factory([
            'last_daily_goal_reach_date' => Carbon::yesterday()->toDateString(),
            'daily_goal_reach_counter' => 2
        ])->has(
            Task::factory([
                'status' => 'in-progress',
                'date' => Carbon::yesterday()->toDateString()
            ])->count(3)
        )->create();

        $this->assertTrue($user->shouldResetGoalReach());
    }

    public function test_user_should_not_reset_goal_reach(): void
    {
        $user = User::factory([
            'last_daily_goal_reach_date' => Carbon::yesterday()->toDateString(),
            'daily_goal_reach_counter' => 1
        ])->has(
            Task::factory([
                'status' => 'finished',
                'date' => Carbon::yesterday()->toDateString()
            ])->count(3)
        )->create();

        $this->assertFalse($user->shouldResetGoalReach());
    }
}
