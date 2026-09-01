<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrganisationVerificationController;
use App\Http\Controllers\ProjectPostingController;
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

    // Module: Project & Partnership Posting Routes
    Route::get('/projects', [ProjectPostingController::class, 'index'])
        ->name('projects.index');
 
    Route::get('/projects/create', [ProjectPostingController::class, 'create'])
        ->name('projects.create');
 
    Route::post('/projects', [ProjectPostingController::class, 'store'])
        ->name('projects.store');
 
    Route::get('/projects/{project}', [ProjectPostingController::class, 'show'])
        ->name('projects.show');
 
    Route::get('/projects/{project}/edit', [ProjectPostingController::class, 'edit'])
        ->name('projects.edit');
 
    Route::put('/projects/{project}', [ProjectPostingController::class, 'update'])
        ->name('projects.update');
 
    Route::delete('/projects/{project}', [ProjectPostingController::class, 'destroy'])
        ->name('projects.destroy');
 
    Route::patch('/projects/{project}/status', [ProjectPostingController::class, 'updateStatus'])
        ->name('projects.updateStatus');

});

require __DIR__ . '/auth.php';