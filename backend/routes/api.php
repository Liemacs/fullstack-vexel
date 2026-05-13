<?php

use App\Http\Controllers\Api\MemberAuthController;
use App\Http\Controllers\Api\VexelController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', [VexelController::class, 'health']);
    Route::get('overview', [VexelController::class, 'overview']);
    Route::get('members', [VexelController::class, 'members']);
    Route::get('members/{member:slug}', [VexelController::class, 'member']);
    Route::get('positions', [VexelController::class, 'positions']);
    Route::get('contracts', [VexelController::class, 'contracts']);
    Route::get('vehicles', [VexelController::class, 'vehicles']);
    Route::get('map-points', [VexelController::class, 'mapPoints']);
    Route::get('timeline', [VexelController::class, 'timeline']);

    Route::prefix('member')->group(function (): void {
        Route::post('login', [MemberAuthController::class, 'login']);
        Route::get('me', [MemberAuthController::class, 'me']);
        Route::post('me', [MemberAuthController::class, 'update']);
        Route::post('logout', [MemberAuthController::class, 'logout']);
    });
});
