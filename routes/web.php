<?php

use App\Http\Controllers\App\HakAkses\HakAksesController;
use App\Http\Controllers\App\Home\HomeController;
use App\Http\Controllers\App\Seminar\SeminarController; // ← Tambahkan ini
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:req-limit', 'handle.inertia'])->group(function () {
    // SSO Routes
    Route::group(['prefix' => 'sso'], function () {
        Route::get('/callback', [AuthController::class, 'ssoCallback'])->name('sso.callback');
    });

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/login-check', [AuthController::class, 'postLoginCheck'])->name('auth.login-check');
        Route::post('/login-post', [AuthController::class, 'postLogin'])->name('auth.login-post');
        Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/totp', [AuthController::class, 'totp'])->name('auth.totp');
        Route::post('/totp-post', [AuthController::class, 'postTotp'])->name('auth.totp-post');
    });

    // Protected Routes
    Route::group(['middleware' => 'check.auth'], function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // Hak Akses Routes
        Route::prefix('hak-akses')->group(function () {
            Route::get('/', [HakAksesController::class, 'index'])->name('hak-akses');
            Route::post('/change', [HakAksesController::class, 'postChange'])->name('hak-akses.change-post');
            Route::post('/delete', [HakAksesController::class, 'postDelete'])->name('hak-akses.delete-post');
            Route::post('/delete-selected', [HakAksesController::class, 'postDeleteSelected'])->name('hak-akses.delete-selected-post');
        });

        // Seminar Routes (Dosen)
        Route::prefix('seminar')->group(function () {
            Route::get('/', [SeminarController::class, 'index'])->name('seminar');
            Route::post('/store', [SeminarController::class, 'store'])->name('seminar.store');
            Route::post('/update', [SeminarController::class, 'update'])->name('seminar.update');
            Route::post('/delete', [SeminarController::class, 'destroy'])->name('seminar.delete');
        });

        // Seminar Admin Routes
        Route::prefix('seminar-admin')->group(function () {
            Route::get('/', [SeminarController::class, 'adminIndex'])->name('seminar.admin');
            Route::post('/update-status', [SeminarController::class, 'updateStatus'])->name('seminar.update-status');
        });
    });
});