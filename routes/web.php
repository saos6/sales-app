<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\EstimateController;
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

    Route::resource('custs', \App\Http\Controllers\CustController::class)->except(['show']);
    Route::get('custs/export', [\App\Http\Controllers\CustController::class, 'export'])->name('custs.export');

    Route::resource('items', ItemController::class)->except(['show']);
    Route::get('items/export', [ItemController::class, 'export'])->name('items.export');

    // Add these lines for Estimate
    Route::resource('estimates', EstimateController::class)->except(['show']);
    Route::get('estimates/export', [EstimateController::class, 'export'])->name('estimates.export');

    Route::resource('sales', \App\Http\Controllers\SalesController::class)->except(['show']);
    Route::get('sales/export', [\App\Http\Controllers\SalesController::class, 'export'])->name('sales.export');
});

require __DIR__.'/auth.php';
