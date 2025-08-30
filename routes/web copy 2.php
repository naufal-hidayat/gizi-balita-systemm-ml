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
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\AnggotaKeluargaController;

// Default Redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes (Laravel Breeze)
require __DIR__ . '/auth.php';

// ==============================================
// Protected Routes (Authenticated User Only)
// ==============================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===================== BALITA =====================
    Route::resource('balita', BalitaController::class)->parameters(['balita' => 'balita']);

    // ===================== PENGUKURAN =====================
    Route::resource('pengukuran', PengukuranController::class);

    // ===================== MASTER DATA =====================
    Route::resource('balita/master-desa', MasterDesaController::class);
    Route::resource('balita/master-posyandu', MasterPosyanduController::class);

    // ===================== REPORTS =====================
   // ===================== REPORTS =====================
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/generate', [ReportController::class, 'generate'])->name('generate');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate'); // untuk quick report
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate.post'); // untuk form manual
    });


    // Export khusus untuk E-PPGBM
    Route::get('/api/export-eppgbm', [ApiController::class, 'exportForEppgbm'])->name('api.export-eppgbm');
    Route::get('/export/csv', [\App\Http\Controllers\ReportController_E::class, 'exportCsv'])->name('reports.export.csv');

    // ===================== ADMIN ONLY =====================
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

// ===================== API Tambahan =====================
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/balita', [ApiController::class, 'getBalita'])->name('api.balita');
    Route::get('/pengukuran', [ApiController::class, 'getPengukuran'])->name('api.pengukuran');
    Route::get('/prediksi', [ApiController::class, 'getPrediksi'])->name('api.prediksi');

    Route::get('/stats/dashboard', [ApiController::class, 'getDashboardStats'])->name('api.stats.dashboard');
    Route::get('/stats/monthly', [ApiController::class, 'getMonthlyStats'])->name('api.stats.monthly');

    // Dropdown dynamic
    Route::get('posyandu-by-area', [BalitaController::class, 'getPosyanduByArea']);
    Route::get('desa-by-area', [BalitaController::class, 'getDesaByArea']);
    Route::get('desa-by-posyandu', [BalitaController::class, 'getDesaByPosyandu']);
});

// ===================== DATA LATIH =====================
Route::resource('data-latih', DataLatihController::class)->middleware(['auth', 'role:admin']);

// ===================== PREDIKSI =====================
Route::get('/prediksi', [PrediksiController::class, 'form'])->name('prediksi.form');
Route::post('/prediksi', [PrediksiController::class, 'submit'])->name('prediksi.submit');

Route::get('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'form'])->name('prediksi.form');
Route::post('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'submit'])->name('prediksi.submit');

Route::get('/pengukuran/data-latih/riwayat', [RiwayatPrediksiController::class, 'index'])->name('prediksi.riwayat');

Route::middleware('auth')->group(function () {
    Route::post('/admin/train-model', [TrainingController::class, 'train'])->name('admin.train-model');
});

// ===================== PREDIKSI BULK =====================
Route::get('/prediksi/semua', [PrediksiController::class, 'prediksiSemua'])->name('prediksi.semua');
Route::get('/prediksi/bulk', [PrediksiBulkController::class, 'index'])->name('prediksi.bulk.form');
Route::post('/prediksi/bulk', [PrediksiBulkController::class, 'predict'])->name('prediksi.bulk.submit');
Route::get('/prediksi/bulk/refresh', [PrediksiBulkController::class, 'refreshData'])->name('prediksi.bulk.refresh');
// Routes untuk Prediksi Bulk dengan fitur sinkronisasi Fuzzy-AHP
Route::group(['prefix' => 'prediksi-bulk', 'as' => 'prediksi.bulk.'], function () {
    Route::get('/', [PrediksiBulkController::class, 'index'])->name('form');
    Route::post('/predict', [PrediksiBulkController::class, 'predict'])->name('submit');
    Route::post('/refresh-data', [PrediksiBulkController::class, 'refreshData'])->name('refresh');
    
    // Route untuk sinkronisasi Fuzzy-AHP
    Route::post('/sync-from-fuzzy', [PrediksiBulkController::class, 'syncFromFuzzy'])->name('sync-fuzzy');
    Route::post('/retrain-model', [PrediksiBulkController::class, 'retrainModel'])->name('retrain');
});

// Routes untuk Sinkronisasi Fuzzy-AHP (dapat digunakan sebagai API)
Route::group(['prefix' => 'sync-fuzzy-ahp', 'as' => 'sync.fuzzy.'], function () {
    Route::post('/sync', [SyncFuzzyToRFController::class, 'syncFuzzyToRandomForest'])->name('sync');
    Route::get('/status', [SyncFuzzyToRFController::class, 'getSyncStatus'])->name('status');
});

// Routes untuk API monitoring (opsional - untuk debug)
Route::group(['prefix' => 'api-monitor', 'as' => 'api.monitor.'], function () {
    Route::get('/flask-status', function () {
        try {
            $response = Http::timeout(5)->get('http://127.0.0.1:5000/health');
            return response()->json([
                'status' => $response->successful() ? 'online' : 'offline',
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'error' => $e->getMessage()
            ]);
        }
    })->name('flask-status');
});

// Route untuk dashboard monitoring (opsional)
Route::get('/dashboard/data-sync', function () {
    $syncController = new \App\Http\Controllers\SyncFuzzyToRFController();
    $syncStatus = $syncController->getSyncStatus()->getData(true);
    
    return view('dashboard.data-sync', compact('syncStatus'));
})->name('dashboard.data-sync');
Route::post('/prediksi-bulk/simple-sync', [PrediksiBulkController::class, 'simpleSyncTest'])->name('prediksi.bulk.simple-sync');
// ===================== KELUARGA =====================
Route::prefix('keluarga')->name('keluarga.')->group(function () {
    Route::get('/', [KeluargaController::class, 'index'])->name('index');
    Route::get('/create', [KeluargaController::class, 'create'])->name('create');
    Route::post('/', [KeluargaController::class, 'store'])->name('store');
    Route::get('/{keluarga}', [KeluargaController::class, 'show'])->name('show');
    Route::get('/{keluarga}/edit', [KeluargaController::class, 'edit'])->name('edit');
    Route::put('/{keluarga}', [KeluargaController::class, 'update'])->name('update');
    Route::delete('/{keluarga}', [KeluargaController::class, 'destroy'])->name('destroy');

    // Anggota keluarga
    Route::post('/{keluarga}/anggota', [KeluargaController::class, 'addAnggota'])->name('add-anggota');
    Route::put('/{keluarga}/anggota/{anggota}', [KeluargaController::class, 'updateAnggota'])->name('update-anggota');
    Route::delete('/{keluarga}/anggota/{anggota}/remove', [KeluargaController::class, 'removeAnggota'])->name('remove-anggota');
});

// ===================== BALITA EXTENDED (QR & EXPORT) =====================
Route::prefix('balita')->name('balita.')->group(function () {
    Route::get('/', [BalitaController::class, 'index'])->name('index');
    Route::get('/create', [BalitaController::class, 'create'])->name('create');
    Route::post('/', [BalitaController::class, 'store'])->name('store');
    Route::get('/{balita}', [BalitaController::class, 'show'])->name('show');
    Route::get('/{balita}/edit', [BalitaController::class, 'edit'])->name('edit');
    Route::put('/{balita}', [BalitaController::class, 'update'])->name('update');
    Route::delete('/{balita}', [BalitaController::class, 'destroy'])->name('destroy');

    // QR
    Route::get('/{balita}/qr-code', [BalitaController::class, 'showQrCode'])->name('qr-code');
    Route::post('/{balita}/regenerate-qr', [BalitaController::class, 'regenerateQrCode'])->name('regenerate-qr');
    Route::get('/{balita}/download-qr', [BalitaController::class, 'downloadQrCode'])->name('download-qr');

    // Bulk QR & Export ML
    Route::post('/bulk-generate-qr', [BalitaController::class, 'bulkGenerateQrCode'])->name('bulk-generate-qr');
    Route::post('/export-qr-pdf', [BalitaController::class, 'exportQrCodesPdf'])->name('export-qr-pdf');
    Route::get('/export/ml', [BalitaController::class, 'exportForML'])->name('export-ml');
});

// Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan.');
});
