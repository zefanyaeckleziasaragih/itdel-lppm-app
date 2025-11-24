<?php

use App\Http\Controllers\App\HakAkses\HakAksesController;
use App\Http\Controllers\App\Home\HomeController;
use App\Http\Controllers\App\Todo\TodoController;

// PENGHARGAAN
use App\Http\Controllers\App\Penghargaan\StatistikController;
use App\Http\Controllers\App\Penghargaan\PengajuanController;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:req-limit', 'handle.inertia'])->group(function () {

    // =======================
    // SSO Routes
    // =======================
    Route::group(['prefix' => 'sso'], function () {
        Route::get('/callback', [AuthController::class, 'ssoCallback'])->name('sso.callback');
    });

    // =======================
    // Authentication Routes
    // =======================
    Route::prefix('auth')->group(function () {
        // Login Routes
        Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/login-check', [AuthController::class, 'postLoginCheck'])->name('auth.login-check');
        Route::post('/login-post', [AuthController::class, 'postLogin'])->name('auth.login-post');

        // Logout Route
        Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        // TOTP Routes
        Route::get('/totp', [AuthController::class, 'totp'])->name('auth.totp');
        Route::post('/totp-post', [AuthController::class, 'postTotp'])->name('auth.totp-post');
    });

    // =======================
    // Protected Routes (butuh login)
    // =======================
    Route::group(['middleware' => 'check.auth'], function () {

        // Beranda
        Route::get('/', [HomeController::class, 'index'])->name('home');

        // --------------------
        // Hak Akses
        // --------------------
        Route::prefix('hak-akses')->group(function () {
            Route::get('/', [HakAksesController::class, 'index'])->name('hak-akses');
            Route::post('/change', [HakAksesController::class, 'postChange'])->name('hak-akses.change-post');
            Route::post('/delete', [HakAksesController::class, 'postDelete'])->name('hak-akses.delete-post');
            Route::post('/delete-selected', [HakAksesController::class, 'postDeleteSelected'])->name('hak-akses.delete-selected-post');
        });

        // --------------------
        // Todo
        // --------------------
        Route::prefix('todo')->group(function () {
            Route::get('/', [TodoController::class, 'index'])->name('todo');
            Route::post('/change', [TodoController::class, 'postChange'])->name('todo.change-post');
            Route::post('/delete', [TodoController::class, 'postDelete'])->name('todo.delete-post');
        });

        // =======================
        // PENGHARGAAN SEMINAR
        // =======================
        Route::prefix('penghargaan')->group(function () {

            // Daftar Seminar yang sudah diajukan
            Route::get('/seminar/daftar', [PengajuanController::class, 'daftarSeminar'])
                ->name('penghargaan.seminar.daftar');

            // Pilih Prosiding (Step 1)
            Route::get('/seminar/pilih', [PengajuanController::class, 'pilihProsiding'])
                ->name('penghargaan.seminar.pilih');

            // Form Pengajuan Seminar (Step 2)
            Route::get('/seminar', [PengajuanController::class, 'index'])
                ->name('penghargaan.seminar');
            
            Route::post('/seminar', [PengajuanController::class, 'storeSeminar'])
                ->name('penghargaan.seminar.store');

            // Redirect dari statistik (tidak digunakan)
            Route::get('/statistik', [StatistikController::class, 'index'])
                ->name('penghargaan.statistik');
        });
    });
});
