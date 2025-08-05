<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\DeptController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('depts', DeptController::class)->except(['show']);
    Route::get('depts/export', [DeptController::class, 'export'])->name('depts.export');

    Route::resource('emps', \App\Http\Controllers\EmpController::class)->except(['show']);
    Route::get('emps/export', [\App\Http\Controllers\EmpController::class, 'export'])->name('emps.export');
});

require __DIR__.'/auth.php';