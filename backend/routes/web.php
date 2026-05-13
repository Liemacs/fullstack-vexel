<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\ContractController;
use App\Http\Controllers\Dashboard\MemberController;
use App\Http\Controllers\Dashboard\PositionController;
use App\Http\Controllers\Dashboard\TimelineController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard/health', function () {
    return response()->json([
        'service' => 'vexel-api',
        'status' => 'ok',
    ]);
})->name('dashboard.health');

Route::get('/dashboard/login', [AuthController::class, 'show'])->name('dashboard.login');
Route::post('/dashboard/login', [AuthController::class, 'login'])->name('dashboard.login.store');
Route::post('/dashboard/logout', [AuthController::class, 'logout'])->name('dashboard.logout');

Route::middleware('dashboard.auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/dashboard/positions', PositionController::class)->names('dashboard.positions')->except(['show']);
    Route::resource('/dashboard/members', MemberController::class)->names('dashboard.members')->except(['show']);
    Route::resource('/dashboard/contracts', ContractController::class)->names('dashboard.contracts')->except(['show']);
    Route::prefix('/dashboard/timeline')->name('dashboard.timeline.')->group(function (): void {
        Route::get('/', [TimelineController::class, 'index'])->name('index');
        Route::get('/create', [TimelineController::class, 'create'])->name('create');
        Route::post('/', [TimelineController::class, 'store'])->name('store');
        Route::get('/{timelineEvent}/edit', [TimelineController::class, 'edit'])->name('edit');
        Route::put('/{timelineEvent}', [TimelineController::class, 'update'])->name('update');
        Route::delete('/{timelineEvent}', [TimelineController::class, 'destroy'])->name('destroy');
    });
    Route::get('/dashboard/{section}', [DashboardController::class, 'page'])->name('dashboard.page');
});

Route::fallback(function () {
    if (! file_exists(public_path('index.html'))) {
        return response('<!doctype html><html lang="ru"><head><meta charset="utf-8"><title>Vexel</title></head><body><h1>Vexel</h1><p>Frontend build is not available in this environment.</p></body></html>');
    }

    return response()->file(public_path('index.html'));
});
