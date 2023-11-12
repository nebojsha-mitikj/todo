<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\TaskStatus;
use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{

    /**
     * Display a listing of the tasks.
     */
    public function index(): Response
    {
        $timezone = Auth::user()->timezone;
        return Inertia::render('Task/Todo', [
            'today' => Carbon::now($timezone)->format('Y-m-d'),
            'tasks' => Task::where('user_id', '=', Auth::id())
                ->whereBetween('date', [
                    Carbon::now($timezone)->format('Y-m-d'),
                    Carbon::now($timezone)->addDay()->format('Y-m-d')
                ])
                ->get()
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        return response()->json(['task' => (new Task())->updateTask($request)]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        return response()->json(['task' => $task->updateTask($request)]);
    }

    /*
     * Update the specified task status in storage.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $task->updateStatus(TaskStatus::tryFrom($request->status));
        $goalReached = false;
        if(Task::areFinished($user) && !$user->dailyGoalIsReached()){
            $user->setDailyGoalReach();
            $goalReached = true;
        }
        return response()->json(['task' => $task, 'goalReached' => $goalReached]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(DestroyTaskRequest $request, Task $task): JsonResponse
    {
        return response()->json(['taskDestroyed' => $task->delete()]);
    }
}
