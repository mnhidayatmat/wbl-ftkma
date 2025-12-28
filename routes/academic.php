<?php

use App\Http\Controllers\Academic\AssessmentController;
use App\Http\Controllers\Academic\FYP\FypAtEvaluationController;
use App\Http\Controllers\Academic\FYP\FypAuditController;
use App\Http\Controllers\Academic\FYP\FypFinalisationController;
use App\Http\Controllers\Academic\FYP\FypIcEvaluationController;
use App\Http\Controllers\Academic\FYP\FypLogbookController;
use App\Http\Controllers\Academic\FYP\FypModerationController;
use App\Http\Controllers\Academic\FYP\FypProgressController;
use App\Http\Controllers\Academic\FYP\FypReportsController;
use App\Http\Controllers\Academic\FYP\FypRubricController;
use App\Http\Controllers\Academic\FYP\FypRubricEvaluationController;
use App\Http\Controllers\Academic\FYP\FypScheduleController;
use App\Http\Controllers\Academic\FYP\FypStudentPerformanceController;
use App\Http\Controllers\Academic\IP\IpAuditController;
use App\Http\Controllers\Academic\IP\IpIcEvaluationController;
use App\Http\Controllers\Academic\IP\IpLecturerEvaluationController;
use App\Http\Controllers\Academic\IP\IpLogbookController;
use App\Http\Controllers\Academic\IP\IpModerationController;
use App\Http\Controllers\Academic\IP\IpReportsController;
use App\Http\Controllers\Academic\IP\IpScheduleController;
use App\Http\Controllers\Academic\IP\IpStudentPerformanceController;
use App\Http\Controllers\Academic\LI\LiIcEvaluationController;
use App\Http\Controllers\Academic\LI\LiLogbookController;
use App\Http\Controllers\Academic\LI\LiStudentPerformanceController;
use App\Http\Controllers\Academic\LI\LiSupervisorEvaluationController;
use App\Http\Controllers\Academic\OSH\OshAtEvaluationController;
use App\Http\Controllers\Academic\OSH\OshAuditController;
use App\Http\Controllers\Academic\OSH\OshFinalisationController;
use App\Http\Controllers\Academic\OSH\OshIcEvaluationController;
use App\Http\Controllers\Academic\OSH\OshLogbookController;
use App\Http\Controllers\Academic\OSH\OshModerationController;
use App\Http\Controllers\Academic\OSH\OshProgressController;
use App\Http\Controllers\Academic\OSH\OshReportsController;
use App\Http\Controllers\Academic\OSH\OshScheduleController;
use App\Http\Controllers\Academic\OSH\OshStudentPerformanceController;
use App\Http\Controllers\Academic\PPE\PpeAtEvaluationController;
use App\Http\Controllers\Academic\PPE\PpeAuditController;
use App\Http\Controllers\Academic\PPE\PpeFinalisationController;
use App\Http\Controllers\Academic\PPE\PpeFinalScoreController;
use App\Http\Controllers\Academic\PPE\PpeGroupSelectionController;
use App\Http\Controllers\Academic\PPE\PpeIcEvaluationController;
use App\Http\Controllers\Academic\PPE\PpeLogbookController;
use App\Http\Controllers\Academic\PPE\PpeModerationController;
use App\Http\Controllers\Academic\PPE\PpeProgressController;
use App\Http\Controllers\Academic\PPE\PpeReportsController;
use App\Http\Controllers\Academic\PPE\PpeScheduleController;
use App\Http\Controllers\Academic\PPE\PpeStudentPerformanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Academic Routes (Lecturer for PPE, OSH, IP courses)
|--------------------------------------------------------------------------
|
| Routes for Course Lecturers to evaluate academic courses:
| - PPE (Professional Practice & Ethics)
| - OSH (Occupational Safety & Health)
| - IP (Internship Preparation)
|
*/

Route::middleware(['auth'])->group(function () {
    // PPE Module Routes
    Route::prefix('academic/ppe')->name('academic.ppe.')->group(function () {
        // Group Selection (All authenticated users)
        Route::get('groups', [PpeGroupSelectionController::class, 'index'])->name('groups.index');

        // Assessments (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('assessments', function () {
                return app(AssessmentController::class)->index(request(), 'PPE');
            })->name('assessments.index');
            Route::get('assessments/create', function () {
                return app(AssessmentController::class)->create(request(), 'PPE');
            })->name('assessments.create');
            Route::post('assessments', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->store($request, 'PPE');
            })->name('assessments.store');
            Route::get('assessments/{assessment}/edit', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->edit(request(), $assessment, 'PPE');
            })->name('assessments.edit');
            Route::put('assessments/{assessment}', function (\Illuminate\Http\Request $request, \App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->update($request, $assessment, 'PPE');
            })->name('assessments.update');
            Route::delete('assessments/{assessment}', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->destroy($assessment, 'PPE');
            })->name('assessments.destroy');
            Route::post('assessments/{assessment}/toggle-active', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->toggleActive($assessment, 'PPE');
            })->name('assessments.toggle-active');
        });

        // Assessment Schedule (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('schedule', [PpeScheduleController::class, 'index'])->name('schedule.index');
            Route::post('schedule/window', [PpeScheduleController::class, 'updateWindow'])->name('schedule.update-window');
            Route::post('schedule/reminder', [PpeScheduleController::class, 'sendReminder'])->name('schedule.send-reminder');
        });

        // Evaluation Progress (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('progress', [PpeProgressController::class, 'index'])->name('progress.index');
        });

        // Moderation (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('moderation', [PpeModerationController::class, 'index'])->name('moderation.index');
            Route::get('moderation/{student}', [PpeModerationController::class, 'show'])->name('moderation.show');
            Route::post('moderation/{student}', [PpeModerationController::class, 'store'])->name('moderation.store');
        });

        // Result Finalisation (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('finalisation', [PpeFinalisationController::class, 'index'])->name('finalisation.index');
            Route::post('finalisation/student/{student}', [PpeFinalisationController::class, 'finaliseStudent'])->name('finalisation.student');
            Route::post('finalisation/group', [PpeFinalisationController::class, 'finaliseGroup'])->name('finalisation.group');
            Route::post('finalisation/course', [PpeFinalisationController::class, 'finaliseCourse'])->name('finalisation.course');
        });

        // Reports (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('reports', [PpeReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/cohort', [PpeReportsController::class, 'exportCohort'])->name('reports.cohort');
            Route::get('reports/group/{group}', [PpeReportsController::class, 'exportGroup'])->name('reports.group');
            Route::get('reports/company/{company}', [PpeReportsController::class, 'exportCompany'])->name('reports.company');
        });

        // Audit Log (Admin and PPE Coordinator)
        Route::middleware('role:admin,ppe_coordinator')->group(function () {
            Route::get('audit', [PpeAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/export', [PpeAuditController::class, 'export'])->name('audit.export');
        });

        // CLO-PLO Analysis (Admin, Coordinator, Lecturer, PPE Coordinator)
        Route::middleware('role:admin,coordinator,lecturer,ppe_coordinator')->group(function () {
            Route::get('clo-plo', [\App\Http\Controllers\Academic\PPE\PpeCloPloController::class, 'index'])->name('clo-plo.index');
            Route::post('clo-plo', [\App\Http\Controllers\Academic\PPE\PpeCloPloController::class, 'store'])->name('clo-plo.store');
            Route::post('clo-plo/update-count', [\App\Http\Controllers\Academic\PPE\PpeCloPloController::class, 'updateCount'])->name('clo-plo.update-count');
            Route::put('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\PPE\PpeCloPloController::class, 'update'])->name('clo-plo.update');
            Route::delete('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\PPE\PpeCloPloController::class, 'destroy'])->name('clo-plo.destroy');
        });

        // Legacy Settings route (redirect to schedule)
        Route::middleware('role:admin')->group(function () {
            Route::get('settings', function () {
                return redirect()->route('academic.ppe.schedule.index');
            })->name('settings.index');
        });

        // Lecturer Evaluation (Admin, Lecturer, and PPE Coordinator - authorization checked in controller)
        Route::middleware('role:admin,lecturer,ppe_coordinator')->group(function () {
            Route::get('lecturer', [PpeAtEvaluationController::class, 'index'])->name('lecturer.index');
            Route::get('lecturer/{student}', [PpeAtEvaluationController::class, 'show'])->name('lecturer.show');
            Route::post('lecturer/{student}', [PpeAtEvaluationController::class, 'store'])->name('lecturer.store');

            // Student Performance Overview
            Route::get('performance', [PpeStudentPerformanceController::class, 'index'])->name('performance.index');

            // Export routes (Admin and PPE Coordinator)
            Route::middleware('role:admin,ppe_coordinator')->group(function () {
                Route::get('performance/export/excel', [PpeStudentPerformanceController::class, 'exportExcel'])->name('performance.export.excel');
                Route::get('performance/export/pdf', [PpeStudentPerformanceController::class, 'exportPdf'])->name('performance.export.pdf');
            });
        });

        // IC Evaluation (Admin, Industry Coach, and PPE Coordinator - authorization checked in controller)
        Route::middleware('role:admin,industry,ppe_coordinator')->group(function () {
            Route::get('ic', [PpeIcEvaluationController::class, 'index'])->name('ic.index');
            Route::get('ic/{student}', [PpeIcEvaluationController::class, 'show'])->name('ic.show');
            Route::post('ic/{student}', [PpeIcEvaluationController::class, 'store'])->name('ic.store');
        });

        // Logbook Evaluation (Admin, Industry Coach, and PPE Coordinator)
        Route::middleware('role:admin,industry,ppe_coordinator')->prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/', [PpeLogbookController::class, 'index'])->name('index');
            Route::get('/{student}', [PpeLogbookController::class, 'show'])->name('show');
            Route::post('/{student}', [PpeLogbookController::class, 'store'])->name('store');
        });

        // Final Scores (All authenticated users can view)
        Route::get('final', [PpeFinalScoreController::class, 'index'])->name('final.index');
        Route::get('final/{student}', [PpeFinalScoreController::class, 'show'])->name('final.show');
    });

    // IP Module Routes
    Route::prefix('academic/ip')->name('academic.ip.')->group(function () {
        // Assessments (Admin and IP Coordinator)
        Route::middleware('role:admin,ip_coordinator')->group(function () {
            Route::get('assessments', function () {
                return app(AssessmentController::class)->index(request(), 'IP');
            })->name('assessments.index');
            Route::get('assessments/create', function () {
                return app(AssessmentController::class)->create(request(), 'IP');
            })->name('assessments.create');
            Route::post('assessments', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->store($request, 'IP');
            })->name('assessments.store');
            Route::get('assessments/{assessment}/edit', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->edit(request(), $assessment, 'IP');
            })->name('assessments.edit');
            Route::put('assessments/{assessment}', function (\Illuminate\Http\Request $request, \App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->update($request, $assessment, 'IP');
            })->name('assessments.update');
            Route::delete('assessments/{assessment}', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->destroy($assessment, 'IP');
            })->name('assessments.destroy');
            Route::post('assessments/{assessment}/toggle-active', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->toggleActive($assessment, 'IP');
            })->name('assessments.toggle-active');

            // Assessment Schedule (Admin only)
            Route::get('schedule', [IpScheduleController::class, 'index'])->name('schedule.index');
            Route::post('schedule/window', [IpScheduleController::class, 'updateWindow'])->name('schedule.update-window');
            Route::post('schedule/reminder', [IpScheduleController::class, 'sendReminder'])->name('schedule.send-reminder');

            // Progress, Moderation, Finalisation, Reports, Audit
            Route::get('progress', [\App\Http\Controllers\Academic\IP\IpProgressController::class, 'index'])->name('progress.index');
            Route::get('moderation', [IpModerationController::class, 'index'])->name('moderation.index');
            Route::get('moderation/{student}', [IpModerationController::class, 'show'])->name('moderation.show');
            Route::post('moderation/{student}', [IpModerationController::class, 'store'])->name('moderation.store');

            // Result Finalisation (Admin only)
            Route::get('finalisation', [\App\Http\Controllers\Academic\IP\IpFinalisationController::class, 'index'])->name('finalisation.index');
            Route::post('finalisation/student/{student}', [\App\Http\Controllers\Academic\IP\IpFinalisationController::class, 'finaliseStudent'])->name('finalisation.student');
            Route::post('finalisation/group', [\App\Http\Controllers\Academic\IP\IpFinalisationController::class, 'finaliseGroup'])->name('finalisation.group');
            Route::post('finalisation/course', [\App\Http\Controllers\Academic\IP\IpFinalisationController::class, 'finaliseCourse'])->name('finalisation.course');
            Route::get('reports', [IpReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/cohort', [IpReportsController::class, 'exportCohort'])->name('reports.cohort');
            Route::get('reports/group/{group}', [IpReportsController::class, 'exportGroup'])->name('reports.group');
            Route::get('reports/company/{company}', [IpReportsController::class, 'exportCompany'])->name('reports.company');
            Route::get('audit', [IpAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/export', [IpAuditController::class, 'export'])->name('audit.export');
        });

        // CLO-PLO Analysis (Admin, Coordinator, Lecturer, IP Coordinator)
        Route::middleware('role:admin,coordinator,lecturer,ip_coordinator')->group(function () {
            Route::get('clo-plo', [\App\Http\Controllers\Academic\IP\IpCloPloController::class, 'index'])->name('clo-plo.index');
            Route::post('clo-plo', [\App\Http\Controllers\Academic\IP\IpCloPloController::class, 'store'])->name('clo-plo.store');
            Route::post('clo-plo/update-count', [\App\Http\Controllers\Academic\IP\IpCloPloController::class, 'updateCount'])->name('clo-plo.update-count');
            Route::put('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\IP\IpCloPloController::class, 'update'])->name('clo-plo.update');
            Route::delete('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\IP\IpCloPloController::class, 'destroy'])->name('clo-plo.destroy');
        });

        // Lecturer Evaluation (Admin, Lecturer, and IP Coordinator)
        Route::middleware('role:admin,lecturer,ip_coordinator')->group(function () {
            Route::get('lecturer', [IpLecturerEvaluationController::class, 'index'])->name('lecturer.index');
            Route::get('lecturer/{student}', [IpLecturerEvaluationController::class, 'show'])->name('lecturer.show');
            Route::post('lecturer/{student}', [IpLecturerEvaluationController::class, 'store'])->name('lecturer.store');
            Route::get('performance', [IpStudentPerformanceController::class, 'index'])->name('performance.index');

            // Export routes (Admin and IP Coordinator)
            Route::middleware('role:admin,ip_coordinator')->group(function () {
                Route::get('performance/export/excel', [IpStudentPerformanceController::class, 'exportExcel'])->name('performance.export.excel');
                Route::get('performance/export/pdf', [IpStudentPerformanceController::class, 'exportPdf'])->name('performance.export.pdf');
            });
        });

        // AT Evaluation (Admin, AT, and IP Coordinator)
        Route::middleware('role:admin,lecturer,ip_coordinator')->group(function () {
            Route::get('at', [\App\Http\Controllers\Academic\IP\IpAtEvaluationController::class, 'index'])->name('at.index');
            Route::get('at/{student}', [\App\Http\Controllers\Academic\IP\IpAtEvaluationController::class, 'show'])->name('at.show');
            Route::post('at/{student}', [\App\Http\Controllers\Academic\IP\IpAtEvaluationController::class, 'store'])->name('at.store');
        });

        // IC Evaluation (Admin, Industry Coach, and IP Coordinator - authorization checked in controller)
        Route::middleware('role:admin,industry,ip_coordinator')->group(function () {
            Route::get('ic', [IpIcEvaluationController::class, 'index'])->name('ic.index');
            Route::get('ic/{student}', [IpIcEvaluationController::class, 'show'])->name('ic.show');
            Route::post('ic/{student}', [IpIcEvaluationController::class, 'store'])->name('ic.store');
            Route::get('ic/{student}/rubric/{assessment}', [IpIcEvaluationController::class, 'rubric'])->name('ic.rubric');
            Route::post('ic/{student}/rubric/{assessment}', [IpIcEvaluationController::class, 'storeRubric'])->name('ic.rubric.store');
        });

        // Logbook Evaluation (Admin, Industry Coach, and IP Coordinator)
        Route::middleware('role:admin,industry,ip_coordinator')->prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/', [IpLogbookController::class, 'index'])->name('index');
            Route::get('/{student}', [IpLogbookController::class, 'show'])->name('show');
            Route::post('/{student}', [IpLogbookController::class, 'store'])->name('store');
        });
    });

    // OSH Module Routes
    Route::prefix('academic/osh')->name('academic.osh.')->group(function () {
        // Assessments (Admin and OSH Coordinator)
        Route::middleware('role:admin,osh_coordinator')->group(function () {
            Route::get('assessments', function () {
                return app(AssessmentController::class)->index(request(), 'OSH');
            })->name('assessments.index');
            Route::get('assessments/create', function () {
                return app(AssessmentController::class)->create(request(), 'OSH');
            })->name('assessments.create');
            Route::post('assessments', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->store($request, 'OSH');
            })->name('assessments.store');
            Route::get('assessments/{assessment}/edit', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->edit(request(), $assessment, 'OSH');
            })->name('assessments.edit');
            Route::put('assessments/{assessment}', function (\Illuminate\Http\Request $request, \App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->update($request, $assessment, 'OSH');
            })->name('assessments.update');
            Route::delete('assessments/{assessment}', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->destroy($assessment, 'OSH');
            })->name('assessments.destroy');
            Route::post('assessments/{assessment}/toggle-active', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->toggleActive($assessment, 'OSH');
            })->name('assessments.toggle-active');

            // Assessment Schedule (Admin only)
            Route::get('schedule', [OshScheduleController::class, 'index'])->name('schedule.index');
            Route::post('schedule/window', [OshScheduleController::class, 'updateWindow'])->name('schedule.update-window');
            Route::post('schedule/reminder', [OshScheduleController::class, 'sendReminder'])->name('schedule.send-reminder');

            // Progress, Moderation, Finalisation, Reports, Audit
            Route::get('progress', [OshProgressController::class, 'index'])->name('progress.index');
            Route::get('moderation', [OshModerationController::class, 'index'])->name('moderation.index');
            Route::get('moderation/{student}', [OshModerationController::class, 'show'])->name('moderation.show');
            Route::post('moderation/{student}', [OshModerationController::class, 'store'])->name('moderation.store');
            Route::get('finalisation', [OshFinalisationController::class, 'index'])->name('finalisation.index');
            Route::post('finalisation/student/{student}', [OshFinalisationController::class, 'finaliseStudent'])->name('finalisation.student');
            Route::post('finalisation/group', [OshFinalisationController::class, 'finaliseGroup'])->name('finalisation.group');
            Route::post('finalisation/course', [OshFinalisationController::class, 'finaliseCourse'])->name('finalisation.course');
            Route::get('reports', [OshReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/cohort', [OshReportsController::class, 'exportCohort'])->name('reports.cohort');
            Route::get('reports/group/{group}', [OshReportsController::class, 'exportGroup'])->name('reports.group');
            Route::get('reports/company/{company}', [OshReportsController::class, 'exportCompany'])->name('reports.company');
            Route::get('audit', [OshAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/export', [OshAuditController::class, 'export'])->name('audit.export');
        });

        // CLO-PLO Analysis (Admin, Coordinator, Lecturer, OSH Coordinator)
        Route::middleware('role:admin,coordinator,lecturer,osh_coordinator')->group(function () {
            Route::get('clo-plo', [\App\Http\Controllers\Academic\OSH\OshCloPloController::class, 'index'])->name('clo-plo.index');
            Route::post('clo-plo', [\App\Http\Controllers\Academic\OSH\OshCloPloController::class, 'store'])->name('clo-plo.store');
            Route::post('clo-plo/update-count', [\App\Http\Controllers\Academic\OSH\OshCloPloController::class, 'updateCount'])->name('clo-plo.update-count');
            Route::put('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\OSH\OshCloPloController::class, 'update'])->name('clo-plo.update');
            Route::delete('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\OSH\OshCloPloController::class, 'destroy'])->name('clo-plo.destroy');
        });

        // Lecturer Evaluation (Admin, Lecturer, and OSH Coordinator)
        Route::middleware('role:admin,lecturer,osh_coordinator')->group(function () {
            Route::get('lecturer', [OshAtEvaluationController::class, 'index'])->name('lecturer.index');
            Route::get('lecturer/{student}', [OshAtEvaluationController::class, 'show'])->name('lecturer.show');
            Route::post('lecturer/{student}', [OshAtEvaluationController::class, 'store'])->name('lecturer.store');

            // Student Performance Overview
            Route::get('performance', [OshStudentPerformanceController::class, 'index'])->name('performance.index');

            // Export routes (Admin and OSH Coordinator)
            Route::middleware('role:admin,osh_coordinator')->group(function () {
                Route::get('performance/export/excel', [OshStudentPerformanceController::class, 'exportExcel'])->name('performance.export.excel');
                Route::get('performance/export/pdf', [OshStudentPerformanceController::class, 'exportPdf'])->name('performance.export.pdf');
            });
        });

        // IC Evaluation (Admin, Industry Coach, and OSH Coordinator - authorization checked in controller)
        Route::middleware('role:admin,industry,osh_coordinator')->group(function () {
            Route::get('ic', [OshIcEvaluationController::class, 'index'])->name('ic.index');
            Route::get('ic/{student}', [OshIcEvaluationController::class, 'show'])->name('ic.show');
            Route::post('ic/{student}', [OshIcEvaluationController::class, 'store'])->name('ic.store');
        });

        // Logbook Evaluation (Admin, Industry Coach, and OSH Coordinator)
        Route::middleware('role:admin,industry,osh_coordinator')->prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/', [OshLogbookController::class, 'index'])->name('index');
            Route::get('/{student}', [OshLogbookController::class, 'show'])->name('show');
            Route::post('/{student}', [OshLogbookController::class, 'store'])->name('store');
        });
    });

    // FYP Module Routes
    Route::prefix('academic/fyp')->name('academic.fyp.')->group(function () {
        // Assessments (Admin and FYP Coordinator)
        Route::middleware('role:admin,fyp_coordinator')->group(function () {
            Route::get('assessments', function () {
                return app(AssessmentController::class)->index(request(), 'FYP');
            })->name('assessments.index');
            Route::get('assessments/create', function () {
                return app(AssessmentController::class)->create(request(), 'FYP');
            })->name('assessments.create');
            Route::post('assessments', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->store($request, 'FYP');
            })->name('assessments.store');
            Route::get('assessments/{assessment}/edit', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->edit(request(), $assessment, 'FYP');
            })->name('assessments.edit');
            Route::put('assessments/{assessment}', function (\Illuminate\Http\Request $request, \App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->update($request, $assessment, 'FYP');
            })->name('assessments.update');
            Route::delete('assessments/{assessment}', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->destroy($assessment, 'FYP');
            })->name('assessments.destroy');
            Route::post('assessments/{assessment}/toggle-active', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->toggleActive($assessment, 'FYP');
            })->name('assessments.toggle-active');
            Route::post('assessments/reorder-components', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->reorderComponents($request);
            })->name('assessments.reorder-components');

            // Assessment Schedule (Admin only)
            Route::get('schedule', [FypScheduleController::class, 'index'])->name('schedule.index');
            Route::post('schedule/window', [FypScheduleController::class, 'updateWindow'])->name('schedule.update-window');
            Route::post('schedule/reminder', [FypScheduleController::class, 'sendReminder'])->name('schedule.send-reminder');

            // Evaluation Progress (Admin only)
            Route::get('progress', [FypProgressController::class, 'index'])->name('progress.index');

            // Moderation (Admin only)
            Route::get('moderation', [FypModerationController::class, 'index'])->name('moderation.index');
            Route::get('moderation/{student}', [FypModerationController::class, 'show'])->name('moderation.show');
            Route::post('moderation/{student}', [FypModerationController::class, 'store'])->name('moderation.store');

            // Result Finalisation (Admin only)
            Route::get('finalisation', [FypFinalisationController::class, 'index'])->name('finalisation.index');
            Route::post('finalisation/student/{student}', [FypFinalisationController::class, 'finaliseStudent'])->name('finalisation.student');
            Route::post('finalisation/group', [FypFinalisationController::class, 'finaliseGroup'])->name('finalisation.group');
            Route::post('finalisation/course', [FypFinalisationController::class, 'finaliseCourse'])->name('finalisation.course');

            // Reports (Admin and FYP Coordinator)
            Route::get('reports', [FypReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/cohort', [FypReportsController::class, 'exportCohort'])->name('reports.cohort');
            Route::get('reports/group/{group}', [FypReportsController::class, 'exportGroup'])->name('reports.group');
            Route::get('reports/company/{company}', [FypReportsController::class, 'exportCompany'])->name('reports.company');
            Route::get('reports/clo-assessment', [FypReportsController::class, 'exportCloAssessment'])->name('reports.clo-assessment');

            // Audit Log (Admin only)
            Route::get('audit', [FypAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/export', [FypAuditController::class, 'export'])->name('audit.export');
        });

        // CLO-PLO Analysis (Admin, Coordinator, Lecturer, FYP Coordinator)
        Route::middleware('role:admin,coordinator,lecturer,fyp_coordinator')->group(function () {
            Route::get('clo-plo', [\App\Http\Controllers\Academic\FYP\FypCloPloController::class, 'index'])->name('clo-plo.index');
            Route::post('clo-plo', [\App\Http\Controllers\Academic\FYP\FypCloPloController::class, 'store'])->name('clo-plo.store');
            Route::post('clo-plo/update-count', [\App\Http\Controllers\Academic\FYP\FypCloPloController::class, 'updateCount'])->name('clo-plo.update-count');
            Route::put('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\FYP\FypCloPloController::class, 'update'])->name('clo-plo.update');
            Route::delete('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\FYP\FypCloPloController::class, 'destroy'])->name('clo-plo.destroy');
        });

        // AT Evaluation (Admin, AT, and FYP Coordinator)
        Route::middleware('role:admin,at,fyp_coordinator')->group(function () {
            Route::get('lecturer', [FypAtEvaluationController::class, 'index'])->name('lecturer.index');
            Route::get('lecturer/{student}', [FypAtEvaluationController::class, 'show'])->name('lecturer.show');
            Route::post('lecturer/{student}', [FypAtEvaluationController::class, 'store'])->name('lecturer.store');

            // Student Performance Overview
            Route::get('performance', [FypStudentPerformanceController::class, 'index'])->name('performance.index');

            // Export routes (Admin and FYP Coordinator)
            Route::middleware('role:admin,fyp_coordinator')->group(function () {
                Route::get('performance/export/excel', [FypStudentPerformanceController::class, 'exportExcel'])->name('performance.export.excel');
                Route::get('performance/export/pdf', [FypStudentPerformanceController::class, 'exportPdf'])->name('performance.export.pdf');
            });
        });

        // IC Evaluation (Admin, Industry Coach, and FYP Coordinator - authorization checked in controller)
        Route::middleware('role:admin,industry,fyp_coordinator')->group(function () {
            Route::get('ic', [FypIcEvaluationController::class, 'index'])->name('ic.index');
            Route::get('ic/{student}', [FypIcEvaluationController::class, 'show'])->name('ic.show');
            Route::post('ic/{student}', [FypIcEvaluationController::class, 'store'])->name('ic.store');
            Route::get('ic/{student}/rubric/{assessment}', [FypIcEvaluationController::class, 'rubric'])->name('ic.rubric');
            Route::post('ic/{student}/rubric/{assessment}', [FypIcEvaluationController::class, 'storeRubric'])->name('ic.rubric.store');
        });

        // Logbook Evaluation (Admin, Industry Coach, and FYP Coordinator)
        Route::middleware('role:admin,industry,fyp_coordinator')->prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/', [FypLogbookController::class, 'index'])->name('index');
            Route::get('/{student}', [FypLogbookController::class, 'show'])->name('show');
            Route::post('/{student}', [FypLogbookController::class, 'store'])->name('store');
        });

        // Rubric Template Management (Admin only)
        Route::middleware('role:admin')->prefix('rubrics')->name('rubrics.')->group(function () {
            Route::get('/', [FypRubricController::class, 'index'])->name('index');
            Route::get('/create', [FypRubricController::class, 'create'])->name('create');
            Route::post('/', [FypRubricController::class, 'store'])->name('store');
            Route::get('/{rubric}', [FypRubricController::class, 'show'])->name('show');
            Route::get('/{rubric}/edit', [FypRubricController::class, 'edit'])->name('edit');
            Route::put('/{rubric}', [FypRubricController::class, 'update'])->name('update');
            Route::delete('/{rubric}', [FypRubricController::class, 'destroy'])->name('destroy');
            Route::post('/{rubric}/duplicate', [FypRubricController::class, 'duplicate'])->name('duplicate');
            Route::post('/{rubric}/elements', [FypRubricController::class, 'addElement'])->name('add-element');
            Route::put('/{rubric}/elements/{element}', [FypRubricController::class, 'updateElement'])->name('update-element');
            Route::delete('/{rubric}/elements/{element}', [FypRubricController::class, 'deleteElement'])->name('delete-element');
            Route::put('/{rubric}/elements/{element}/descriptors', [FypRubricController::class, 'updateDescriptors'])->name('update-descriptors');
            Route::post('/{rubric}/reorder', [FypRubricController::class, 'reorderElements'])->name('reorder');
        });

        // Rubric-based Evaluation (Admin, AT, and IC - role checked in controller)
        Route::middleware('role:admin,at,industry')->prefix('rubric-evaluation')->name('rubric-evaluation.')->group(function () {
            Route::get('/', [FypRubricEvaluationController::class, 'index'])->name('index');
            Route::get('/{student}', [FypRubricEvaluationController::class, 'show'])->name('show');
            Route::post('/{student}', [FypRubricEvaluationController::class, 'store'])->name('store');
            Route::post('/{student}/{template}/submit', [FypRubricEvaluationController::class, 'submit'])->name('submit');
            Route::post('/{student}/{template}/release', [FypRubricEvaluationController::class, 'release'])->name('release');
        });

        // Project Proposal (Student)
        Route::middleware('role:student')->prefix('project-proposal')->name('project-proposal.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'edit'])->name('edit');
            Route::put('/', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'update'])->name('update');
            Route::post('/submit', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'submit'])->name('submit');
        });

        // Project Proposal Management (Admin, AT, and FYP Coordinator)
        Route::middleware('role:admin,at,fyp_coordinator')->prefix('proposals')->name('proposals.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'index'])->name('index');
            Route::get('/{proposal}', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'show'])->name('show');
            Route::post('/{proposal}/approve', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'approve'])->name('approve');
            Route::post('/{proposal}/reject', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'reject'])->name('reject');
            Route::get('/{proposal}/pdf', [\App\Http\Controllers\Academic\FYP\FypProjectProposalController::class, 'exportPdf'])->name('pdf');
        });
    });

    // Industrial Training (LI) Module Routes
    Route::prefix('academic/li')->name('academic.li.')->group(function () {
        // Assessments (Admin and LI Coordinator)
        Route::middleware('role:admin,li_coordinator')->group(function () {
            Route::get('assessments', function () {
                return app(AssessmentController::class)->index(request(), 'LI');
            })->name('assessments.index');
            Route::get('assessments/create', function () {
                return app(AssessmentController::class)->create(request(), 'LI');
            })->name('assessments.create');
            Route::post('assessments', function (\Illuminate\Http\Request $request) {
                return app(AssessmentController::class)->store($request, 'LI');
            })->name('assessments.store');
            Route::get('assessments/{assessment}/edit', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->edit(request(), $assessment, 'LI');
            })->name('assessments.edit');
            Route::put('assessments/{assessment}', function (\Illuminate\Http\Request $request, \App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->update($request, $assessment, 'LI');
            })->name('assessments.update');
            Route::delete('assessments/{assessment}', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->destroy($assessment, 'LI');
            })->name('assessments.destroy');
            Route::post('assessments/{assessment}/toggle-active', function (\App\Models\Assessment $assessment) {
                return app(AssessmentController::class)->toggleActive($assessment, 'LI');
            })->name('assessments.toggle-active');

            // Assessment Schedule, Progress, Moderation, Finalisation, Reports, Audit
            Route::get('schedule', function () {
                return view('academic.li.schedule.index');
            })->name('schedule.index');
            Route::get('progress', [\App\Http\Controllers\Academic\LI\LiProgressController::class, 'index'])->name('progress.index');
            Route::get('moderation', [\App\Http\Controllers\Academic\LI\LiModerationController::class, 'index'])->name('moderation.index');
            Route::get('moderation/{student}', [\App\Http\Controllers\Academic\LI\LiModerationController::class, 'show'])->name('moderation.show');
            Route::post('moderation/{student}', [\App\Http\Controllers\Academic\LI\LiModerationController::class, 'store'])->name('moderation.store');
            Route::get('finalisation', [\App\Http\Controllers\Academic\LI\LiFinalisationController::class, 'index'])->name('finalisation.index');
            Route::post('finalisation/student/{student}', [\App\Http\Controllers\Academic\LI\LiFinalisationController::class, 'finaliseStudent'])->name('finalisation.student');
            Route::post('finalisation/group', [\App\Http\Controllers\Academic\LI\LiFinalisationController::class, 'finaliseGroup'])->name('finalisation.group');
            Route::post('finalisation/course', [\App\Http\Controllers\Academic\LI\LiFinalisationController::class, 'finaliseCourse'])->name('finalisation.course');
            Route::get('reports', [\App\Http\Controllers\Academic\LI\LiReportsController::class, 'index'])->name('reports.index');
            Route::get('reports/cohort', [\App\Http\Controllers\Academic\LI\LiReportsController::class, 'exportCohort'])->name('reports.cohort');
            Route::get('reports/group/{group}', [\App\Http\Controllers\Academic\LI\LiReportsController::class, 'exportGroup'])->name('reports.group');
            Route::get('reports/company/{company}', [\App\Http\Controllers\Academic\LI\LiReportsController::class, 'exportCompany'])->name('reports.company');
            Route::get('audit', [\App\Http\Controllers\Academic\LI\LiAuditController::class, 'index'])->name('audit.index');
            Route::get('audit/export', [\App\Http\Controllers\Academic\LI\LiAuditController::class, 'export'])->name('audit.export');
        });

        // CLO-PLO Analysis (Admin, Coordinator, Lecturer, LI Coordinator)
        Route::middleware('role:admin,coordinator,lecturer,li_coordinator')->group(function () {
            Route::get('clo-plo', [\App\Http\Controllers\Academic\LI\LiCloPloController::class, 'index'])->name('clo-plo.index');
            Route::post('clo-plo', [\App\Http\Controllers\Academic\LI\LiCloPloController::class, 'store'])->name('clo-plo.store');
            Route::post('clo-plo/update-count', [\App\Http\Controllers\Academic\LI\LiCloPloController::class, 'updateCount'])->name('clo-plo.update-count');
            Route::put('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\LI\LiCloPloController::class, 'update'])->name('clo-plo.update');
            Route::delete('clo-plo/{cloPloMapping}', [\App\Http\Controllers\Academic\LI\LiCloPloController::class, 'destroy'])->name('clo-plo.destroy');
        });

        // Supervisor Evaluation (Admin, Supervisor LI, and LI Coordinator)
        Route::middleware('role:admin,supervisor_li,li_coordinator')->group(function () {
            Route::get('lecturer', [LiSupervisorEvaluationController::class, 'index'])->name('lecturer.index');
            Route::get('lecturer/{student}', [LiSupervisorEvaluationController::class, 'show'])->name('lecturer.show');
            Route::post('lecturer/{student}', [LiSupervisorEvaluationController::class, 'store'])->name('lecturer.store');
        });

        // IC Evaluation (Admin, Industry Coach, and LI Coordinator)
        Route::middleware('role:admin,industry,li_coordinator')->group(function () {
            Route::get('ic', [LiIcEvaluationController::class, 'index'])->name('ic.index');
            Route::get('ic/{student}', [LiIcEvaluationController::class, 'show'])->name('ic.show');
            Route::post('ic/{student}', [LiIcEvaluationController::class, 'store'])->name('ic.store');
        });

        // Logbook Evaluation (Admin, Industry Coach, and LI Coordinator)
        Route::middleware('role:admin,industry,li_coordinator')->prefix('logbook')->name('logbook.')->group(function () {
            Route::get('/', [LiLogbookController::class, 'index'])->name('index');
            Route::get('/{student}', [LiLogbookController::class, 'show'])->name('show');
            Route::post('/{student}', [LiLogbookController::class, 'store'])->name('store');
        });

        // Student Performance (Admin, Supervisor LI, IC, and LI Coordinator)
        Route::middleware('role:admin,supervisor_li,industry,li_coordinator')->group(function () {
            Route::get('performance', [LiStudentPerformanceController::class, 'index'])->name('performance.index');
            Route::get('performance/export/excel', [LiStudentPerformanceController::class, 'exportExcel'])->name('performance.export.excel');
            Route::get('performance/export/pdf', [LiStudentPerformanceController::class, 'exportPdf'])->name('performance.export.pdf');
        });
    });

    // Student Overview Routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('ppe/overview', [\App\Http\Controllers\Student\StudentPpeOverviewController::class, 'index'])
            ->name('ppe.overview');
    });
});
