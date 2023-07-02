<?php

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UserGoalReachedTest extends TestCase
{

    public function test_daily_goal_is_reached(): void
    {
        $user = new User();
        $user->last_daily_goal_reach_date = Carbon::today()->toDateString();
        $this->assertTrue($user->dailyGoalIsReached());
    }

    public function test_daily_goal_is_not_reached(): void
    {
        $cases = [
            Carbon::tomorrow()->toDateString(),
            Carbon::yesterday()->toDateString(),
            '',
            null,
            true,
            false,
            1
        ];
        $user = new User();
        foreach ($cases as $case){
            $user->last_daily_goal_reach_date = $case;
            $this->assertFalse($user->dailyGoalIsReached());
        }
    }

}
