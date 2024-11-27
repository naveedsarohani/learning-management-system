<?php

use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
<<<<<<<<< Temporary merge branch 1
use App\Http\Controllers\API\QuestionController;
=========
use App\Http\Controllers\API\LessonController;
>>>>>>>>> Temporary merge branch 2
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'validateLogin');
    Route::post('/register', 'register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users', 'index');
        Route::get('/logout', 'invalidateLogin');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{user}', 'delete');
        Route::post('/update-password/{id}', 'updatePassword');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessments', AssessmentController::class)->except(['index', 'show', 'update']);
    Route::apiResource('submissions', SubmissionController::class)->only(['index', 'show']);
<<<<<<<<< Temporary merge branch 1
    Route::apiResource('enrollments', EnrollmentController::class);
=========
    Route::apiResource('lessons', LessonController::class);
>>>>>>>>> Temporary merge branch 2

    Route::middleware('instructor_or_admin')->group(function () {
        Route::apiResource('assessments', AssessmentController::class)->only(['index', 'show', 'update']);
        Route::apiResource('submissions', SubmissionController::class)->except(['index', 'show']);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('questions', QuestionController::class);
    });
});
