<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Courier;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// ── Client portal ────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ShipmentController::class, 'dashboard'])->name('dashboard');
    Route::resource('/shipments', ShipmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/shipments/{shipment}/invoice', [ShipmentController::class, 'invoice'])->name('shipments.invoice');
});

// ── AI Assistant (public — no login required) ──────────────────────
Route::prefix('ai')->name('ai.')->group(function () {
    Route::get('/',         [AiAssistantController::class, 'chat'])->name('chat');
    Route::post('/message', [AiAssistantController::class, 'message'])->name('message');
    Route::post('/clear',   [AiAssistantController::class, 'clearHistory'])->name('clear');
});

// ── Admin portal ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/shipments', [Admin\ShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/{shipment}', [Admin\ShipmentController::class, 'show'])->name('shipments.show');
        Route::patch('/shipments/{shipment}', [Admin\ShipmentController::class, 'update'])->name('shipments.update');
        Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings');
        Route::get('/settings/{group}', [Admin\SettingsController::class, 'show'])->name('settings.group');
        Route::patch('/settings/{group}', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-email', [Admin\SettingsController::class, 'testEmail'])->name('settings.test-email');
    });

// ── Courier portal ───────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:courier'])
    ->prefix('courier')->name('courier.')
    ->group(function () {
        Route::get('/dashboard', [Courier\DashboardController::class, 'index'])->name('dashboard');
        Route::patch('/shipments/{shipment}/status', [Courier\DispatchController::class, 'updateStatus'])
            ->name('shipments.update-status');
    });

// ── Profile (all auth users) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
