<?php

use App\Http\Controllers\Academic\FYP\FypScheduleController;
use App\Http\Controllers\Academic\IP\IpIcEvaluationController;
use App\Http\Controllers\Academic\LI\LiIcEvaluationController;
use App\Http\Controllers\Academic\LI\LiScheduleController;
use App\Http\Controllers\Academic\LI\LiSupervisorEvaluationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentPlacementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Debug route to check PHP settings
Route::get('/debug-php-settings', function () {
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403);
    }

    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
    ]);
})->name('debug.php.settings');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    // Student Placement Routes
    Route::prefix('placement')->name('placement.')->group(function () {
        Route::get('/', [StudentPlacementController::class, 'index'])->name('index');
        Route::get('/student', [StudentPlacementController::class, 'studentIndex'])->name('student.index');
        Route::post('/store', [StudentPlacementController::class, 'store'])->name('store');

        // Student Actions
        Route::post('/company/store', [StudentPlacementController::class, 'storeCompany'])->name('company.store');
        Route::post('/company/link', [StudentPlacementController::class, 'linkCompany'])->name('company.link');
        Route::post('/company/{company}/request-edit', [StudentPlacementController::class, 'requestCompanyEdit'])->name('company.request-edit');

        // Coordinator Actions (AJAX)
        Route::post('/{student}/at/{user}', [StudentPlacementController::class, 'assignAt'])->name('assign.at');
        Route::post('/{student}/ic/{user}', [StudentPlacementController::class, 'assignIc'])->name('assign.ic');
        Route::delete('/{student}/at', [StudentPlacementController::class, 'unassignAt'])->name('unassign.at');
        Route::delete('/{student}/ic', [StudentPlacementController::class, 'unassignIc'])->name('unassign.ic');

        // Student-side coordinator assignment (POST)
        Route::post('/request-coordinator', [StudentPlacementController::class, 'requestCoordinator'])->name('request.coordinator');

        // Edit Requests (Coordinator)
        Route::get('/edit-requests', [StudentPlacementController::class, 'editRequests'])->name('edit.requests');
        Route::post('/edit-requests/{request}/approve', [StudentPlacementController::class, 'approveEditRequest'])->name('edit.approve');
        Route::post('/edit-requests/{request}/reject', [StudentPlacementController::class, 'rejectEditRequest'])->name('edit.reject');

        // Company Management
        Route::post('/company/{company}/approve', [StudentPlacementController::class, 'approveCompany'])->name('company.approve');
        Route::post('/company/{company}/reject', [StudentPlacementController::class, 'rejectCompany'])->name('company.reject');

        // Bulk update student profile
        Route::post('/update-profile', [StudentPlacementController::class, 'updateProfile'])->name('update.profile');

        // Approve IC
        Route::post('/ic/{user}/approve', [StudentPlacementController::class, 'approveIc'])->name('ic.approve');
    });

    // PPE Routes
    Route::get('/ppe/student', [\App\Http\Controllers\Academic\PPE\PpeStudentController::class, 'index'])
        ->name('ppe.student.index');

    Route::post('/ppe/student/at-marks', [\App\Http\Controllers\Academic\PPE\PpeStudentController::class, 'submitAtMarks'])
        ->name('ppe.student.at-marks.store');

    Route::post('/ppe/student/ic-marks', [\App\Http\Controllers\Academic\PPE\PpeStudentController::class, 'submitIcMarks'])
        ->name('ppe.student.ic-marks.store');

    // FYP Schedule Routes
    Route::prefix('fyp/schedules')->name('fyp.schedules.')->group(function () {
        Route::get('/', [FypScheduleController::class, 'index'])->name('index');
        Route::get('/create', [FypScheduleController::class, 'create'])->name('create');
        Route::post('/', [FypScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}', [FypScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [FypScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [FypScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [FypScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{schedule}/finalize', [FypScheduleController::class, 'finalize'])->name('finalize');
    });

    // IP IC Evaluation Routes
    Route::prefix('ip/ic-evaluation')->name('ip.ic-evaluation.')->group(function () {
        Route::get('/', [IpIcEvaluationController::class, 'index'])->name('index');
        Route::get('/{student}', [IpIcEvaluationController::class, 'show'])->name('show');
        Route::post('/{student}', [IpIcEvaluationController::class, 'store'])->name('store');
    });

    // LI Schedule Routes
    Route::prefix('li/schedules')->name('li.schedules.')->group(function () {
        Route::get('/', [LiScheduleController::class, 'index'])->name('index');
        Route::get('/create', [LiScheduleController::class, 'create'])->name('create');
        Route::post('/', [LiScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}', [LiScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [LiScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [LiScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [LiScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{schedule}/finalize', [LiScheduleController::class, 'finalize'])->name('finalize');
    });

    // LI IC Evaluation Routes
    Route::prefix('li/ic-evaluation')->name('li.ic-evaluation.')->group(function () {
        Route::get('/', [LiIcEvaluationController::class, 'index'])->name('index');
        Route::get('/{student}', [LiIcEvaluationController::class, 'show'])->name('show');
        Route::post('/{student}', [LiIcEvaluationController::class, 'store'])->name('store');
    });

    // LI Supervisor Evaluation Routes
    Route::prefix('li/supervisor-evaluation')->name('li.supervisor-evaluation.')->group(function () {
        Route::get('/', [LiSupervisorEvaluationController::class, 'index'])->name('index');
        Route::get('/{student}', [LiSupervisorEvaluationController::class, 'show'])->name('show');
        Route::post('/{student}', [LiSupervisorEvaluationController::class, 'store'])->name('store');
    });

    // AJAX endpoint for WBL module switching based on student matric
    Route::get('/api/student/{matricNumber}/wbl-module', function ($matricNumber) {
        $student = \App\Models\Student::where('matric_number', $matricNumber)->first();

        if (! $student) {
            return response()->json(['wbl_module' => null], 404);
        }

        return response()->json(['wbl_module' => $student->wbl_module]);
    })->name('api.student.wbl-module');

    // Attachments
    Route::get('/attachments/{attachmentId}', function ($attachmentId) {
        $attachment = \App\Models\Attachment::findOrFail($attachmentId);

        // Check if user has permission to view this attachment
        // For now, just check if they're authenticated
        if (! \Illuminate\Support\Facades\Storage::exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return \Illuminate\Support\Facades\Storage::download($attachment->file_path, $attachment->file_name);
    })->middleware('auth')
        ->whereNumber('attachment');

    // Reference Samples Routes
    Route::prefix('reference-samples')->name('reference-samples.')->group(function () {
        // Student view (download only)
        Route::get('student', [\App\Http\Controllers\ReferenceSampleController::class, 'studentIndex'])->name('student');
        Route::get('{referenceSample}/download', [\App\Http\Controllers\ReferenceSampleController::class, 'download'])->name('download');

        // Admin/Coordinator management routes
        Route::middleware('role:admin,coordinator')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReferenceSampleController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\ReferenceSampleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ReferenceSampleController::class, 'store'])->name('store');
            Route::get('{referenceSample}', [\App\Http\Controllers\ReferenceSampleController::class, 'show'])->name('show');
            Route::get('{referenceSample}/edit', [\App\Http\Controllers\ReferenceSampleController::class, 'edit'])->name('edit');
            Route::put('{referenceSample}', [\App\Http\Controllers\ReferenceSampleController::class, 'update'])->name('update');
            Route::delete('{referenceSample}', [\App\Http\Controllers\ReferenceSampleController::class, 'destroy'])->name('destroy');
            Route::post('{referenceSample}/toggle-status', [\App\Http\Controllers\ReferenceSampleController::class, 'toggleStatus'])->name('toggle-status');
        });
    });
});
