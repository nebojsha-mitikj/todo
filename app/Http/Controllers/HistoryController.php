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
        $timezone = Auth::user()->timezone;
        return Inertia::render('Task/History', [
            'tasks' => Task::where('user_id', '=', Auth::id())
                ->whereBetween('date', [
                    Carbon::now($timezone)->subDays(14)->format('Y-m-d'),
                    Carbon::now($timezone)->subDay()->format('Y-m-d')
                ])->orderBy('date', 'desc')
                ->get()
        ]);
    }

}
