<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Pengukuran;
use App\Models\PrediksiGizi;

class RandomForestController extends Controller
{
    private $apiUrl = 'http://127.0.0.1:5000';

    public function bulkForm()
    {
        $user = auth()->user();
        
        // Ambil data PENGUKURAN asli yang belum ada prediksi Random Forest
        // atau semua data jika ingin override prediksi lama
        $query = Pengukuran::with(['balita.masterPosyandu', 'balita.masterDesa']);
        
        // Filter berdasarkan role user
        if (!$user->isAdmin()) {
            $query->whereHas('balita', function ($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }
        
        $pengukuranData = $query->get();
        $dataCount = $pengukuranData->count();
        
        // Cek berapa yang sudah ada prediksi Random Forest
        $existingPredictions = PrediksiGizi::whereIn('pengukuran_id', $pengukuranData->pluck('id'))
                                          ->whereNotNull('final_score') // Indikator bahwa ini dari Fuzzy-AHP
                                          ->count();
        
        $newDataCount = $dataCount - $existingPredictions;
        
        // Status untuk tampilan
        $csvExists = $dataCount > 0;
        $lastUpdated = $dataCount > 0 ? $pengukuranData->max('updated_at')->timestamp : 0;
        
        // Analisis distribusi dari data Fuzzy-AHP yang sudah ada
        $existingDistribution = PrediksiGizi::whereIn('pengukuran_id', $pengukuranData->pluck('id'))
                                           ->whereNotNull('final_score')
                                           ->selectRaw('prediksi_status, count(*) as count')
                                           ->groupBy('prediksi_status')
                                           ->pluck('count', 'prediksi_status');
        
        // Konversi ke format yang diharapkan view
        $statusDistribution = [
            0 => $existingDistribution->get('normal', 0), // normal
            1 => $existingDistribution->get('berisiko_stunting', 0), // beresiko
            2 => $existingDistribution->get('stunting', 0) // stunting
        ];
        
        // Tentukan kualitas data
        $dataQuality = $this->assessDataQuality($statusDistribution, $existingPredictions);
        
        // Sync status dengan Fuzzy-AHP
        $syncStatus = $this->getSyncStatus($pengukuranData->pluck('id'));
        
        return view('pengukuran.data_latih.bulk-form', compact(
            'csvExists', 
            'dataCount', 
            'statusDistribution', 
            'lastUpdated', 
            'dataQuality',
            'syncStatus',
            'existingPredictions',
            'newDataCount'
        ));
    }
    
    public function bulkSubmit(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Ambil data PENGUKURAN asli
            $query = Pengukuran::with(['balita.masterPosyandu', 'balita.masterDesa']);
            
            if (!$user->isAdmin()) {
                $query->whereHas('balita', function ($q) use ($user) {
                    $q->where('posyandu', $user->posyandu_name);
                });
            }
            
            $pengukuranData = $query->get();
            
            if ($pengukuranData->isEmpty()) {
                return back()->with('error', 'Tidak ada data pengukuran yang tersedia untuk diprediksi.');
            }
            
            // Siapkan data untuk API Random Forest
            $apiData = [];
            foreach ($pengukuranData as $pengukuran) {
                $balita = $pengukuran->balita;
                if (!$balita) continue;
                
                $apiData[] = [
                    'pengukuran_id' => $pengukuran->id, // Tambahkan ID untuk mapping balik
                    'nama' => $balita->nama_balita,
                    'area' => $balita->area ?? 'Unknown',
                    'posyandu' => $balita->masterPosyandu->nama_posyandu ?? $balita->posyandu ?? 'Unknown',
                    'desa' => $balita->masterDesa->nama_desa ?? $balita->desa ?? 'Unknown',
                    'berat_badan' => (float) $pengukuran->berat_badan,
                    'tinggi_badan' => (float) $pengukuran->tinggi_badan,
                    'lingkar_kepala' => (float) $pengukuran->lingkar_kepala,
                    'lingkar_lengan' => (float) $pengukuran->lingkar_lengan,
                    'usia' => (float) $pengukuran->umur_bulan,
                    'asi_eksklusif' => $pengukuran->asi_eksklusif === 'ya' ? 1 : 0,
                    'status_imunisasi' => $pengukuran->imunisasi_lengkap === 'ya' ? 1 : 0,
                    'riwayat_penyakit' => $pengukuran->riwayat_penyakit === 'ya' ? 1 : 0,
                    'akses_air_bersih' => $pengukuran->akses_air_bersih === 'ya' ? 1 : 0,
                    'sanitasi_layak' => $pengukuran->sanitasi_layak === 'ya' ? 1 : 0
                ];
            }
            
            Log::info('Mengirim data ke Random Forest API', ['count' => count($apiData)]);
            
            // Kirim ke API Random Forest
            $response = Http::timeout(120)->post($this->apiUrl . '/predict-bulk', $apiData);
            
            if (!$response->successful()) {
                Log::error('API Error', ['status' => $response->status(), 'body' => $response->body()]);
                return back()->with('error', 'Gagal melakukan prediksi: ' . $response->body());
            }
            
            $result = $response->json();
            
            if (!isset($result['data'])) {
                return back()->with('error', 'Format response API tidak valid.');
            }
            
            // Simpan hasil ke database dengan struktur tabel yang benar
            $savedCount = 0;
            foreach ($result['data'] as $prediction) {
                // Cari pengukuran berdasarkan pengukuran_id jika ada, atau nama
                $pengukuran = null;
                
                if (isset($prediction['pengukuran_id'])) {
                    $pengukuran = $pengukuranData->firstWhere('id', $prediction['pengukuran_id']);
                } else {
                    // Fallback: cari berdasarkan nama dan area
                    $pengukuran = $pengukuranData->first(function($p) use ($prediction) {
                        return $p->balita && 
                               $p->balita->nama_balita === $prediction['nama'] &&
                               ($p->balita->area ?? 'Unknown') === $prediction['area'];
                    });
                }
                
                if ($pengukuran) {
                    // Update atau buat prediksi baru dengan struktur tabel yang benar
                    $prediksi = PrediksiGizi::updateOrCreate(
                        ['pengukuran_id' => $pengukuran->id],
                        [
                            // Data Random Forest (tanpa menghapus data Fuzzy-AHP yang sudah ada)
                            'prediksi_status' => $this->mapStatusCode($prediction['code']),
                            'confidence_level' => $prediction['confidence'],
                            'rekomendasi' => $this->generateRecommendation($prediction),
                            'prioritas' => $this->determinePriority($prediction),
                            'updated_at' => now()
                        ]
                    );
                    
                    // Jika ini adalah data baru (tidak ada final_score), set default values
                    if (is_null($prediksi->final_score)) {
                        $prediksi->update([
                            'final_score' => null, // Biarkan null untuk membedakan dengan Fuzzy-AHP
                            'created_at' => now()
                        ]);
                    }
                    
                    $savedCount++;
                }
            }
            
            Log::info('Prediksi Random Forest selesai', [
                'total_processed' => count($result['data']),
                'saved_to_db' => $savedCount
            ]);
            
            // Kelompokkan hasil per area untuk tampilan
            $grouped = $this->groupResultsByArea($result['data']);
            
            return view('pengukuran.data_latih.bulk-result', [
                'result' => $result['data'],
                'grouped' => $grouped,
                'summary' => $result['summary'] ?? [],
                'method' => 'Random Forest',
                'source' => 'pengukuran_real'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Random Forest Bulk Prediction Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    private function mapStatusCode($code)
    {
        return match($code) {
            0 => 'normal',
            1 => 'berisiko_stunting', 
            2 => 'stunting',
            default => 'normal'
        };
    }
    
    private function generateRecommendation($prediction)
    {
        $status = strtolower($prediction['status_gizi']);
        $confidence = $prediction['confidence'];
        
        $recommendations = [
            'stunting' => [
                'Konsultasi segera dengan tenaga kesehatan',
                'Pemberian makanan bergizi tinggi protein',
                'Monitoring pertumbuhan intensif',
                'Edukasi pola asuh yang baik'
            ],
            'beresiko stunting' => [
                'Perbaikan pola makan dan gizi',
                'Pemantauan berkala pertumbuhan',
                'Konseling gizi untuk orang tua',
                'Stimulasi tumbuh kembang'
            ],
            'normal' => [
                'Pertahankan pola makan seimbang',
                'Lanjutkan pemantauan rutin',
                'Berikan stimulasi sesuai usia'
            ]
        ];
        
        $baseRecommendations = $recommendations[$status] ?? $recommendations['normal'];
        
        if ($confidence < 70) {
            $baseRecommendations[] = 'Perlu evaluasi ulang dengan metode lain';
        }
        
        return implode('; ', $baseRecommendations);
    }
    
    private function determinePriority($prediction)
    {
        $status = strtolower($prediction['status_gizi']);
        $confidence = $prediction['confidence'];
        
        if ($status === 'stunting') {
            return 'tinggi';
        } elseif ($status === 'beresiko stunting') {
            return $confidence >= 80 ? 'tinggi' : 'sedang';
        } else {
            return 'rendah';
        }
    }
    
    private function assessDataQuality($distribution, $total)
    {
        if ($total < 10) return 'insufficient';
        if ($total == 0) return 'empty';
        
        $stunting = $distribution[2];
        $beresiko = $distribution[1];
        $normal = $distribution[0];
        
        // Cek keseimbangan
        $stuntingPct = $total > 0 ? ($stunting / $total) * 100 : 0;
        
        if ($stuntingPct == 0) return 'no_stunting';
        if ($stuntingPct < 5) return 'very_low_stunting';
        if ($stuntingPct > 50 || $normal < ($total * 0.1)) return 'imbalanced';
        
        return 'good';
    }
    
    private function getSyncStatus($pengukuranIds)
    {
        // Cek data Fuzzy-AHP yang sudah ada
        $fuzzyData = PrediksiGizi::whereIn('pengukuran_id', $pengukuranIds)
                                 ->whereNotNull('final_score') // Indikator Fuzzy-AHP
                                 ->get();
        
        $fuzzyDistribution = $fuzzyData->groupBy('prediksi_status')
                                      ->map->count()
                                      ->toArray();
        
        return [
            'fuzzy_total' => $fuzzyData->count(),
            'csv_exists' => $fuzzyData->count() > 0,
            'sync_needed' => false, // Karena data sudah ada di database
            'fuzzy_distribution' => $fuzzyDistribution,
            'csv_last_modified' => $fuzzyData->max('updated_at')?->format('d/m/Y H:i')
        ];
    }
    
    private function groupResultsByArea($results)
    {
        $grouped = [];
        
        foreach ($results as $result) {
            $area = $result['area'];
            
            if (!isset($grouped[$area])) {
                $grouped[$area] = [
                    'total' => 0,
                    'stunting' => 0,
                    'beresiko' => 0,
                    'normal' => 0
                ];
            }
            
            $grouped[$area]['total']++;
            
            $status = strtolower($result['status_gizi']);
            if ($status === 'stunting') {
                $grouped[$area]['stunting']++;
            } elseif (str_contains($status, 'beresiko')) {
                $grouped[$area]['beresiko']++;
            } else {
                $grouped[$area]['normal']++;
            }
        }
        
        // Tambahkan persentase
        foreach ($grouped as $area => &$data) {
            $total = $data['total'];
            $data['persentase_stunting'] = $total > 0 ? round(($data['stunting'] / $total) * 100, 1) : 0;
            $data['persentase_beresiko'] = $total > 0 ? round(($data['beresiko'] / $total) * 100, 1) : 0;
            $data['persentase_normal'] = $total > 0 ? round(($data['normal'] / $total) * 100, 1) : 0;
            $data['persentase_buruk'] = $data['persentase_stunting'] + $data['persentase_beresiko'];
        }
        
        return $grouped;
    }
    
    // Method untuk sync dari Fuzzy-AHP (menggunakan data yang sudah ada)
    public function syncFromFuzzy()
    {
        try {
            $user = auth()->user();
            
            // Ambil data prediksi yang sudah ada dari Fuzzy-AHP
            $query = PrediksiGizi::with(['pengukuran.balita'])
                                 ->whereNotNull('final_score'); // Yang sudah ada hasil Fuzzy-AHP
            
            if (!$user->isAdmin()) {
                $query->whereHas('pengukuran.balita', function ($q) use ($user) {
                    $q->where('posyandu', $user->posyandu_name);
                });
            }
            
            $fuzzyPredictions = $query->get();
            
            if ($fuzzyPredictions->isEmpty()) {
                return back()->with('warning', 'Tidak ada data Fuzzy-AHP yang dapat disinkronisasi.');
            }
            
            return back()->with('success', "Data Fuzzy-AHP ({$fuzzyPredictions->count()} records) sudah tersedia untuk training Random Forest.");
            
        } catch (\Exception $e) {
            Log::error('Sync Fuzzy-AHP Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal melakukan sinkronisasi: ' . $e->getMessage());
        }
    }
    
    // Method untuk retrain model menggunakan data Fuzzy-AHP yang sudah ada
    public function retrain()
    {
        try {
            // Export data untuk training dari PrediksiGizi yang sudah ada
            $user = auth()->user();
            
            $query = PrediksiGizi::with([
                'pengukuran.balita.masterPosyandu',
                'pengukuran.balita.masterDesa'
            ])->whereNotNull('final_score'); // Data dari Fuzzy-AHP
            
            if (!$user->isAdmin()) {
                $query->whereHas('pengukuran.balita', function ($q) use ($user) {
                    $q->where('posyandu', $user->posyandu_name);
                });
            }
            
            $predictions = $query->get();
            
            if ($predictions->isEmpty()) {
                return back()->with('error', 'Tidak ada data training yang tersedia. Jalankan sistem Fuzzy-AHP terlebih dahulu.');
            }
            
            // Tentukan path dan buat folder jika belum ada
            $csvPath = storage_path('app/ml/data_latih.csv');
            if (!file_exists(dirname($csvPath))) {
                mkdir(dirname($csvPath), 0755, true);
            }
            
            // Simpan data ke CSV
            $file = fopen($csvPath, 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8
            
            // Header CSV
            $columns = [
                'nama',
                'area',
                'posyandu', 
                'desa',
                'berat_badan',
                'tinggi_badan',
                'lingkar_kepala',
                'lingkar_lengan',
                'usia',
                'asi_eksklusif',
                'status_imunisasi',
                'riwayat_penyakit',
                'akses_air_bersih',
                'sanitasi_layak',
                'status_stunting'
            ];
            fputcsv($file, $columns);
            
            foreach ($predictions as $prediction) {
                $m = $prediction->pengukuran;
                $b = $m->balita ?? null;
                
                if (!$m || !$b) continue;
                
                // Konversi status untuk Random Forest
                $statusCode = match($prediction->prediksi_status) {
                    'normal' => 0,
                    'berisiko_stunting' => 1,
                    'stunting' => 2,
                    default => 0
                };
                
                fputcsv($file, [
                    $b->nama_balita,
                    $b->area,
                    $b->masterPosyandu->nama_posyandu ?? $b->posyandu,
                    $b->masterDesa->nama_desa ?? $b->desa,
                    $m->berat_badan,
                    $m->tinggi_badan,
                    $m->lingkar_kepala,
                    $m->lingkar_lengan,
                    $m->umur_bulan,
                    $m->asi_eksklusif === 'ya' ? 1 : 0,
                    $m->imunisasi_lengkap === 'ya' ? 1 : 0,
                    $m->riwayat_penyakit === 'ya' ? 1 : 0,
                    $m->akses_air_bersih === 'ya' ? 1 : 0,
                    $m->sanitasi_layak === 'ya' ? 1 : 0,
                    $statusCode
                ]);
            }
            
            fclose($file);
            
            // Jalankan Python script training
            $python = 'python';
            $scriptPath = base_path('ml/train_model.py');
            
            $descriptorspec = [
                1 => ['pipe', 'w'], // stdout
                2 => ['pipe', 'w'], // stderr
            ];
            
            $process = proc_open("$python \"$scriptPath\"", $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                $stdout = stream_get_contents($pipes[1]);
                $stderr = stream_get_contents($pipes[2]);
                
                fclose($pipes[1]);
                fclose($pipes[2]);
                
                $exitCode = proc_close($process);
                
                if ($exitCode === 0) {
                    return back()->with('success', "Model Random Forest berhasil dilatih ulang dengan {$predictions->count()} data dari sistem Fuzzy-AHP!");
                } else {
                    return back()->with('error', "Gagal melatih model.<strong>Error:</strong><pre>$stderr</pre>");
                }
            } else {
                return back()->with('error', "Gagal menjalankan proses Python.");
            }
            
        } catch (\Exception $e) {
            Log::error('Retrain Model Error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal melatih model: ' . $e->getMessage());
        }
    }
}