<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Task/History', [
            'tasks' => Task::where('user_id', '=', Auth::id())
                ->whereBetween('date', [
                    Carbon::now()->subDays(14)->format('Y-m-d'),
                    Carbon::now()->subDay()->format('Y-m-d')
                ])->orderBy('date', 'desc')
                ->get()
        ]);
    }

}
