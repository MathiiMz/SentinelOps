<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;

/**
 * Public Routes (No Authentication Required)
 */
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

/**
 * Protected Routes (Authentication Required)
 */
Route::middleware('auth:sanctum')->group(function () {
    /**
     * Authentication Routes
     */
    Route::prefix('auth')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    /**
     * User Routes
     */
    Route::apiResource('users', UserController::class)->except(['store']);

    /**
     * Incident Routes
     */
    Route::apiResource('incidents', IncidentController::class);
    Route::post('incidents/{incident}/assign', [IncidentController::class, 'assign']);
    Route::patch('incidents/{incident}/status', [IncidentController::class, 'updateStatus']);

    /**
     * Comment Routes
     */
    Route::get('incidents/{incident}/comments', [CommentController::class, 'index']);
    Route::post('incidents/{incident}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
});
