<?php

use App\Http\Controllers\StudentPlacementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Placement Tracking Routes
|--------------------------------------------------------------------------
|
| Routes for managing student placement tracking before and after WBL hiring.
| Access: Admin, Coordinator (full), Lecturer/AT/IC/Supervisor LI (read-only)
|
*/

Route::middleware(['auth'])->prefix('placement')->name('placement.')->group(function () {
    // Group overview
    Route::get('/', [StudentPlacementController::class, 'index'])->name('index');

    // Group detail view
    Route::get('group/{group}', [StudentPlacementController::class, 'showGroup'])->name('group.show');

    // View student placement tracking (Admin & Coordinator can view any student)
    Route::get('student/{student}/view', [StudentPlacementController::class, 'viewStudentPlacement'])->name('student.view');

    // Status updates (students can update their own, admin/coordinator can update any)
    Route::post('student/{student}/status', [StudentPlacementController::class, 'updateStatus'])->name('student.status.update');

    // SAL operations (Admin & Coordinator only)
    Route::post('student/{student}/release-sal', [StudentPlacementController::class, 'releaseSal'])->name('student.sal.release');
    Route::post('bulk-release-sal', [StudentPlacementController::class, 'bulkReleaseSal'])->name('bulk.sal.release');

    // SCL operations (Admin & Coordinator only)
    Route::post('student/{student}/release-scl', [StudentPlacementController::class, 'releaseScl'])->name('student.scl.release');

    // Proof upload (students can upload their own, admin/coordinator can upload any)
    Route::post('student/{student}/upload-proof', [StudentPlacementController::class, 'uploadProof'])->name('student.proof.upload');

    // Downloads (accessible by student, admin, coordinator, and read-only roles)
    Route::get('student/{student}/download-sal', [StudentPlacementController::class, 'downloadSal'])->name('student.sal.download');
    Route::get('student/{student}/download-scl', [StudentPlacementController::class, 'downloadScl'])->name('student.scl.download');
    Route::get('student/{student}/view-proof', [StudentPlacementController::class, 'viewProof'])->name('student.proof.view');

    // Reset placement tracking (Admin only)
    Route::post('student/{student}/reset', [StudentPlacementController::class, 'reset'])->name('student.reset')->middleware('role:admin');
});

// Student-specific placement tracking routes
Route::middleware('auth')->prefix('student/placement')->name('student.placement.')->group(function () {
    Route::get('/', [StudentPlacementController::class, 'studentView'])->name('index');
    Route::post('status', [StudentPlacementController::class, 'studentUpdateStatus'])->name('status.update');
    Route::post('company/add', [StudentPlacementController::class, 'addCompany'])->name('company.add');
    Route::delete('company/{application}', [StudentPlacementController::class, 'deleteCompany'])->name('company.delete');
    Route::post('company/{application}/got-interview', [StudentPlacementController::class, 'markAsInterviewed'])->name('company.got-interview');
    Route::post('company/{application}/update-interview', [StudentPlacementController::class, 'updateCompanyInterview'])->name('company.update-interview');
    Route::post('company/{application}/update-follow-up', [StudentPlacementController::class, 'updateCompanyFollowUp'])->name('company.update-follow-up');
    Route::post('upload-proof', [StudentPlacementController::class, 'studentUploadProof'])->name('proof.upload');
    Route::get('view-proof', [StudentPlacementController::class, 'studentViewProof'])->name('proof.view');
    Route::get('download-sal', [StudentPlacementController::class, 'studentDownloadSal'])->name('download-sal');
    Route::get('download-scl', [StudentPlacementController::class, 'studentDownloadScl'])->name('download-scl');
});
