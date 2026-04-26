<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GoogleAuthController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('login');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('index'))->name('dashboard');
    Route::get('/profile', fn() => view('profile'))->name('profile');
    Route::get('/myrequests', fn() => view('myrequests'))->name('myrequests');
    Route::get('/assigned', fn() => view('assigned'))->name('assigned');
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

