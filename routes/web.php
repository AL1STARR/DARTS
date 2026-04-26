<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('index'))->name('dashboard');
    Route::get('/profile', fn() => view('profile'))->name('profile');
    Route::get('/myrequests', fn() => view('myrequests'))->name('myrequests');
    Route::get('/assigned', fn() => view('assigned'))->name('assigned');
    Route::get('/archive', fn() => view('archive'))->name('archive');
    Route::get('/routing', fn() => view('routing'))->name('routing');
});
