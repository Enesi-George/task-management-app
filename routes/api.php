<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserListController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class)->only(['index', 'show']);
    Route::apiResource('user-lists', UserListController::class);
});