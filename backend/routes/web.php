<?php

use App\Http\Controllers\Dashboard\TimelineController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'vexel-api',
        'status' => 'ok',
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::prefix('/dashboard/timeline')->name('dashboard.timeline.')->group(function (): void {
    Route::get('/', [TimelineController::class, 'index'])->name('index');
    Route::get('/create', [TimelineController::class, 'create'])->name('create');
    Route::post('/', [TimelineController::class, 'store'])->name('store');
    Route::get('/{timelineEvent}/edit', [TimelineController::class, 'edit'])->name('edit');
    Route::put('/{timelineEvent}', [TimelineController::class, 'update'])->name('update');
    Route::delete('/{timelineEvent}', [TimelineController::class, 'destroy'])->name('destroy');
});
Route::get('/dashboard/{section}', [DashboardController::class, 'page'])->name('dashboard.page');

Route::fallback(function () {
    return response()->file(public_path('index.html'));
});
