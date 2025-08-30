<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class PrediksiBulkController_C extends Controller
{
    public function index()
    {
        // Cek apakah file CSV ada
        $csvPath = storage_path('app/ml/data_latih.csv');
        $csvExists = File::exists($csvPath);
        
        // Jika file ada, ambil info jumlah data
        $dataCount = 0;
        $lastUpdated = null;
        
        if ($csvExists) {
            $dataCount = count(file($csvPath)) - 1; // minus header
            $lastUpdated = File::lastModified($csvPath);
        }
        
        return view('pengukuran.data_latih.bulk-form', compact('csvExists', 'dataCount', 'lastUpdated'));
    }

    public function predict(Request $request)
    {
        // Path ke file CSV yang sudah ada
        $csvPath = storage_path('app/ml/data_latih.csv');
        
        // Cek apakah file CSV ada
        if (!File::exists($csvPath)) {
            return back()->with('error', '❌ File data latih tidak ditemukan. Silakan latih model terlebih dahulu melalui menu Export & Training.');
        }

        try {
            // Baca file CSV
            $csvContent = file($csvPath);
            
            if (empty($csvContent)) {
                return back()->with('error', '❌ File CSV kosong.');
            }

            // Parse CSV
            $header = str_getcsv(array_shift($csvContent));
            $data = [];

            foreach ($csvContent as $index => $row) {
                $rowData = str_getcsv($row);
                
                if (count($rowData) !== count($header)) {
                    continue; // Skip baris yang tidak valid
                }

                $combinedData = array_combine($header, $rowData);
                
                // Format data untuk API Flask
                $data[] = [
                    'nama' => $combinedData['nama'] ?? 'Unknown',
                    'area' => $combinedData['area'] ?? 'Unknown',
                    'posyandu' => $combinedData['posyandu'] ?? 'Unknown',
                    'desa' => $combinedData['desa'] ?? 'Unknown',
                    'berat_badan' => floatval($combinedData['berat_badan'] ?? 0),
                    'tinggi_badan' => floatval($combinedData['tinggi_badan'] ?? 0),
                    'lingkar_kepala' => floatval($combinedData['lingkar_kepala'] ?? 0),
                    'lingkar_lengan' => floatval($combinedData['lingkar_lengan'] ?? 0),
                    'usia' => floatval($combinedData['usia'] ?? 0),
                    'asi_eksklusif' => intval($combinedData['asi_eksklusif'] ?? 0),
                    'status_imunisasi' => intval($combinedData['status_imunisasi'] ?? 0),
                    'riwayat_penyakit' => intval($combinedData['riwayat_penyakit'] ?? 0),
                    'akses_air_bersih' => intval($combinedData['akses_air_bersih'] ?? 0),
                    'sanitasi_layak' => intval($combinedData['sanitasi_layak'] ?? 0),
                ];
            }

            if (empty($data)) {
                return back()->with('error', '❌ Tidak ada data valid yang dapat diproses.');
            }

            // Kirim ke API Flask untuk prediksi
            $response = Http::timeout(60)->post('http://127.0.0.1:5000/predict-bulk', $data);

            if ($response->failed()) {
                return back()->with('error', '❌ Gagal menghubungi API Flask. Pastikan server Flask sedang berjalan di port 5000.');
            }

            $result = $response->json()['data'] ?? [];

            if (empty($result)) {
                return back()->with('error', '❌ Tidak ada hasil prediksi yang diterima dari API.');
            }

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

            return view('pengukuran.data_latih.bulk-result', compact('result', 'grouped', 'statusAreaSummary'));

        } catch (\Exception $e) {
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
                    ->with('success', '✅ Data berhasil diperbarui dan model dilatih ulang!');
            } else {
                return redirect()->route('prediksi.bulk.form')
                    ->with('error', '❌ Gagal memperbarui data.');
            }
        } catch (\Exception $e) {
            return redirect()->route('prediksi.bulk.form')
                ->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}