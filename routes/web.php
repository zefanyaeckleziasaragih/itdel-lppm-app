<?php

use Illuminate\Support\Facades\Route;

// AUTH
use App\Http\Controllers\Auth\AuthController;

// HOME & APP
use App\Http\Controllers\App\Home\HomeController;
use App\Http\Controllers\App\HakAkses\HakAksesController;
use App\Http\Controllers\App\Todo\TodoController;

// PENGHARGAAN
use App\Http\Controllers\App\Penghargaan\StatistikController;
use App\Http\Controllers\App\Penghargaan\PengajuanController;
use App\Http\Controllers\App\HRD\DashboardHrdController;

// PENGAJUAN JURNAL
use App\Http\Controllers\App\PengajuanJurnal\JurnalController;

// DAFTAR PENGHARGAAN (Admin / HRD)
use App\Http\Controllers\App\HRD\DaftarPenghargaanController;

Route::middleware(['throttle:req-limit', 'handle.inertia'])->group(function () {

    // =======================
    // SSO Routes
    // =======================
    Route::prefix('sso')->group(function () {
        Route::get('/callback', [AuthController::class, 'ssoCallback'])
            ->name('sso.callback');
    });

    // =======================
    // Authentication Routes
    // =======================
    Route::prefix('auth')->group(function () {

        Route::get('/login', [AuthController::class, 'login'])
            ->name('auth.login');

        Route::post('/login-check', [AuthController::class, 'postLoginCheck'])
            ->name('auth.login-check');

        Route::post('/login-post', [AuthController::class, 'postLogin'])
            ->name('auth.login-post');

        Route::get('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');

        Route::get('/totp', [AuthController::class, 'totp'])
            ->name('auth.totp');

        Route::post('/totp-post', [AuthController::class, 'postTotp'])
            ->name('auth.totp-post');
    });

    // =======================
    // Protected Routes (butuh login)
    // =======================
    Route::middleware('check.auth')->group(function () {

        // Beranda
        Route::get('/', [HomeController::class, 'index'])
            ->name('home');

        // =======================
        // Hak Akses
        // =======================
        Route::prefix('hak-akses')->group(function () {
            Route::get('/', [HakAksesController::class, 'index'])
                ->name('hak-akses');

            Route::post('/change', [HakAksesController::class, 'postChange'])
                ->name('hak-akses.change-post');

            Route::post('/delete', [HakAksesController::class, 'postDelete'])
                ->name('hak-akses.delete-post');

            Route::post('/delete-selected', [HakAksesController::class, 'postDeleteSelected'])
                ->name('hak-akses.delete-selected-post');
        });

        // =======================
        // Todo
        // =======================
        Route::prefix('todo')->group(function () {
            Route::get('/', [TodoController::class, 'index'])
                ->name('todo');

            Route::post('/change', [TodoController::class, 'postChange'])
                ->name('todo.change-post');

            Route::post('/delete', [TodoController::class, 'postDelete'])
                ->name('todo.delete-post');
        });

        // =======================
        // PENGHARGAAN (Statistik + Daftar + Seminar + Dashboard HRD)
        // =======================
        Route::prefix('penghargaan')->group(function () {

            // ---------- Dashboard HRD ----------
            Route::get('/dashboard-hrd', [DashboardHrdController::class, 'index'])
                ->name('penghargaan.dashboard-hrd');

            // ---------- Statistik LPPM ----------
            Route::get('/statistik', [StatistikController::class, 'index'])
                ->name('penghargaan.statistik');

            // ---------- Daftar dosen pengajuan penghargaan (LPPM view) ----------
            Route::get('/daftar', [PengajuanController::class, 'index'])
                ->name('penghargaan.daftar');

            // Detail / Form konfirmasi (LPPM)
            Route::get('/daftar/{id}', [PengajuanController::class, 'show'])
                ->name('penghargaan.detail');

            // Simpan konfirmasi (status & dana – LPPM)
            Route::post('/daftar/{id}/konfirmasi', [PengajuanController::class, 'konfirmasi'])
                ->name('penghargaan.konfirmasi');

            // ---------- SEMINAR (DOSEN) ----------
            // Daftar Seminar yang sudah diajukan
            Route::get('/seminar/daftar', [PengajuanController::class, 'daftarSeminar'])
                ->name('penghargaan.seminar.daftar');

            // Pilih Prosiding (Step 1)
            Route::get('/seminar/pilih', [PengajuanController::class, 'pilihProsiding'])
                ->name('penghargaan.seminar.pilih');

            // Form Pengajuan Seminar (Step 2)
            Route::get('/seminar', [PengajuanController::class, 'formSeminar'])
                ->name('penghargaan.seminar');

            // Simpan Pengajuan Seminar
            Route::post('/seminar', [PengajuanController::class, 'storeSeminar'])
                ->name('penghargaan.seminar.store');

            // (Opsional) alias route seperti di branch temanmu:
            Route::get('/seminar/form', [PengajuanController::class, 'formSeminar'])
                ->name('penghargaan.seminar.form-alt');

            Route::post('/seminar/store', [PengajuanController::class, 'storeSeminar'])
                ->name('penghargaan.seminar.store-alt');
        });

        // =======================
        // ⭐ Pengajuan Jurnal
        // =======================
        Route::prefix('pengajuan-jurnal')->name('pengajuan.jurnal.')->group(function () {

            // Halaman Daftar Jurnal
            Route::get('/', [JurnalController::class, 'index'])
                ->name('daftar');

            // Halaman Pilih Data
            Route::get('/pilih-data', [JurnalController::class, 'pilihData'])
                ->name('pilih-data');

            // Halaman Form Penghargaan
            Route::get('/form', [JurnalController::class, 'form'])
                ->name('form');

            // Submit Form
            Route::post('/store', [JurnalController::class, 'store'])
                ->name('store');

            // (opsional, kalau di branch-mu ada)
            Route::get('/edit/{id}', [JurnalController::class, 'edit'])
                ->name('edit');

            Route::put('/update/{id}', [JurnalController::class, 'update'])
                ->name('update');

            Route::delete('/delete/{id}', [JurnalController::class, 'delete'])
                ->name('delete');
        });

        // =======================
        // DAFTAR PENGHARGAAN (ADMIN / HRD)
        // =======================
        Route::prefix('daftar-penghargaan')->group(function () {

            Route::get('/', [DaftarPenghargaanController::class, 'index'])
                ->name('daftar-penghargaan');

            Route::get('/{id}', [DaftarPenghargaanController::class, 'show'])
                ->name('daftar-penghargaan.detail');

            // Aksi HRD: tandai dana sudah dicairkan
            Route::post('/{id}/cairkan', [DaftarPenghargaanController::class, 'cairkanDana'])
                ->name('daftar-penghargaan.cairkan');
        });
    }); // end middleware check.auth
}); // end middleware throttle + inertia
