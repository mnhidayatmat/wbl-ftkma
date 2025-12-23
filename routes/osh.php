<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| OSH Routes (Occupational Safety & Health - Lecturer)
|--------------------------------------------------------------------------
|
| Routes for Lecturers managing Occupational Safety & Health (OSH) course
| for students.
|
*/

Route::middleware(['auth'])->group(function () {
    // OSH Module Routes (to be implemented)
    Route::prefix('academic/osh')->name('academic.osh.')->group(function () {
        // OSH routes will be implemented here
        // Example:
        // Route::get('students', [OshStudentController::class, 'index'])->name('students.index');
        // Route::get('evaluations', [OshEvaluationController::class, 'index'])->name('evaluations.index');
        // Route::get('summary', [OshSummaryController::class, 'index'])->name('summary.index');
    });
    
    // Student routes for OSH overview
    Route::middleware('role:student')->prefix('student/osh')->name('student.osh.')->group(function () {
        Route::get('overview', function () {
            return view('student.osh.overview');
        })->name('overview');
    });
});

