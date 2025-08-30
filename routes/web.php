<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

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
use App\Http\Controllers\MasterDesaController;
use App\Http\Controllers\MasterPosyanduController;
use App\Http\Controllers\PrediksiBulkController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\SyncFuzzyToRFController;
use App\Http\Controllers\ReportController_E;

// Default Redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes (Laravel Breeze)
require __DIR__ . '/auth.php';

// ==============================================
// Protected Routes (Authenticated User Only)
// ==============================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===================== PENGUKURAN =====================
    Route::resource('pengukuran', PengukuranController::class);

    // ===================== BALITA + MASTER =====================
    Route::prefix('balita')->name('balita.')->group(function () {

        // --- MASTER DATA (letakkan di atas agar tidak ketabrak {balita}) ---
        Route::resource('master-desa', MasterDesaController::class);
        Route::resource('master-posyandu', MasterPosyanduController::class);

        // --- BALITA CRUD ---
        Route::get('/', [BalitaController::class, 'index'])->name('index');
        Route::get('/create', [BalitaController::class, 'create'])->name('create');
        Route::post('/', [BalitaController::class, 'store'])->name('store');

        Route::get('/{balita}', [BalitaController::class, 'show'])->whereNumber('balita')->name('show');
        Route::get('/{balita}/edit', [BalitaController::class, 'edit'])->whereNumber('balita')->name('edit');
        Route::put('/{balita}', [BalitaController::class, 'update'])->whereNumber('balita')->name('update');
        Route::delete('/{balita}', [BalitaController::class, 'destroy'])->whereNumber('balita')->name('destroy');

        // --- QR & Export ML ---
        Route::get('{balita}/qr-code', [BalitaController::class, 'qrCode'])->name('qr-code');
        Route::post('/balita/{balita}/regenerate-qr', [BalitaController::class, 'regenerateQr'])
            ->name('regenerate-qr');
        Route::get('{balita}/download-qr', [BalitaController::class, 'downloadQr'])->name('download-qr');

        // Ini route tujuan QR saat di-scan
        Route::get('/pengukuran/mobile/{nik}', [PengukuranController::class, 'mobileForm'])->name('pengukuran.mobile');

        // Route::get('/{balita}/qr-code', [BalitaController::class, 'showQrCode'])->whereNumber('balita')->name('qr-code');
        // Route::post('/{balita}/regenerate-qr', [BalitaController::class, 'regenerateQrCode'])->whereNumber('balita')->name('regenerate-qr');
        // Route::get('/{balita}/download-qr', [BalitaController::class, 'downloadQrCode'])->whereNumber('balita')->name('download-qr');

        // Route::post('/bulk-generate-qr', [BalitaController::class, 'bulkGenerateQrCode'])->name('bulk-generate-qr');
        // Route::post('/export-qr-pdf', [BalitaController::class, 'exportQrCodesPdf'])->name('export-qr-pdf');
        // Route::get('/export/ml', [BalitaController::class, 'exportForML'])->name('export-ml');
    });

    // ===================== REPORTS =====================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        // GET: quick report, POST: form manual → satu endpoint
        Route::match(['get', 'post'], 'generate', [ReportController::class, 'generate'])->name('generate');
        Route::post('export', [ReportController::class, 'export'])->name('export');
    });

    // Export khusus untuk E-PPGBM
    Route::get('/api/export-eppgbm', [ApiController::class, 'exportForEppgbm'])->name('api.export-eppgbm');
    Route::get('/export/csv', [ReportController_E::class, 'exportCsv'])->name('reports.export.csv');

    // ===================== ADMIN ONLY =====================
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/fuzzy', [SettingController::class, 'fuzzyIndex'])->name('fuzzy');
            Route::post('/fuzzy/criteria', [SettingController::class, 'updateCriteria'])->name('fuzzy.criteria');
            Route::post('/fuzzy/rules', [SettingController::class, 'updateRules'])->name('fuzzy.rules');
        });
        Route::get('balita/migrate-addresses', [BalitaController::class, 'migrateAddresses'])->name('balita.migrate-addresses');
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

// bedakan nama agar tidak nubruk dgn yg di atas
Route::get('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'form'])->name('prediksi.datalatih.form');
Route::post('/pengukuran/data-latih/prediksi', [PrediksiController::class, 'submit'])->name('prediksi.datalatih.submit');

Route::get('/pengukuran/data-latih/riwayat', [RiwayatPrediksiController::class, 'index'])->name('prediksi.riwayat');

Route::middleware('auth')->group(function () {
    Route::post('/admin/train-model', [TrainingController::class, 'train'])->name('admin.train-model');
});

// ===================== PREDIKSI BULK =====================
Route::group(['prefix' => 'prediksi-bulk', 'as' => 'prediksi.bulk.'], function () {
    Route::get('/', [PrediksiBulkController::class, 'index'])->name('form');
    Route::post('/predict', [PrediksiBulkController::class, 'predict'])->name('submit');
    Route::post('/refresh-data', [PrediksiBulkController::class, 'refreshData'])->name('refresh');

    // Sinkronisasi Fuzzy-AHP
    Route::post('/sync-from-fuzzy', [PrediksiBulkController::class, 'syncFromFuzzy'])->name('sync-fuzzy');
    Route::post('/retrain-model', [PrediksiBulkController::class, 'retrainModel'])->name('retrain');

    // Simple sync test
    Route::post('/simple-sync', [PrediksiBulkController::class, 'simpleSyncTest'])->name('simple-sync');
});

// ===================== Sinkronisasi Fuzzy-AHP =====================
Route::group(['prefix' => 'sync-fuzzy-ahp', 'as' => 'sync.fuzzy.'], function () {
    Route::post('/sync', [SyncFuzzyToRFController::class, 'syncFuzzyToRandomForest'])->name('sync');
    Route::get('/status', [SyncFuzzyToRFController::class, 'getSyncStatus'])->name('status');
});

// ===================== API monitoring (opsional) =====================
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

// dashboard monitoring (opsional)
Route::get('/dashboard/data-sync', function () {
    $syncController = new SyncFuzzyToRFController();
    $syncStatus = $syncController->getSyncStatus()->getData(true);
    return view('dashboard.data-sync', compact('syncStatus'));
})->name('dashboard.data-sync');

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

// Fallback Route
Route::fallback(fn() => redirect()->route('dashboard')->with('error', 'Halaman tidak ditemukan.'));
