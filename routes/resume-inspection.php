<?php

use App\Http\Controllers\StudentResumeInspectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Resume Inspection Routes
|--------------------------------------------------------------------------
|
| Routes for Student Resume Inspection module.
| Students can upload resume and portfolio.
| Coordinators/Admins can review and approve/reject.
|
*/

Route::middleware(['auth'])->group(function () {
    // Student Routes
    Route::middleware('role:student')->prefix('student/resume-inspection')->name('student.resume.')->group(function () {
        Route::get('/', [StudentResumeInspectionController::class, 'studentIndex'])->name('index');
        Route::post('save-checklist', [StudentResumeInspectionController::class, 'saveChecklist'])->name('save-checklist');
        Route::post('upload-document', [StudentResumeInspectionController::class, 'studentUploadDocument'])->name('upload-document');
        Route::post('reply', [StudentResumeInspectionController::class, 'studentReply'])->name('reply');
        Route::delete('delete-document', [StudentResumeInspectionController::class, 'studentDeleteDocument'])->name('delete-document');
        Route::get('view-document/{inspection}', [StudentResumeInspectionController::class, 'viewDocument'])->name('view-document');
        Route::get('download-document/{inspection}', [StudentResumeInspectionController::class, 'downloadDocument'])->name('download-document');
        Route::get('sample/{sample}', [StudentResumeInspectionController::class, 'downloadSample'])->name('sample');
    });

    // Coordinator/Admin Routes
    Route::middleware('role:admin,coordinator')->prefix('coordinator/resume-inspection')->name('coordinator.resume.')->group(function () {
        Route::get('/', [StudentResumeInspectionController::class, 'coordinatorIndex'])->name('index');
        Route::get('review/{inspection}', [StudentResumeInspectionController::class, 'coordinatorReview'])->name('review');
        Route::post('review/{inspection}', [StudentResumeInspectionController::class, 'review'])->name('review.submit');
        Route::post('reset/{inspection}', [StudentResumeInspectionController::class, 'reset'])->name('reset');
        Route::get('view-document/{inspection}', [StudentResumeInspectionController::class, 'viewDocument'])->name('view-document');
        Route::get('download-document/{inspection}', [StudentResumeInspectionController::class, 'downloadDocument'])->name('download-document');
    });
});
