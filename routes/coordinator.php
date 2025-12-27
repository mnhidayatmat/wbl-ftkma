<?php

use App\Http\Controllers\ModuleCoordinatorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module Coordinator Routes
|--------------------------------------------------------------------------
|
| Routes for module coordinators (FYP, IP, OSH, PPE, LI)
|
*/

Route::middleware(['auth'])->group(function () {
    // Module Coordinator Dashboard - accessible by any module coordinator
    Route::get('/coordinator/dashboard', [ModuleCoordinatorController::class, 'dashboard'])
        ->name('coordinator.dashboard');
});
