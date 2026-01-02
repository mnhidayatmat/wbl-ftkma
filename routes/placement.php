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
    Route::post('bulk-release-scl', [StudentPlacementController::class, 'bulkReleaseScl'])->name('bulk.scl.release');

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
    Route::post('company/{application}/update-offer', [StudentPlacementController::class, 'updateCompanyOffer'])->name('company.update-offer');
    Route::get('company/check', [StudentPlacementController::class, 'checkCompanyExists'])->name('company.check');
    Route::post('upload-proof', [StudentPlacementController::class, 'studentUploadProof'])->name('proof.upload');
    Route::get('view-proof', [StudentPlacementController::class, 'studentViewProof'])->name('proof.view');
    Route::get('download-sal', [StudentPlacementController::class, 'studentDownloadSal'])->name('download-sal');
    Route::get('download-scl', [StudentPlacementController::class, 'studentDownloadScl'])->name('download-scl');

    // Offer letter upload and view (ACCEPTED stage)
    Route::post('offer-letter/upload', [StudentPlacementController::class, 'uploadOfferLetter'])->name('offer-letter.upload');
    Route::get('offer-letter/view', [StudentPlacementController::class, 'viewOfferLetter'])->name('offer-letter.view');

    // Company details (ACCEPTED stage)
    Route::post('company-details', [StudentPlacementController::class, 'saveCompanyDetails'])->name('company-details.save');

    // Proceed to SCL Release (ACCEPTED stage with all requirements met)
    Route::post('proceed-scl', [StudentPlacementController::class, 'proceedToSclRelease'])->name('proceed-scl');

    // Medical checkup upload and view (SCL_RELEASED stage)
    Route::post('medical-checkup/upload', [StudentPlacementController::class, 'uploadMedicalCheckup'])->name('upload-medical-checkup');
    Route::get('medical-checkup/view', [StudentPlacementController::class, 'viewMedicalCheckup'])->name('view-medical-checkup');

    // Placement preferences (skills, interests, preferred industry, location)
    Route::post('preferences', [StudentPlacementController::class, 'updatePreferences'])->name('preferences.update');
});
