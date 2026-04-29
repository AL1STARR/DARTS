<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AssignedRequestController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('login');
});

Route::get('/privacy-policy', fn() => view('policies', ['page' => 'privacy', 'title' => 'Privacy Policy']))->name('privacy');
Route::get('/terms-of-service', fn() => view('policies', ['page' => 'terms', 'title' => 'Terms of Service']))->name('terms');
Route::get('/documentation', fn() => view('policies', ['page' => 'documentation', 'title' => 'Documentation']))->name('documentation');

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
    Route::get('/archive', [ArchiveController::class, 'index'])->name('archive');
    Route::post('/archive', [ArchiveController::class, 'store'])->name('archive.store');
    Route::get('/archive/department-docs', [ArchiveController::class, 'departmentDocs'])->name('archive.department-docs');
    Route::get('/archive/{archiveDocument}/download', [ArchiveController::class, 'download'])->name('archive.download');
    Route::get('/archive/{archiveDocument}/view', [ArchiveController::class, 'view'])->name('archive.view');
    Route::patch('/archive/{archiveDocument}', [ArchiveController::class, 'update'])->name('archive.update');
    Route::delete('/archive/{archiveDocument}', [ArchiveController::class, 'destroy'])->name('archive.destroy');
Route::get('/routing', [RoutingController::class, 'index'])->name('routing');
    Route::get('/routing/departments', [RoutingController::class, 'getDepartments'])->name('routing.departments');
    Route::get('/routing/list', [RoutingController::class, 'list'])->name('routing.list');
    Route::post('/routing/store', [RoutingController::class, 'store'])->name('routing.store');
    Route::get('/routing/{routeId}/detail', [RoutingController::class, 'detail'])->name('routing.detail');
    Route::patch('/routing/{routeId}/status', [RoutingController::class, 'updateStatus'])->name('routing.status');
    Route::patch('/routing/{routeId}/republish', [RoutingController::class, 'republish'])->name('routing.republish');
    Route::delete('/routing/{routeId}', [RoutingController::class, 'destroy'])->name('routing.destroy');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/admin/requests/{user}/approve', [AdminController::class, 'approve'])->name('admin.requests.approve');
    Route::delete('/admin/requests/{user}/reject', [AdminController::class, 'reject'])->name('admin.requests.reject');
    Route::post('/admin/settings/{group}', [AdminController::class, 'settingStore'])->name('admin.settings.store');
    Route::delete('/admin/settings/{setting}', [AdminController::class, 'settingDestroy'])->name('admin.settings.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/{notification}/dismiss', [NotificationController::class, 'dismiss'])->name('notifications.dismiss');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
});

