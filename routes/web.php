<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CourseWebController;
use App\Http\Controllers\Web\MaterialWebController;
use App\Http\Controllers\Web\AssignmentWebController;
use App\Http\Controllers\Web\SubmissionWebController;
use App\Http\Controllers\Web\DiscussionWebController;
use App\Http\Controllers\Web\ReplyWebController;
use App\Http\Controllers\Web\ReportsController;

Route::middleware(['auth'])->group(function () {
    Route::post('/materials', [MaterialWebController::class, 'store'])->middleware('role:lecturer');
});


Route::get('/', fn() => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login']);

    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthWebController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CourseWebController::class, 'index']);
    Route::post('/courses', [CourseWebController::class, 'store'])->middleware('role:lecturer');
    Route::post('/courses/{id}/enroll', [CourseWebController::class, 'enroll'])->middleware('role:student');
    Route::post('/courses/{id}/delete', [CourseWebController::class, 'destroy'])->middleware('role:lecturer');
    Route::put('/courses/{id}', [CourseWebController::class, 'update'])->middleware('role:lecturer');
    Route::post('/materials', [MaterialWebController::class, 'store'])->middleware(['auth', 'role:lecturer']);
    Route::get('/materials/{id}/download', [MaterialWebController::class, 'download'])
        ->name('materials.download');
    Route::delete(
        '/materials/{id}',
        [MaterialWebController::class, 'destroy']

    )->name('materials.destroy');
    Route::post('/assignments', [AssignmentWebController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{id}/download', [AssignmentWebController::class, 'download'])
        ->name('assignments.download');

    Route::post('/submissions', [SubmissionWebController::class, 'store'])->name('submissions.store');
    Route::post('/submissions/{id}/grade', [SubmissionWebController::class, 'grade'])->name('submissions.grade');
    Route::get('/submissions/{id}/download', [SubmissionWebController::class, 'download'])->name('submissions.download');
    Route::post('/discussions', [DiscussionWebController::class, 'store'])
        ->name('discussions.store');
    Route::post('/discussions/{id}/replies', [ReplyWebController::class, 'store'])

        ->name('replies.store');

    Route::get('/reports/courses', [ReportsController::class, 'courses']);
    Route::get('/reports/assignments', [ReportsController::class, 'assignments']);
    Route::get('/reports/students/{id}', [ReportsController::class, 'student']);
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::post('/logout', [AuthWebController::class, 'logout']);
});
