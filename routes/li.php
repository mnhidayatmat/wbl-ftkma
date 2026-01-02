<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LI Routes (Supervisor LI)
|--------------------------------------------------------------------------
|
| Routes for Supervisor LI managing Latihan Industri (LI) students
| in Semester 8 (Phase 2).
|
| Note: Supervisor LI is different from Lecturer, AT, and IC.
| Supervisor LI only handles LI, NOT FYP, NOT PPE.
|
*/

Route::middleware(['auth', 'role:supervisor_li,admin'])->group(function () {
    // LI routes will be implemented here
    // Example:
    // Route::prefix('li')->name('li.')->group(function () {
    //     Route::get('students', [LiStudentController::class, 'index'])->name('students.index');
    //     Route::get('evaluation/{student}', [LiEvaluationController::class, 'show'])->name('evaluation.show');
    //     Route::post('evaluation/{student}', [LiEvaluationController::class, 'store'])->name('evaluation.store');
    // });
});

// Student LI Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('student/li/overview', [\App\Http\Controllers\Student\StudentLiOverviewController::class, 'index'])
        ->name('student.li.overview');
    Route::get('student/li/submissions', [\App\Http\Controllers\Student\StudentModuleSubmissionController::class, 'li'])
        ->name('student.li.submissions');
});
