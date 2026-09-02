<?php

use App\Http\Controllers\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health/database', [HealthController::class, 'database']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
