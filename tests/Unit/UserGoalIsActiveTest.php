<?php

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserGoalIsActiveTest extends TestCase
{

    public function test_daily_goal_streak_is_active(): void
    {
        $user = new User();

        $user->last_daily_goal_reach_date = Carbon::yesterday()->toDateString();
        $user->daily_goal_reach_counter = 2;

        $this->assertTrue($user->dailyGoalStreakIsActive());
    }

    public function test_daily_goal_streak_is_inactive(): void
    {
        $cases = [
            ['date' => null, 'count' => 1],
            ['date' => Carbon::yesterday()->toDateString(), 'count' => 0]
        ];
        foreach ($cases as $case){
            $user = new User();
            $user->last_daily_goal_reach_date = $case['date'];
            $user->daily_goal_reach_counter = $case['count'];
            $this->assertFalse($user->dailyGoalStreakIsActive());
        }
    }

}
