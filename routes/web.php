<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\ResetGoalReachMiddleware;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(Auth::check()){
        return redirect(RouteServiceProvider::HOME);
    }
    return redirect('/login');
});

Route::middleware(['auth', ResetGoalReachMiddleware::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('todo')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('task.index');
        Route::post('/', [TaskController::class, 'store'])->name('task.store');
        Route::put('/{task}', [TaskController::class, 'update'])->name('task.update');
        Route::put('/status/{task}', [TaskController::class, 'updateStatus'])->name('task.update.status');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('task.destroy');
    });

    Route::prefix('history')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('history.index');
    });

});

require __DIR__.'/auth.php';
