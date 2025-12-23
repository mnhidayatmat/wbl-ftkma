<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IP Routes (Internship Preparation - Lecturer)
|--------------------------------------------------------------------------
|
| Routes for Lecturers managing Internship Preparation (IP) course
| for students.
|
*/

Route::middleware(['auth'])->group(function () {
    // IP Module Routes (to be implemented)
    Route::prefix('academic/ip')->name('academic.ip.')->group(function () {
        // IP routes will be implemented here
        // Example:
        // Route::get('students', [IpStudentController::class, 'index'])->name('students.index');
        // Route::get('assessments', [IpAssessmentController::class, 'index'])->name('assessments.index');
        // Route::get('summary', [IpSummaryController::class, 'index'])->name('summary.index');
    });

    // Student routes for IP overview
    Route::middleware('role:student')->prefix('student/ip')->name('student.ip.')->group(function () {
        Route::get('overview', function () {
            return view('student.ip.overview');
        })->name('overview');
    });
});
