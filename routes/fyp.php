<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FYP Routes (AT - Academic Tutor)
|--------------------------------------------------------------------------
|
| Routes for Academic Tutors (AT) managing Final Year Project (FYP)
| for students in Semester 7 (Phase 1).
|
| Note: AT is different from Lecturer (Course Lecturer).
| AT only handles FYP evaluation, not PPE/OSH/IP courses.
|
*/

Route::middleware(['auth', 'role:at,admin'])->group(function () {
    // FYP routes will be implemented here
    // Example:
    // Route::prefix('fyp')->name('fyp.')->group(function () {
    //     Route::get('students', [FypStudentController::class, 'index'])->name('students.index');
    //     Route::get('evaluation/{student}', [FypEvaluationController::class, 'show'])->name('evaluation.show');
    //     Route::post('evaluation/{student}', [FypEvaluationController::class, 'store'])->name('evaluation.store');
    // });
});

// Student FYP Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('student/fyp/overview', [\App\Http\Controllers\Student\StudentFypOverviewController::class, 'index'])
        ->name('student.fyp.overview');
    Route::get('student/fyp/submissions', [\App\Http\Controllers\Student\StudentModuleSubmissionController::class, 'fyp'])
        ->name('student.fyp.submissions');
});
