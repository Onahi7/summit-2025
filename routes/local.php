<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocalTestController;

// These routes are only for local development and testing
Route::prefix('local-test')->group(function () {
    Route::get('/login-as-admin', [LocalTestController::class, 'loginAsAdmin']);
    Route::get('/login-as-participant', [LocalTestController::class, 'loginAsParticipant']);
    Route::get('/login-as-validator', [LocalTestController::class, 'loginAsValidator']);
});
