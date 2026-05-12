<?php

use App\Http\Controllers\Api\VexelController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', [VexelController::class, 'health']);
    Route::get('overview', [VexelController::class, 'overview']);
    Route::get('members', [VexelController::class, 'members']);
    Route::get('members/{member:slug}', [VexelController::class, 'member']);
    Route::get('contracts', [VexelController::class, 'contracts']);
    Route::get('vehicles', [VexelController::class, 'vehicles']);
    Route::get('map-points', [VexelController::class, 'mapPoints']);
    Route::get('timeline', [VexelController::class, 'timeline']);
});
