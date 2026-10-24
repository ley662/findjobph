<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ApplicantDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\JobDetailController;
use Illuminate\Support\Facades\Route;

// Public Jobs Feed and Job Details (Both / and /jobs serve the feed)
Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/jobs', [FeedController::class, 'index'])->name('jobs.feed');
Route::get('/jobs/{job}', [JobDetailController::class, 'show'])->name('jobs.show');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Applicant Portal & Actions
    Route::middleware('role:applicant')->group(function () {
        Route::post('/jobs/{job}/apply', [JobDetailController::class, 'apply'])->name('jobs.apply');
        Route::post('/jobs/{job}/upload-requirement', [JobDetailController::class, 'uploadRequirement'])->name('jobs.upload-requirement');

        Route::get('/applicant/vault', [ApplicantDashboardController::class, 'documents'])->name('applicant.documents');
        Route::post('/applicant/vault', [ApplicantDashboardController::class, 'uploadDocument'])->name('applicant.documents.upload');
        Route::delete('/applicant/vault/{document}', [ApplicantDashboardController::class, 'deleteDocument'])->name('applicant.documents.delete');

        Route::get('/applicant/applications', [ApplicantDashboardController::class, 'applications'])->name('applicant.applications');
    });

    // Employer Portal
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/jobs/create', [EmployerDashboardController::class, 'createJob'])->name('jobs.create');
        Route::post('/jobs', [EmployerDashboardController::class, 'storeJob'])->name('jobs.store');
        Route::get('/jobs/{job}/edit', [EmployerDashboardController::class, 'editJob'])->name('jobs.edit');
        Route::put('/jobs/{job}', [EmployerDashboardController::class, 'updateJob'])->name('jobs.update');
        Route::patch('/jobs/{job}/toggle', [EmployerDashboardController::class, 'toggleStatus'])->name('jobs.toggle');
        Route::delete('/jobs/{job}', [EmployerDashboardController::class, 'deleteJob'])->name('jobs.delete');

        Route::get('/applicants', [EmployerDashboardController::class, 'applicants'])->name('applicants');
        Route::patch('/applications/{application}/status', [EmployerDashboardController::class, 'updateApplicationStatus'])->name('applications.status');
    });

    // Admin Portal
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::patch('/users/{user}/toggle', [AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('users.delete');

        Route::get('/jobs', [AdminDashboardController::class, 'jobs'])->name('jobs');
        Route::patch('/jobs/{job}/toggle', [AdminDashboardController::class, 'toggleJobStatus'])->name('jobs.toggle');
        Route::delete('/jobs/{job}', [AdminDashboardController::class, 'deleteJob'])->name('jobs.delete');

        Route::get('/applications', [AdminDashboardController::class, 'applications'])->name('applications');
    });
});
