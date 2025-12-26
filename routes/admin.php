<?php

use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\StudentAssignmentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RecruitmentPoolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for Admin users with full system access.
|
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Lecturer Course Assignment (kept for backward compatibility if needed)
    Route::post('lecturers/assign', [StudentAssignmentController::class, 'storeLecturerAssignment'])->name('lecturers.assign.store');
    Route::delete('lecturers/assign/{assignment}', [StudentAssignmentController::class, 'removeLecturerAssignment'])->name('lecturers.assign.remove');

    // Student Course Assignment (kept for backward compatibility if needed)
    Route::put('students/{student}/course-assign', [StudentAssignmentController::class, 'updateStudentCourseAssignment'])->name('students.course-assign.update');
    Route::delete('students/course-assign/{assignment}', [StudentAssignmentController::class, 'removeStudentCourseAssignment'])->name('students.course-assign.remove');

    // Companies Management
    Route::get('companies/search', [CompanyController::class, 'search'])->name('companies.search');
    Route::resource('companies', CompanyController::class);

    // Students Management
    Route::get('students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');
    Route::post('students/preview-import', [StudentController::class, 'previewImport'])->name('students.preview-import');
    Route::post('students/confirm-import', [StudentController::class, 'confirmImport'])->name('students.confirm-import');
    Route::get('students/download-errors', [StudentController::class, 'downloadErrors'])->name('students.download-errors');
    Route::post('students/cancel-import', [StudentController::class, 'cancelImport'])->name('students.cancel-import');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::resource('students', StudentController::class);

    // Assessment Management (Admin only)
    Route::resource('assessments', AssessmentController::class);
    Route::post('assessments/{assessment}/toggle-active', [AssessmentController::class, 'toggleActive'])->name('assessments.toggle-active');

    // User Roles Management
    Route::get('users/roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'index'])->name('users.roles.index');
    Route::put('users/roles/{user}/update-roles', [\App\Http\Controllers\Admin\UserRoleController::class, 'updateRoles'])->name('users.roles.update-roles');
    Route::delete('users/roles/{user}', [\App\Http\Controllers\Admin\UserRoleController::class, 'destroy'])->name('users.roles.destroy');

    // User Access / Permissions Management
    Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/update', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');
    Route::post('permissions/bulk-update', [\App\Http\Controllers\Admin\PermissionController::class, 'bulkUpdate'])->name('permissions.bulk-update');
    Route::post('permissions/bulk-update-module', [\App\Http\Controllers\Admin\PermissionController::class, 'bulkUpdateModule'])->name('permissions.bulk-update-module');
    Route::post('permissions/grant-all', [\App\Http\Controllers\Admin\PermissionController::class, 'grantAll'])->name('permissions.grant-all');
    Route::post('permissions/set-view-only', [\App\Http\Controllers\Admin\PermissionController::class, 'setViewOnly'])->name('permissions.set-view-only');
    Route::post('permissions/revoke-all', [\App\Http\Controllers\Admin\PermissionController::class, 'revokeAll'])->name('permissions.revoke-all');

    // Groups Management
    Route::resource('groups', \App\Http\Controllers\GroupController::class);
    Route::post('groups/{group}/mark-completed', [\App\Http\Controllers\GroupController::class, 'markCompleted'])->name('groups.mark-completed');
    Route::post('groups/{group}/reopen', [\App\Http\Controllers\GroupController::class, 'reopen'])->name('groups.reopen');

    // Company Agreements Management (MoU/MoA/LOI)
    Route::get('agreements', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'index'])->name('agreements.index');
    Route::get('agreements/create', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'create'])->name('agreements.create');
    Route::post('agreements', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'store'])->name('agreements.store');
    Route::get('agreements/import', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'importForm'])->name('agreements.import');
    Route::post('agreements/import/preview', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'importPreview'])->name('agreements.import.preview');
    Route::post('agreements/import/execute', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'importExecute'])->name('agreements.import.execute');
    Route::get('agreements/template', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'downloadTemplate'])->name('agreements.template');
    Route::get('agreements/{agreement}', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'show'])->name('agreements.show');
    Route::get('agreements/{agreement}/edit', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'edit'])->name('agreements.edit');
    Route::put('agreements/{agreement}', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'update'])->name('agreements.update');
    Route::delete('agreements/{agreement}', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'destroy'])->name('agreements.destroy');
    Route::patch('agreements/{agreement}/status', [\App\Http\Controllers\Admin\CompanyAgreementController::class, 'updateStatus'])->name('agreements.update-status');
});

/*
|--------------------------------------------------------------------------
| Recruitment Routes (Admin & Coordinator)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('recruitment')->name('recruitment.')->group(function () {
    // Recruitment Pool - filtering and management
    Route::get('/pool', [RecruitmentPoolController::class, 'index'])->name('pool.index')
        ->middleware('role:admin,coordinator');

    // Export routes
    Route::post('/export/excel', [RecruitmentPoolController::class, 'exportExcel'])->name('export.excel')
        ->middleware('role:admin,coordinator');
    Route::post('/export/pdf', [RecruitmentPoolController::class, 'exportPdf'])->name('export.pdf')
        ->middleware('role:admin,coordinator');
    Route::post('/export/resumes', [RecruitmentPoolController::class, 'downloadResumes'])->name('export.resumes')
        ->middleware('role:admin,coordinator');

    // Email handover
    Route::post('/email-recruiter', [RecruitmentPoolController::class, 'emailToRecruiter'])->name('email-recruiter')
        ->middleware('role:admin,coordinator');

    // Handover history
    Route::get('/handovers', [RecruitmentPoolController::class, 'handovers'])->name('handovers.index')
        ->middleware('role:admin,coordinator');
});
