<?php

use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\ExamQuestionController;
use App\Http\Controllers\API\ExamSubmissionController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\ProgressController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/cities', [UserController::class, 'cities']);


Route::controller(UserController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'validateLogin');
    Route::post('/register', 'register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users', 'index');
        Route::get('/logout', 'invalidateLogin');
        Route::put('/update', 'update');
        Route::delete('/delete/{user}', 'delete');
        Route::post('/update-password', 'updatePassword');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('assessments', AssessmentController::class)->only(['index', 'show']);
    Route::apiResource('lessons', LessonController::class)->only(['index', 'show']);
    Route::get('/lessons/courses/{courseId}', [LessonController::class, 'courseLessons']);
    Route::apiResource('submissions', SubmissionController::class)->only(['store']);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
    Route::apiResource('progresses', ProgressController::class)->except(['destroy']);

    Route::apiResource('enrollments', EnrollmentController::class);
    Route::apiResource('answers', AnswerController::class);

    // exam/test
    Route::apiResource('exams', ExamController::class)->only(['index', 'show']);
    Route::apiResource('exam-questions', ExamQuestionController::class)->only(['index', 'show']);
    Route::apiResource('exam-submissions', ExamSubmissionController::class)->only(['store', 'index', 'show']);

    Route::middleware('instructor_or_admin')->group(function () {
        Route::apiResource('assessments', AssessmentController::class)->except(['index', 'show']);
        Route::apiResource('lessons', LessonController::class)->except(['index', 'show']);
        Route::apiResource('submissions', SubmissionController::class)->except(['store']);
        Route::apiResource('questions', QuestionController::class)->except(['index', 'show']);
        Route::apiResource('progresses', ProgressController::class)->only(['destroy']);

        // exam/test
        Route::apiResource('exams', ExamController::class)->except(['index', 'show']);
        Route::apiResource('exam-questions', ExamQuestionController::class)->except(['index', 'show']);
        Route::apiResource('exam-submissions', ExamSubmissionController::class)->except(['store', 'index', 'show']);
    });
});
