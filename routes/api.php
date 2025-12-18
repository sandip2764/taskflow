<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Task routes
    Route::get('/tasks/statistics', [TaskController::class, 'statistics']);
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::apiResource('tasks', TaskController::class);
    
    // Category routes
    Route::apiResource('categories', CategoryController::class)->only(['index', 'store', 'show']);
});
