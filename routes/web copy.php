<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\PengukuranController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\RiwayatPrediksiController;
use App\Http\Controllers\DataLatihController;
use App\Http\Controllers\MLDashboardController;
use App\Http\Controllers\MasterDesaController;
use App\Http\Controllers\MasterPosyanduController;
use App\Http\Controllers\PrediksiBulkController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Laravel Breeze)
require __DIR__ . '/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Balita Management
    // Route::resource('balita', BalitaController::class);
    Route::resource('balita', BalitaController::class)->parameters([
        'balita' => 'balita'
    ]);
    // Pengukuran & Prediksi
    Route::resource('pengukuran', PengukuranController::class);

    // Master Data Routes
    Route::resource('balita/master-desa', MasterDesaController::class);
    Route::resource('balita/master-posyandu', MasterPosyanduController::class);
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/generate', [ReportController::class, 'generate'])->name('generate'); // For GET requests
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });

    // API for E-PPGBM Integration
    Route::get('/api/export-eppgbm', [ApiController::class, 'exportForEppgbm'])->name('api.export-eppgbm');
    Route::get('/export/csv', [\App\Http\Controllers\ReportController_E::class, 'exportCsv'])->name('reports.export.csv');


    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {

        // User Management
        Route::resource('users', UserController::class)->except(['show']);

        // Fuzzy-AHP Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/fuzzy', [SettingController::class, 'fuzzyIndex'])->name('fuzzy');
            Route::post('/fuzzy/criteria', [SettingController::class, 'updateCriteria'])->name('fuzzy.criteria');
            Route::post('/fuzzy/rules', [SettingController::class, 'updateRules'])->name('fuzzy.rules');
        });
    });
});

// Additional API Routes
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Export endpoints for external systems
    Route::get('/balita', [ApiController::class, 'getBalita'])->name('api.balita');
    Route::get('/pengukuran', [ApiController::class, 'getPengukuran'])->name('api.pengukuran');
    Route::get('/prediksi', [ApiController::class, 'getPrediksi'])->name('api.prediksi');

    // Statistics API
    Route::get('/stats/dashboard', [ApiController::class, 'getDashboardStats'])->name('api.stats.dashboard');
    Route::get('/stats/monthly', [ApiController::class, 'getMonthlyStats'])->name('api.stats.monthly');
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan.');
});

// routes/web.php
Route::resource('data-latih', DataLatihController::class)->middleware(['auth', 'role:admin']);

Route::get('/prediksi', [PrediksiController::class, 'form'])->name('prediksi.form');
Route::post('/prediksi', [PrediksiController::class, 'submit'])->name('prediksi.submit');

Route::middleware('auth')->group(function () {
    Route::post('/admin/train-model', [TrainingController::class, 'train'])->name('admin.train-model');
});

Route::get('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'form'])->name('prediksi.form');
Route::post('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'submit'])->name('prediksi.submit');


Route::get('/pengukuran/data-latih/riwayat', [RiwayatPrediksiController::class, 'index'])->name('prediksi.riwayat');


// API routes untuk dropdown dependencies
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('posyandu-by-area', [BalitaController::class, 'getPosyanduByArea']);
    Route::get('desa-by-area', [BalitaController::class, 'getDesaByArea']);
    Route::get('desa-by-posyandu', [BalitaController::class, 'getDesaByPosyandu']);
});

Route::get('/prediksi/semua', [PrediksiController::class, 'prediksiSemua'])->name('prediksi.semua');
Route::get('/prediksi/bulk', [PrediksiBulkController::class, 'index'])->name('prediksi.bulk.form');
Route::post('/prediksi/bulk', [PrediksiBulkController::class, 'predict'])->name('prediksi.bulk.submit');
Route::get('/prediksi/bulk/refresh', [PrediksiBulkController::class, 'refreshData'])->name('prediksi.bulk.refresh');