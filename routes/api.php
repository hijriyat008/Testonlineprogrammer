<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\DiscussionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('courses', CourseController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/courses', [CourseController::class, 'store'])->middleware('role:lecturer');
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->middleware('role:student');
    Route::post('/materials', [MaterialController::class, 'store']);
    Route::get('/materials/{id}/download', [MaterialController::class, 'download']);
    Route::post('/discussions', [DiscussionController::class, 'store']);
    Route::post('/discussions/{id}/replies', [DiscussionController::class, 'reply']);
});
