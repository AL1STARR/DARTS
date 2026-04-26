<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssignedRequestController;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('login');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', fn() => view('profile'))->name('profile');
    Route::get('/myrequests', [DocumentRequestController::class, 'index'])->name('myrequests');
    Route::post('/myrequests', [DocumentRequestController::class, 'store'])->name('myrequests.store');
    Route::patch('/myrequests/{documentRequest}', [DocumentRequestController::class, 'update'])->name('myrequests.update');
    Route::delete('/myrequests/{documentRequest}', [DocumentRequestController::class, 'destroy'])->name('myrequests.destroy');
    Route::get('/attachments/{attachment}', [DocumentRequestController::class, 'viewAttachment'])->name('attachments.view');
    Route::get('/assigned', [AssignedRequestController::class, 'index'])->name('assigned');
    Route::patch('/assigned/{documentRequest}/status', [AssignedRequestController::class, 'updateStatus'])->name('assigned.status');
    Route::patch('/assigned/{documentRequest}/transfer', [AssignedRequestController::class, 'transfer'])->name('assigned.transfer');
    Route::get('/assigned/department-users', [AssignedRequestController::class, 'departmentUsers'])->name('assigned.department-users');
    Route::get('/archive', fn() => view('archive'))->name('archive');
    Route::get('/routing', fn() => view('routing'))->name('routing');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/requests/{user}/approve', [AdminController::class, 'approve'])->name('admin.requests.approve');
    Route::delete('/admin/requests/{user}/reject', [AdminController::class, 'reject'])->name('admin.requests.reject');
    Route::post('/admin/settings/{group}', [AdminController::class, 'settingStore'])->name('admin.settings.store');
    Route::delete('/admin/settings/{setting}', [AdminController::class, 'settingDestroy'])->name('admin.settings.destroy');
});

