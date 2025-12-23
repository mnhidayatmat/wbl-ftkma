<?php

use App\Http\Controllers\Industry\MyStudentsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Industry Routes (IC - Industry Coach)
|--------------------------------------------------------------------------
|
| Routes for Industry Coaches (IC) to evaluate students.
| IC supervises students for the ENTIRE WBL duration.
|
| Note: IC is a fixed term for industry supervisors only.
| IC evaluates rubric/oral evaluation etc.
|
*/

Route::middleware(['auth', 'role:industry,admin'])->group(function () {
    // IC - My Students
    Route::prefix('industry')->name('industry.')->group(function () {
        Route::get('students', [MyStudentsController::class, 'index'])->name('students.index');
    });
});
