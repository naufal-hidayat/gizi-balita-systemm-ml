<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SyncFuzzyToRFController extends Controller
{
    /**
     * Sinkronisasi data dari tabel prediksi_gizi (Fuzzy-AHP) ke CSV untuk Random Forest
     */
    public function syncFuzzyToRandomForest()
    {
        try {
            // Cek struktur tabel yang tersedia
            $tableStructure = $this->analyzeTableStructure();
            
            if (!$tableStructure['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $tableStructure['message']
                ]);
            }

            // Ambil data dari tabel prediksi_gizi dengan query yang disesuaikan
            $data = $this->fetchPredictionData($tableStructure);

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data prediksi Fuzzy-AHP yang ditemukan'
                ]);
            }

            // Konversi data ke format Random Forest
            $convertedData = $this->convertDataToRFFormat($data);

            // Analisis distribusi
            $distribution = [
                0 => $convertedData->where('status_stunting', 0)->count(), // Normal
                1 => $convertedData->where('status_stunting', 1)->count(), // Beresiko
                2 => $convertedData->where('status_stunting', 2)->count()  // Stunting
            ];

            $total = $convertedData->count();
            
            Log::info('Fuzzy-AHP to RF Sync Analysis', [
                'total_records' => $total,
                'distribution' => $distribution,
                'percentages' => [
                    'normal' => $total > 0 ? round(($distribution[0] / $total) * 100, 1) : 0,
                    'beresiko' => $total > 0 ? round(($distribution[1] / $total) * 100, 1) : 0,
                    'stunting' => $total > 0 ? round(($distribution[2] / $total) * 100, 1) : 0
                ]
            ]);

            // Simpan ke CSV
            $csvPath = storage_path('app/ml/data_latih_from_fuzzy.csv');
            $this->saveToCSV($convertedData, $csvPath);

            // Backup data lama dan replace
            $originalCsvPath = storage_path('app/ml/data_latih.csv');
            if (File::exists($originalCsvPath)) {
                $backupPath = storage_path('app/ml/data_latih_backup_' . date('Y-m-d_H-i-s') . '.csv');
                File::copy($originalCsvPath, $backupPath);
                Log::info('Backup created: ' . $backupPath);
            }

            // Copy file baru ke lokasi utama
            File::copy($csvPath, $originalCsvPath);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disinkronisasi dari Fuzzy-AHP ke Random Forest',
                'data' => [
                    'total_records' => $total,
                    'distribution' => $distribution,
                    'file_path' => $originalCsvPath,
                    'backup_created' => File::exists($backupPath ?? '') ? ($backupPath ?? '') : null,
                    'table_structure' => $tableStructure['info']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in syncFuzzyToRandomForest: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analisis struktur tabel yang tersedia
     */
    private function analyzeTableStructure()
    {
        try {
            // Cek tabel yang tersedia
            $tables = DB::select("SHOW TABLES");
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);

            $availableTables = [
                'prediksi_gizi' => in_array('prediksi_gizi', $tableNames),
                'pengukuran' => in_array('pengukuran', $tableNames),
                'anak' => in_array('anak', $tableNames),
                'posyandu' => in_array('posyandu', $tableNames),
                'desa' => in_array('desa', $tableNames)
            ];

            Log::info('Available tables:', $availableTables);

            if (!$availableTables['prediksi_gizi']) {
                return [
                    'success' => false,
                    'message' => 'Tabel prediksi_gizi tidak ditemukan'
                ];
            }

            // Cek kolom di tabel prediksi_gizi
            $prediksiColumns = Schema::getColumnListing('prediksi_gizi');
            Log::info('prediksi_gizi columns:', $prediksiColumns);

            // Cek kolom di tabel pengukuran jika ada
            $pengukuranColumns = [];
            if ($availableTables['pengukuran']) {
                $pengukuranColumns = Schema::getColumnListing('pengukuran');
                Log::info('pengukuran columns:', $pengukuranColumns);
            }

            return [
                'success' => true,
                'info' => [
                    'available_tables' => $availableTables,
                    'prediksi_columns' => $prediksiColumns,
                    'pengukuran_columns' => $pengukuranColumns
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error analyzing table structure: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error analyzing table structure: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ambil data prediksi dengan query yang disesuaikan
     */
    private function fetchPredictionData($tableStructure)
    {
        $availableTables = $tableStructure['info']['available_tables'];
        
        try {
            // Query sederhana untuk mengambil data dari prediksi_gizi saja
            if (!$availableTables['pengukuran']) {
                // Jika tidak ada tabel pengukuran, gunakan data dari prediksi_gizi saja
                return DB::table('prediksi_gizi')
                    ->select([
                        DB::raw("'Data-' || id as nama"),
                        DB::raw("'area_" . (rand(1,4)) . "' as area"),
                        DB::raw("'Posyandu-' || (id % 10 + 1) as posyandu"),
                        DB::raw("'Desa-' || (id % 5 + 1) as desa"),
                        DB::raw("CASE 
                            WHEN zscore_bb_u > 0 THEN 15.0 + (zscore_bb_u * 2)
                            ELSE 12.0 + (zscore_bb_u * 1.5)
                        END as berat_badan"),
                        DB::raw("CASE 
                            WHEN zscore_tb_u > 0 THEN 85.0 + (zscore_tb_u * 5)
                            ELSE 80.0 + (zscore_tb_u * 3)
                        END as tinggi_badan"),
                        DB::raw("45.0 + (zscore_bb_u * 1.5) as lingkar_kepala"),
                        DB::raw("14.0 + (zscore_bb_u * 1.0) as lingkar_lengan"),
                        DB::raw("24 + (id % 36) as usia"),
                        DB::raw("CASE WHEN (id % 3) = 0 THEN 0 ELSE 1 END as asi_eksklusif"),
                        DB::raw("CASE WHEN (id % 4) = 0 THEN 0 ELSE 1 END as status_imunisasi"),
                        DB::raw("CASE WHEN (id % 5) = 0 THEN 1 ELSE 0 END as riwayat_penyakit"),
                        DB::raw("CASE WHEN (id % 3) = 0 THEN 0 ELSE 1 END as akses_air_bersih"),
                        DB::raw("CASE WHEN (id % 4) = 0 THEN 0 ELSE 1 END as sanitasi_layak"),
                        'prediksi_status',
                        'confidence_level',
                        'zscore_tb_u',
                        'zscore_bb_u',
                        'zscore_bb_tb'
                    ])
                    ->whereNotNull('prediksi_status')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // Jika ada tabel pengukuran, coba join tapi dengan fallback
            if ($availableTables['pengukuran']) {
                try {
                    return DB::table('prediksi_gizi as pg')
                        ->join('pengukuran as p', 'pg.pengukuran_id', '=', 'p.id')
                        ->select([
                            DB::raw("CONCAT('Anak-', pg.id) as nama"),
                            DB::raw("CASE 
                                WHEN pg.id % 4 = 1 THEN 'timur'
                                WHEN pg.id % 4 = 2 THEN 'barat'
                                WHEN pg.id % 4 = 3 THEN 'utara'
                                ELSE 'selatan'
                            END as area"),
                            DB::raw("CONCAT('Posyandu-', (pg.id % 8 + 1)) as posyandu"),
                            DB::raw("CONCAT('Desa-', (pg.id % 6 + 1)) as desa"),
                            'p.berat_badan',
                            'p.tinggi_badan',
                            DB::raw("COALESCE(p.lingkar_kepala, 45.0 + (pg.zscore_bb_u * 1.5)) as lingkar_kepala"),
                            DB::raw("COALESCE(p.lingkar_lengan_atas, p.lingkar_lengan, 14.0 + (pg.zscore_bb_u * 1.0)) as lingkar_lengan"),
                            DB::raw("COALESCE(p.usia_bulan, p.usia, 24 + (pg.id % 36)) as usia"),
                            DB::raw("COALESCE(p.asi_eksklusif, CASE WHEN (pg.id % 3) = 0 THEN 0 ELSE 1 END) as asi_eksklusif"),
                            DB::raw("COALESCE(p.status_imunisasi, CASE WHEN (pg.id % 4) = 0 THEN 0 ELSE 1 END) as status_imunisasi"),
                            DB::raw("COALESCE(p.riwayat_penyakit, CASE WHEN (pg.id % 5) = 0 THEN 1 ELSE 0 END) as riwayat_penyakit"),
                            DB::raw("COALESCE(p.akses_air_bersih, CASE WHEN (pg.id % 3) = 0 THEN 0 ELSE 1 END) as akses_air_bersih"),
                            DB::raw("COALESCE(p.sanitasi_layak, CASE WHEN (pg.id % 4) = 0 THEN 0 ELSE 1 END) as sanitasi_layak"),
                            'pg.prediksi_status',
                            'pg.confidence_level',
                            'pg.zscore_tb_u',
                            'pg.zscore_bb_u',
                            'pg.zscore_bb_tb'
                        ])
                        ->whereNotNull('pg.prediksi_status')
                        ->orderBy('pg.created_at', 'desc')
                        ->get();
                } catch (\Exception $e) {
                    Log::warning('Failed to join with pengukuran table, using prediksi_gizi only: ' . $e->getMessage());
                    // Fallback ke query sederhana
                    return $this->fetchPredictionDataFallback();
                }
            }

        } catch (\Exception $e) {
            Log::error('Error in fetchPredictionData: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Fallback query jika join gagal
     */
    private function fetchPredictionDataFallback()
    {
        return DB::table('prediksi_gizi')
            ->select([
                DB::raw("CONCAT('Anak-', id) as nama"),
                DB::raw("CASE 
                    WHEN id % 4 = 1 THEN 'timur'
                    WHEN id % 4 = 2 THEN 'barat'
                    WHEN id % 4 = 3 THEN 'utara'
                    ELSE 'selatan'
                END as area"),
                DB::raw("CONCAT('Posyandu-', (id % 8 + 1)) as posyandu"),
                DB::raw("CONCAT('Desa-', (id % 6 + 1)) as desa"),
                DB::raw("CASE 
                    WHEN zscore_bb_u > 0 THEN 15.0 + (zscore_bb_u * 2)
                    WHEN zscore_bb_u < -2 THEN 8.0 + (zscore_bb_u * 1.0)
                    ELSE 12.0 + (zscore_bb_u * 1.5)
                END as berat_badan"),
                DB::raw("CASE 
                    WHEN zscore_tb_u > 0 THEN 85.0 + (zscore_tb_u * 5)
                    WHEN zscore_tb_u < -2 THEN 65.0 + (zscore_tb_u * 3)
                    ELSE 80.0 + (zscore_tb_u * 4)
                END as tinggi_badan"),
                DB::raw("45.0 + (zscore_bb_u * 1.5) as lingkar_kepala"),
                DB::raw("14.0 + (zscore_bb_u * 1.0) as lingkar_lengan"),
                DB::raw("24 + (id % 36) as usia"),
                DB::raw("CASE WHEN (id % 3) = 0 THEN 0 ELSE 1 END as asi_eksklusif"),
                DB::raw("CASE WHEN (id % 4) = 0 THEN 0 ELSE 1 END as status_imunisasi"),
                DB::raw("CASE WHEN (id % 5) = 0 THEN 1 ELSE 0 END as riwayat_penyakit"),
                DB::raw("CASE WHEN (id % 3) = 0 THEN 0 ELSE 1 END as akses_air_bersih"),
                DB::raw("CASE WHEN (id % 4) = 0 THEN 0 ELSE 1 END as sanitasi_layak"),
                'prediksi_status',
                'confidence_level',
                'zscore_tb_u',
                'zscore_bb_u',
                'zscore_bb_tb'
            ])
            ->whereNotNull('prediksi_status')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Konversi data ke format Random Forest
     */
    private function convertDataToRFFormat($data)
    {
        return $data->map(function ($row) {
            // Konversi status prediksi Fuzzy-AHP ke format Random Forest
            $status_stunting = $this->convertFuzzyStatusToRF($row->prediksi_status);
            
            return [
                'nama' => $row->nama ?? 'Unknown',
                'area' => $row->area ?? 'unknown',
                'posyandu' => $row->posyandu ?? 'Unknown',
                'desa' => $row->desa ?? 'Unknown',
                'berat_badan' => max(3.0, min(30.0, floatval($row->berat_badan ?? 12.0))),
                'tinggi_badan' => max(50.0, min(120.0, floatval($row->tinggi_badan ?? 80.0))),
                'lingkar_kepala' => max(35.0, min(60.0, floatval($row->lingkar_kepala ?? 45.0))),
                'lingkar_lengan' => max(10.0, min(25.0, floatval($row->lingkar_lengan ?? 14.0))),
                'usia' => max(6, min(60, intval($row->usia ?? 24))),
                'asi_eksklusif' => $this->convertToBoolean($row->asi_eksklusif ?? 1),
                'status_imunisasi' => $this->convertToBoolean($row->status_imunisasi ?? 1),
                'riwayat_penyakit' => $this->convertToBoolean($row->riwayat_penyakit ?? 0),
                'akses_air_bersih' => $this->convertToBoolean($row->akses_air_bersih ?? 1),
                'sanitasi_layak' => $this->convertToBoolean($row->sanitasi_layak ?? 1),
                'status_stunting' => $status_stunting,
                // Metadata tambahan untuk analisis
                'confidence_level' => floatval($row->confidence_level ?? 70.0),
                'zscore_tb_u' => floatval($row->zscore_tb_u ?? 0.0),
                'zscore_bb_u' => floatval($row->zscore_bb_u ?? 0.0),
                'zscore_bb_tb' => floatval($row->zscore_bb_tb ?? 0.0)
            ];
        });
    }

    /**
     * Konversi status prediksi Fuzzy-AHP ke format Random Forest
     */
    private function convertFuzzyStatusToRF($fuzzyStatus)
    {
        $status = strtolower(trim($fuzzyStatus));
        
        switch ($status) {
            case 'normal':
                return 0;
            case 'berisiko_stunting':
            case 'beresiko_stunting':
            case 'berisiko stunting':
            case 'beresiko':
                return 1;
            case 'stunting':
                return 2;
            default:
                // Log status yang tidak dikenali
                Log::warning('Unknown fuzzy status: ' . $fuzzyStatus);
                return 0; // Default ke normal
        }
    }

    /**
     * Konversi nilai ke boolean 0/1
     */
    private function convertToBoolean($value)
    {
        if (is_null($value)) return 0;
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            if (in_array($value, ['ya', 'yes', 'true', '1', 'lengkap', 'ada', 'baik'])) {
                return 1;
            } elseif (in_array($value, ['tidak', 'no', 'false', '0', 'tidak_lengkap', 'tidak_ada', 'buruk'])) {
                return 0;
            }
        }
        
        return intval($value) > 0 ? 1 : 0;
    }

    /**
     * Simpan data ke file CSV
     */
    private function saveToCSV($data, $filePath)
    {
        // Pastikan direktori ada
        $directory = dirname($filePath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Header CSV
        $headers = [
            'nama', 'area', 'posyandu', 'desa', 'berat_badan', 'tinggi_badan',
            'lingkar_kepala', 'lingkar_lengan', 'usia', 'asi_eksklusif',
            'status_imunisasi', 'riwayat_penyakit', 'akses_air_bersih',
            'sanitasi_layak', 'status_stunting'
        ];

        // Buka file untuk menulis
        $file = fopen($filePath, 'w');
        
        // Tulis header
        fputcsv($file, $headers);
        
        // Tulis data
        foreach ($data as $row) {
            $csvRow = [
                $row['nama'],
                $row['area'],
                $row['posyandu'],
                $row['desa'],
                $row['berat_badan'],
                $row['tinggi_badan'],
                $row['lingkar_kepala'],
                $row['lingkar_lengan'],
                $row['usia'],
                $row['asi_eksklusif'],
                $row['status_imunisasi'],
                $row['riwayat_penyakit'],
                $row['akses_air_bersih'],
                $row['sanitasi_layak'],
                $row['status_stunting']
            ];
            fputcsv($file, $csvRow);
        }
        
        fclose($file);
        
        Log::info('CSV saved successfully: ' . $filePath, [
            'records_count' => $data->count()
        ]);
    }

    /**
     * API endpoint untuk mendapatkan status sinkronisasi
     */
    public function getSyncStatus()
    {
        try {
            // Hitung data di tabel prediksi_gizi
            $fuzzyData = DB::table('prediksi_gizi')->count();
            $fuzzyDistribution = DB::table('prediksi_gizi')
                ->select('prediksi_status', DB::raw('count(*) as count'))
                ->whereNotNull('prediksi_status')
                ->groupBy('prediksi_status')
                ->get()
                ->pluck('count', 'prediksi_status')
                ->toArray();

            // Cek file CSV Random Forest
            $csvPath = storage_path('app/ml/data_latih.csv');
            $csvExists = File::exists($csvPath);
            $csvLastModified = $csvExists ? File::lastModified($csvPath) : null;

            return response()->json([
                'fuzzy_ahp' => [
                    'total_records' => $fuzzyData,
                    'distribution' => $fuzzyDistribution
                ],
                'random_forest_csv' => [
                    'exists' => $csvExists,
                    'last_modified' => $csvLastModified ? date('Y-m-d H:i:s', $csvLastModified) : null,
                    'path' => $csvPath
                ],
                'sync_needed' => $fuzzyData > 0 && (!$csvExists || $fuzzyData > 0)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting sync status: ' . $e->getMessage()
            ], 500);
        }
    }
}