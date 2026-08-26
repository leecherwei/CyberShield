<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganisationVerificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Easy Direct Logout
    Route::get('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Module 3.1: Organisation Verification Routes
    Route::get('/organisation/verify', function () {
        return view('organisation.verification');
    })->name('organisation.verification.show');

    Route::post('/organisation/verify', [OrganisationVerificationController::class, 'store'])
        ->name('organisation.verification.store');
});

require __DIR__ . '/auth.php';