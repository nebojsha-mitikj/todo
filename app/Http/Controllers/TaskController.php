<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        return Inertia::render('Task/Todo', [
            'tasks' => Task::where('user_id', '=', Auth::id())
                ->whereBetween('date', [Carbon::today(), Carbon::tomorrow()])
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
        $user = User::findOrFail(Auth::id());
        $task->updateStatus($request->status);
        if(Task::areFinished($user->id) && !$user->dailyGoalIsReached()){
            $user->setDailyGoalReach();
        }
        return response()->json(['task' => $task]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(DestroyTaskRequest $request, Task $task): JsonResponse
    {
        return response()->json(['taskDestroyed' => $task->delete()]);
    }
}
