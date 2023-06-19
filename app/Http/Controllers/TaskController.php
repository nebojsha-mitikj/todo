<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{

    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        return response()->json(['task' => (new Task())->updateTask($request)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        return response()->json(['task' => $task->updateTask($request)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyTaskRequest $request, Task $task): JsonResponse
    {
        return response()->json(['taskDestroyed' => $task->delete()]);
    }
}
