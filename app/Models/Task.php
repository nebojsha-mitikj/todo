<?php

declare(strict_types=1);

namespace App\Models;

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

    protected $fillable = ['description', 'status', 'date', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateTask(Request $request): self
    {
        $this->description = $request->description;
        $this->status = $request->status;
        $this->user_id = Auth::id();
        $this->date = $request->has('date') ? $request->date : Carbon::now();
        $this->save();
        return $this;
    }


}