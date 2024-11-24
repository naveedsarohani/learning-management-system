<?php

use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
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

Route::apiResource('assessments', AssessmentController::class);

/*Routes Maintained By Wajid*/

#Course
Route::apiResource('courses', CourseController::class)->middleware(['auth:sanctum', 'instructor_or_admin']);

#Enrollment
Route::apiResource('enrollments', EnrollmentController::class)->middleware('auth:sanctum');
