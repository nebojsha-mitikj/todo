<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\TaskStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'description',
        'status',
        'date',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateTask(Request $request): self
    {
        $this->description = $request->description;
        $this->status = $request->status;
        $this->user_id = Auth::id();
        if (empty($this->date)) {
            $timezone = Auth::user()->timezone;
            if ($request->for_today) {
                $this->date = Carbon::now($timezone)->format('Y-m-d');
            } else {
                $this->date = Carbon::now($timezone)->addDay()->format('Y-m-d');
            }
        }
        if (empty($request->start_time) || empty($request->end_time)) {
            $this->start_time = '00:00:00';
            $this->end_time = '00:00:00';
        } else {
            $this->start_time = $request->start_time;
            $this->end_time = $request->end_time;
        }
        $this->save();
        return $this;
    }

    public function updateStatus(TaskStatus $status): self
    {
        $this->status = $status->value;
        $this->save();
        return $this;
    }

    public static function areFinished(User $user): bool
    {
        return !self::where('user_id', $user->id)
            ->whereDate('date', Carbon::now($user->timezone)->format('Y-m-d'))
            ->where('status', '!=', TaskStatus::Finished->value)
            ->exists();
    }


}
