<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Lecturer\MyStudentsController as LecturerMyStudentsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\WorkplaceIssueReportController;
use App\Models\WorkplaceIssueAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Welcome page (public, no auth required)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Password Reset Routes
    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');

    // Email Verification Route (can be accessed without authentication via signed URL)
    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Resend Verification Email (for unauthenticated users)
    Route::post('verification/resend', [\App\Http\Controllers\Auth\ResendVerificationController::class, 'store'])->name('verification.resend');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email Verification Routes (require authentication)
    Route::get('verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::post('email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    // Role Switching Routes
    Route::post('role/switch', [\App\Http\Controllers\RoleSwitchController::class, 'switch'])->name('role.switch');
    Route::get('role/available', [\App\Http\Controllers\RoleSwitchController::class, 'getAvailableRoles'])->name('role.available');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Global Search
    Route::get('search', [SearchController::class, 'search'])->name('search');

    // Student Profile Routes (Student role only) - MUST be before resource route to avoid conflicts
    Route::middleware('role:student')->prefix('students')->name('students.')->group(function () {
        Route::get('profile', [StudentProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/create', [StudentProfileController::class, 'create'])->name('profile.create');
        Route::post('profile', [StudentProfileController::class, 'store'])->name('profile.store');
        Route::get('profile/{student}/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile/{student}', [StudentProfileController::class, 'update'])->name('profile.update');
    });

    Route::resource('students', StudentController::class);

    Route::resource('companies', CompanyController::class);

    // Company Management Routes
    Route::prefix('companies/{company}')->name('companies.')->group(function () {
        // Contacts
        Route::post('contacts', [CompanyController::class, 'storeContact'])->name('contacts.store');
        Route::put('contacts/{contact}', [CompanyController::class, 'updateContact'])->name('contacts.update');
        Route::delete('contacts/{contact}', [CompanyController::class, 'destroyContact'])->name('contacts.destroy');

        // Notes
        Route::post('notes', [CompanyController::class, 'storeNote'])->name('notes.store');
        Route::delete('notes/{note}', [CompanyController::class, 'destroyNote'])->name('notes.destroy');

        // Documents
        Route::post('documents', [CompanyController::class, 'storeDocument'])->name('documents.store');
        Route::get('documents/{document}/download', [CompanyController::class, 'downloadDocument'])->name('documents.download');
        Route::delete('documents/{document}', [CompanyController::class, 'destroyDocument'])->name('documents.destroy');

        // MoU
        Route::post('mou', [CompanyController::class, 'storeMou'])->name('mou.store');

        // MoA
        Route::post('moas', [CompanyController::class, 'storeMoa'])->name('moas.store');
        Route::put('moas/{moa}', [CompanyController::class, 'updateMoa'])->name('moas.update');
        Route::delete('moas/{moa}', [CompanyController::class, 'destroyMoa'])->name('moas.destroy');
    });

    // Module-specific Assign Students Routes (Admin only)
    Route::middleware('role:admin')->group(function () {
        // PPE Assign Students (Single Lecturer)
        Route::get('ppe/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('ppe.assign-students');
        Route::put('ppe/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('ppe.assign-students.update');
        Route::delete('ppe/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('ppe.assign-students.remove');

        // FYP Assign Students (Individual AT)
        Route::get('fyp/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('fyp.assign-students');
        Route::put('fyp/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('fyp.assign-students.update');
        Route::delete('fyp/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('fyp.assign-students.remove');

        // IP Assign Students (Single Lecturer)
        Route::get('ip/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('ip.assign-students');
        Route::put('ip/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('ip.assign-students.update');
        Route::delete('ip/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('ip.assign-students.remove');

        // OSH Assign Students (Single Lecturer)
        Route::get('osh/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('osh.assign-students');
        Route::put('osh/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('osh.assign-students.update');
        Route::delete('osh/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('osh.assign-students.remove');

        // Industrial Training (LI) Assign Students (Individual Supervisor)
        Route::get('industrial-training/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('li.assign-students');
        Route::put('industrial-training/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('li.assign-students.update');
        Route::delete('industrial-training/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('li.assign-students.remove');

        // IC Assign Students (Individual IC)
        Route::get('ic/assign-students', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'index'])->name('ic.assign-students');
        Route::put('ic/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'update'])->name('ic.assign-students.update');
        Route::delete('ic/assign-students/{student}', [\App\Http\Controllers\Wbl\WblAssignmentController::class, 'remove'])->name('ic.assign-students.remove');
    });

    // Lecturer Routes - My Students
    Route::middleware('role:lecturer')->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('students', [LecturerMyStudentsController::class, 'index'])->name('students.index');
    });

    // Workplace Issue Reporting Routes
    Route::prefix('workplace-issues')->name('workplace-issues.')->group(function () {
        // Public routes (all authenticated users)
        Route::get('/', [WorkplaceIssueReportController::class, 'index'])->name('index');
        Route::get('attachments/{attachment}/download', [WorkplaceIssueReportController::class, 'downloadAttachment'])
            ->name('attachments.download')
            ->whereNumber('attachment');

        // Student-only routes (create and submit reports)
        Route::get('create', [WorkplaceIssueReportController::class, 'create'])
            ->name('create')
            ->middleware('role:student');
        Route::post('/', [WorkplaceIssueReportController::class, 'store'])
            ->name('store')
            ->middleware('role:student');
        Route::post('{workplaceIssue}/feedback', [WorkplaceIssueReportController::class, 'storeFeedback'])
            ->name('feedback.store')
            ->middleware('role:student');

        // Show route (must come after static routes like 'create')
        Route::get('{workplaceIssue}', [WorkplaceIssueReportController::class, 'show'])->name('show');

        // Admin/Coordinator-only routes (update and delete)
        Route::put('{workplaceIssue}', [WorkplaceIssueReportController::class, 'update'])
            ->name('update')
            ->middleware('role:admin,coordinator');
        Route::delete('attachments/{attachment}', [WorkplaceIssueReportController::class, 'deleteAttachment'])
            ->name('attachments.delete')
            ->middleware('role:admin,coordinator')
            ->whereNumber('attachment');
    });

    // Industry Coach Routes - My Students (moved to routes/industry.php)
    // Lecturer Routes - My Students (kept here for now, will be moved to routes/academic.php later)
});

// Include module-specific route files
require __DIR__.'/academic.php';
require __DIR__.'/fyp.php';
require __DIR__.'/ip.php';
require __DIR__.'/osh.php';
require __DIR__.'/li.php';
require __DIR__.'/industry.php';
require __DIR__.'/admin.php';
require __DIR__.'/placement.php';
require __DIR__.'/resume-inspection.php';
