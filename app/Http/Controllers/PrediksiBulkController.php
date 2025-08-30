<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr; 
use Illuminate\Support\Facades\Log;

class PrediksiBulkController extends Controller
{
    public function index()
    {
        // Cek status sinkronisasi dengan sistem Fuzzy-AHP
        $syncStatus = $this->checkSyncStatus();
        
        // Cek apakah file CSV ada
        $csvPath = storage_path('app/ml/data_latih.csv');
        $csvExists = File::exists($csvPath);
        
        // Jika file ada, ambil info jumlah data dan distribusi
        $dataCount = 0;
        $lastUpdated = null;
        $statusDistribution = [];
        $dataQuality = 'unknown';
        
        if ($csvExists) {
            try {
                $csvContent = file($csvPath);
                $dataCount = count($csvContent) - 1; // minus header
                $lastUpdated = File::lastModified($csvPath);
                
                // Analisis distribusi data
                $statusDistribution = $this->analyzeDataDistribution($csvPath);
                $dataQuality = $this->assessDataQuality($statusDistribution, $dataCount);
                
            } catch (\Exception $e) {
                Log::error('Error analyzing CSV: ' . $e->getMessage());
            }
        }
        
        return view('pengukuran.data_latih.bulk-form', compact(
            'csvExists', 
            'dataCount', 
            'lastUpdated', 
            'statusDistribution',
            'dataQuality',
            'syncStatus'
        ));
    }

    /**
     * Cek status sinkronisasi dengan sistem Fuzzy-AHP
     */
    private function checkSyncStatus()
    {
        try {
            $syncController = new SyncFuzzyToRFController();
            $response = $syncController->getSyncStatus();
            $data = $response->getData(true);
            
            return [
                'fuzzy_total' => $data['fuzzy_ahp']['total_records'] ?? 0,
                'fuzzy_distribution' => $data['fuzzy_ahp']['distribution'] ?? [],
                'csv_exists' => $data['random_forest_csv']['exists'] ?? false,
                'csv_last_modified' => $data['random_forest_csv']['last_modified'] ?? null,
                'sync_needed' => $data['sync_needed'] ?? false
            ];
        } catch (\Exception $e) {
            Log::error('Error checking sync status: ' . $e->getMessage());
            return [
                'fuzzy_total' => 0,
                'fuzzy_distribution' => [],
                'csv_exists' => false,
                'csv_last_modified' => null,
                'sync_needed' => false
            ];
        }
    }

    /**
     * Sinkronisasi data dari Fuzzy-AHP ke Random Forest
     */
    public function syncFromFuzzy()
    {
        try {
            $syncController = new SyncFuzzyToRFController();
            $response = $syncController->syncFuzzyToRandomForest();
            $data = $response->getData(true);
            
            if ($data['success']) {
                $distribution = $data['data']['distribution'];
                $total = $data['data']['total_records'];
                
                $message = "✅ Data berhasil disinkronisasi dari sistem Fuzzy-AHP ke Random Forest! " .
                          "Total: {$total} records. " .
                          "Normal: {$distribution[0]}, Beresiko: {$distribution[1]}, Stunting: {$distribution[2]}";
                
                return redirect()->route('prediksi.bulk.form')
                    ->with('success', $message);
            } else {
                return redirect()->route('prediksi.bulk.form')
                    ->with('error', '❌ ' . $data['message']);
            }
        } catch (\Exception $e) {
            Log::error('Error in syncFromFuzzy: ' . $e->getMessage());
            return redirect()->route('prediksi.bulk.form')
                ->with('error', '❌ Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Analisis distribusi status stunting dalam data
     */
    private function analyzeDataDistribution($csvPath)
    {
        try {
            $csvContent = file($csvPath);
            if (empty($csvContent)) {
                return [];
            }

            $header = str_getcsv(array_shift($csvContent));
            $statusIndex = array_search('status_stunting', $header);
            
            if ($statusIndex === false) {
                return [];
            }

            $distribution = [0 => 0, 1 => 0, 2 => 0]; // Normal, Beresiko, Stunting
            
            foreach ($csvContent as $row) {
                $rowData = str_getcsv($row);
                if (isset($rowData[$statusIndex]) && is_numeric($rowData[$statusIndex])) {
                    $status = intval($rowData[$statusIndex]);
                    if (isset($distribution[$status])) {
                        $distribution[$status]++;
                    }
                }
            }

            return $distribution;
        } catch (\Exception $e) {
            Log::error('Error in analyzeDataDistribution: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Penilaian kualitas data
     */
    private function assessDataQuality($distribution, $totalData)
    {
        if (empty($distribution) || $totalData < 10) {
            return 'insufficient';
        }

        $total = array_sum($distribution);
        if ($total == 0) {
            return 'empty';
        }

        // Hitung persentase
        $normalPct = ($distribution[0] / $total) * 100;
        $beresikoPct = ($distribution[1] / $total) * 100;
        $stuntingPct = ($distribution[2] / $total) * 100;

        // Penilaian kualitas berdasarkan distribusi
        if ($normalPct > 95) {
            return 'imbalanced'; // Terlalu timpang ke normal
        } elseif ($beresikoPct == 0 && $stuntingPct == 0) {
            return 'no_stunting'; // Tidak ada data stunting sama sekali
        } elseif ($beresikoPct + $stuntingPct < 5) {
            return 'very_low_stunting'; // Sangat sedikit data stunting
        } elseif ($total < 30) {
            return 'small_dataset'; // Dataset terlalu kecil
        } else {
            return 'good'; // Kualitas baik
        }
    }

    public function predict(Request $request)
    {
        // Path ke file CSV yang sudah ada
        $csvPath = storage_path('app/ml/data_latih.csv');
        
        // Cek apakah file CSV ada
        if (!File::exists($csvPath)) {
            return back()->with('error', '❌ File data latih tidak ditemukan. Silakan sinkronisasi data dari sistem Fuzzy-AHP terlebih dahulu.');
        }

        try {
            // Cek kualitas data sebelum prediksi
            $distribution = $this->analyzeDataDistribution($csvPath);
            $quality = $this->assessDataQuality($distribution, 0);
            
            if (in_array($quality, ['imbalanced', 'no_stunting', 'very_low_stunting'])) {
                return back()->with('warning', 
                    '⚠️ Data training tidak seimbang. Hanya ' . ($distribution[1] + $distribution[2]) . 
                    ' dari ' . array_sum($distribution) . 
                    ' data yang memiliki status stunting/beresiko. Hasil prediksi mungkin tidak akurat. ' .
                    'Disarankan untuk sinkronisasi ulang data dari sistem Fuzzy-AHP.'
                );
            }

            // Baca file CSV
            $csvContent = file($csvPath);
            
            if (empty($csvContent)) {
                return back()->with('error', '❌ File CSV kosong.');
            }

            // Parse CSV dengan penanganan error yang lebih baik
            $header = str_getcsv(array_shift($csvContent));
            $data = [];
            $skippedRows = 0;

            foreach ($csvContent as $index => $row) {
                $rowData = str_getcsv($row);
                
                // Skip baris yang tidak valid
                if (count($rowData) !== count($header)) {
                    $skippedRows++;
                    continue;
                }

                $combinedData = array_combine($header, $rowData);
                
                // Validasi data yang diperlukan untuk prediksi
                $requiredFields = [
                    'berat_badan', 'tinggi_badan', 'lingkar_kepala', 
                    'lingkar_lengan', 'usia', 'asi_eksklusif', 
                    'status_imunisasi', 'riwayat_penyakit', 
                    'akses_air_bersih', 'sanitasi_layak'
                ];

                $hasValidData = true;
                foreach ($requiredFields as $field) {
                    if (!isset($combinedData[$field]) || 
                        $combinedData[$field] === '' || 
                        $combinedData[$field] === null) {
                        $hasValidData = false;
                        break;
                    }
                }

                // Skip baris dengan data tidak lengkap
                if (!$hasValidData) {
                    $skippedRows++;
                    continue;
                }
                
                // Format data untuk API Flask dengan validasi
                try {
                    $data[] = [
                        'nama' => $combinedData['nama'] ?? 'Unknown',
                        'area' => $combinedData['area'] ?? 'Unknown',
                        'posyandu' => $combinedData['posyandu'] ?? 'Unknown',
                        'desa' => $combinedData['desa'] ?? 'Unknown',
                        'berat_badan' => floatval($combinedData['berat_badan']),
                        'tinggi_badan' => floatval($combinedData['tinggi_badan']),
                        'lingkar_kepala' => floatval($combinedData['lingkar_kepala']),
                        'lingkar_lengan' => floatval($combinedData['lingkar_lengan']),
                        'usia' => floatval($combinedData['usia']),
                        'asi_eksklusif' => intval($combinedData['asi_eksklusif']),
                        'status_imunisasi' => intval($combinedData['status_imunisasi']),
                        'riwayat_penyakit' => intval($combinedData['riwayat_penyakit']),
                        'akses_air_bersih' => intval($combinedData['akses_air_bersih']),
                        'sanitasi_layak' => intval($combinedData['sanitasi_layak']),
                    ];
                } catch (\Exception $e) {
                    $skippedRows++;
                    Log::warning("Error processing row " . ($index + 1) . ": " . $e->getMessage());
                    continue;
                }
            }

            if (empty($data)) {
                return back()->with('error', '❌ Tidak ada data valid yang dapat diproses. Periksa format data CSV Anda.');
            }

            Log::info("Processing " . count($data) . " records, skipped " . $skippedRows . " invalid rows");

            // Cek koneksi ke Flask API
            try {
                $healthCheck = Http::timeout(5)->get('http://127.0.0.1:5000/health');
                if ($healthCheck->failed()) {
                    return back()->with('error', '❌ Server Flask tidak merespons. Pastikan Flask API berjalan di port 5000.');
                }
            } catch (\Exception $e) {
                return back()->with('error', '❌ Tidak dapat terhubung ke Flask API. Pastikan server Flask sedang berjalan di port 5000.');
            }

            // Kirim ke API Flask untuk prediksi dengan timeout yang lebih lama
            $response = Http::timeout(120)->post('http://127.0.0.1:5000/predict-bulk', $data);

            if ($response->failed()) {
                $errorMsg = '❌ Gagal menghubungi API Flask.';
                if ($response->status() == 500) {
                    $errorMsg .= ' Server error - periksa log Flask API.';
                } elseif ($response->status() == 404) {
                    $errorMsg .= ' Endpoint tidak ditemukan.';
                }
                return back()->with('error', $errorMsg);
            }

            $responseData = $response->json();
            
            if (!isset($responseData['data']) || empty($responseData['data'])) {
                $errorDetail = isset($responseData['error']) ? $responseData['error'] : 'Unknown error';
                return back()->with('error', '❌ Tidak ada hasil prediksi: ' . $errorDetail);
            }

            $result = $responseData['data'];

            // Log hasil untuk debugging
            Log::info("Prediction completed", [
                'total_input' => count($data),
                'total_output' => count($result),
                'summary' => $responseData['summary'] ?? 'No summary'
            ]);

            // Kelompokkan dan hitung persentase per area
            $grouped = collect($result)->groupBy('area')->map(function ($items) {
                $total = count($items);
                $stunting = $items->where('status_gizi', 'Stunting')->count();
                $beresiko = $items->where('status_gizi', 'Beresiko Stunting')->count();
                $normal = $items->where('status_gizi', 'Normal')->count();

                return [
                    'total' => $total,
                    'stunting' => $stunting,
                    'beresiko' => $beresiko,
                    'normal' => $normal,
                    'persentase_stunting' => $total > 0 ? round(($stunting / $total) * 100, 2) : 0,
                    'persentase_beresiko' => $total > 0 ? round(($beresiko / $total) * 100, 2) : 0,
                    'persentase_normal' => $total > 0 ? round(($normal / $total) * 100, 2) : 0,
                    'persentase_buruk' => $total > 0 ? round((($stunting + $beresiko) / $total) * 100, 2) : 0
                ];
            });

            // Hitung dominasi status per area
            $statusAreaSummary = $grouped->map(function ($data) {
                $statuses = [
                    'Stunting' => $data['persentase_stunting'],
                    'Beresiko Stunting' => $data['persentase_beresiko'],
                    'Normal' => $data['persentase_normal'],
                ];
                
                return collect($statuses)->sortDesc()->keys()->first();
            })->countBy();

            // Hitung summary global
            $globalSummary = [
                'total_anak' => count($result),
                'total_stunting' => collect($result)->where('status_gizi', 'Stunting')->count(),
                'total_beresiko' => collect($result)->where('status_gizi', 'Beresiko Stunting')->count(),
                'total_normal' => collect($result)->where('status_gizi', 'Normal')->count(),
                'processing_info' => [
                    'total_input' => count($data),
                    'skipped_rows' => $skippedRows,
                    'success_rate' => count($data) > 0 ? round((count($result) / count($data)) * 100, 2) : 0
                ]
            ];

            // Info perbandingan dengan Fuzzy-AHP
            $comparisonInfo = [
                'method' => 'Random Forest (dari data Fuzzy-AHP)',
                'source_distribution' => $distribution,
                'sync_needed' => false
            ];

            return view('pengukuran.data_latih.bulk-result', compact(
                'result', 
                'grouped', 
                'statusAreaSummary',
                'globalSummary',
                'distribution',
                'comparisonInfo'
            ));

        } catch (\Exception $e) {
            Log::error('Error in bulk prediction: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk refresh/update data CSV dari database
     */
    public function refreshData()
    {
        try {
            // Panggil method export dari ReportController_E untuk generate CSV terbaru
            $reportController = new \App\Http\Controllers\ReportController_E();
            $result = $reportController->exportCsv();
            
            // Cek apakah berhasil
            if ($result->getSession()->has('success')) {
                return redirect()->route('prediksi.bulk.form')
                    ->with('success', '✅ Data berhasil diperbarui dan model siap dilatih ulang!');
            } else {
                return redirect()->route('prediksi.bulk.form')
                    ->with('error', '❌ Gagal memperbarui data.');
            }
        } catch (\Exception $e) {
            Log::error('Error refreshing data: ' . $e->getMessage());
            return redirect()->route('prediksi.bulk.form')
                ->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk latih ulang model dengan data yang sudah diperbaiki
     */
    public function retrainModel()
    {
        try {
            // Path untuk script training
            $scriptPath = base_path('ml/train_model.py');
            
            if (!file_exists($scriptPath)) {
                return back()->with('error', '❌ Script training tidak ditemukan. Pastikan file train_model_fixed.py ada di folder ml/');
            }

            // Jalankan script Python
            $command = "cd " . base_path() . " && python " . $scriptPath;
            $output = shell_exec($command . " 2>&1");
            
            Log::info('Retrain model output: ' . $output);
            
            // Cek apakah model berhasil dibuat
            $modelPath = base_path('ml/model_rf.pkl');
            if (file_exists($modelPath)) {
                return redirect()->route('prediksi.bulk.form')
                    ->with('success', '✅ Model berhasil dilatih ulang dengan data yang disinkronisasi dari Fuzzy-AHP!');
            } else {
                return back()->with('error', '❌ Gagal melatih model. Output: ' . $output);
            }
            
        } catch (\Exception $e) {
            Log::error('Error retraining model: ' . $e->getMessage());
            return back()->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}