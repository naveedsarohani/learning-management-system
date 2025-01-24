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
Route::controller(UserController::class)->group(function(){
    Route::get('/cities', 'cities');

    Route::prefix('auth')->group(function(){
        Route::get('/users', 'index');
        Route::post('/login', 'validateLogin');
        Route::post('/register', 'register');
    });
});

Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
Route::apiResource('lessons', LessonController::class)->only(['index', 'show']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {
    // auth
    Route::controller(UserController::class)->prefix('auth')->group(function(){
        Route::get('/logout', 'invalidateLogin');
        Route::put('/update', 'update');
        Route::delete('/delete/{user}', 'delete');
        Route::post('/update-password', 'updatePassword'); 
    });

    // resource apivzs
    Route::apiResource('enrollments', EnrollmentController::class)->only(['index', 'show', 'store']);
    Route::apiResource('assessments', AssessmentController::class)->only(['index', 'show']);
    Route::apiResource('submissions', SubmissionController::class)->only(['index', 'show']);
    Route::apiResource('questions', QuestionController::class)->only(['index', 'show']);
    Route::apiResource('answers', AnswerController::class)->only(['index', 'show']);
    Route::apiResource('progresses', ProgressController::class)->except(['destroy']);
    Route::apiResource('submissions', SubmissionController::class)->only(['store']);
    Route::apiResource('exams', ExamController::class)->only(['index', 'show']);
    Route::apiResource('exam-questions', ExamQuestionController::class)->only(['index', 'show']);
    Route::apiResource('exam-submissions', ExamSubmissionController::class)->except(['destroy']);

    // only admin/instructor
    Route::middleware('instructor_or_admin')->group(function(){
        Route::apiResource('courses', CourseController::class)->except(['index', 'show']);
        Route::apiResource('enrollments', EnrollmentController::class)->except(['index', 'show', 'store']);
        Route::apiResource('progresses', ProgressController::class)->only(['destroy']);
        Route::apiResource('assessments', AssessmentController::class)->except(['index', 'show']);
        Route::apiResource('lessons', LessonController::class)->except(['index', 'show']);
        Route::apiResource('questions', QuestionController::class)->except(['index', 'show']);
        Route::apiResource('answers', AnswerController::class)->except(['index', 'show']);
        Route::apiResource('submissions', SubmissionController::class)->only(['destroy']);
        Route::apiResource('exams', ExamController::class)->except(['index', 'show']);
        Route::apiResource('exam-questions', ExamQuestionController::class)->except(['index', 'show']);
        Route::apiResource('exam-submissions', ExamSubmissionController::class)->only(['destroy']);
    });
});
