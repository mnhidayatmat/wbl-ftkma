<?php

use App\Http\Controllers\LecturerProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Lecturer Profile
    Route::get('/lecturer/profile', [LecturerProfileController::class, 'show'])
        ->name('lecturer.profile.show');

    Route::patch('/lecturer/profile', [LecturerProfileController::class, 'update'])
        ->name('lecturer.profile.update');
});
