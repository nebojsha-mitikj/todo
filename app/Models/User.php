<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'daily_goal_reach_counter',
        'last_daily_goal_reach_date',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function dailyGoalStreakIsActive(): bool
    {
        return $this->last_daily_goal_reach_date !== null && $this->daily_goal_reach_counter > 0;
    }

    public function dailyGoalIsReached(): bool
    {
        return $this->last_daily_goal_reach_date === Carbon::today()->format('Y-m-d');
    }

    public function shouldResetGoalReach(): bool
    {
        if($this->dailyGoalIsReached() || !$this->dailyGoalStreakIsActive()){
            return false;
        }
        return $this->tasks()
            ->whereBetween('date', [
                $this->last_daily_goal_reach_date,
                Carbon::yesterday()->toDateString()
            ])->where('status', '!=', 'finished')
            ->exists();
    }

    public function resetGoalReach(): bool
    {
        $this->daily_goal_reach_counter = 0;
        $this->save();
        return true;
    }

    public function setDailyGoalReach(): bool
    {
        $this->last_daily_goal_reach_date = Carbon::today();
        ++$this->daily_goal_reach_counter;
        return $this->save();
    }

}
