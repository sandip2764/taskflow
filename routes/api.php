<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public routes with auth rate limiter
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Heavy operations with stricter limit
Route::middleware(['auth:sanctum', 'throttle:heavy'])->group(function () {
    Route::get('/tasks/statistics', [TaskController::class, 'statistics']);
});

// Protected routes with api rate limiter
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Task routes
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::apiResource('tasks', TaskController::class);
    
    // Category routes
    Route::apiResource('categories', CategoryController::class)->only(['index', 'store', 'show']);
});

