<?php

use App\Http\Controllers\ModuleCoordinatorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module Coordinator Routes
|--------------------------------------------------------------------------
|
| Routes for module coordinators (FYP, IP, OSH, PPE, LI) and WBL coordinators (BTA, BTD, BTG)
|
*/

Route::middleware(['auth'])->group(function () {
    // Module Coordinator Dashboard - accessible by any module coordinator
    Route::get('/coordinator/dashboard', [ModuleCoordinatorController::class, 'dashboard'])
        ->name('coordinator.dashboard')
        ->middleware('role:fyp_coordinator,ip_coordinator,osh_coordinator,ppe_coordinator,li_coordinator,bta_wbl_coordinator,btd_wbl_coordinator,btg_wbl_coordinator,admin');

    // Module Coordinator Profile
    Route::get('/coordinator/profile', [ModuleCoordinatorController::class, 'profile'])
        ->name('coordinator.profile.show')
        ->middleware('role:fyp_coordinator,ip_coordinator,osh_coordinator,ppe_coordinator,li_coordinator,bta_wbl_coordinator,btd_wbl_coordinator,btg_wbl_coordinator,admin');

    Route::patch('/coordinator/profile', [ModuleCoordinatorController::class, 'updateProfile'])
        ->name('coordinator.profile.update')
        ->middleware('role:fyp_coordinator,ip_coordinator,osh_coordinator,ppe_coordinator,li_coordinator,bta_wbl_coordinator,btd_wbl_coordinator,btg_wbl_coordinator,admin');
});
