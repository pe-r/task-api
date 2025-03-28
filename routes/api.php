<?php

use Illuminate\HTTP\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\CheckTaskAuthorization;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware([CheckTaskAuthorization::class])->group(function () {
        Route::get('/tasks/{taskId}', [TaskController::class, 'show']);
        Route::put('/tasks/{taskId}', [TaskController::class, 'update']);
        Route::delete('/tasks/{taskId}', [TaskController::class, 'destroy']);
    });

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);

    Route::get('/projects', [TaskController::class, 'listProjects']);
    Route::get('/tasks.project/{projectId}', [TaskController::class, 'showProjectTasks']);
    Route::get('/tasks.user/{id}', [TaskController::class, 'showUserTasks']);

    Route::get('/tasks.overdue', [TaskController::class, 'overdueTasks']);
});
