<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use App\Models\PrediksiGizi;

class ReportController_E extends Controller
{
    public function exportCsv()
    {
        $user = auth()->user();

        // ✅ Tambahkan eager loading untuk masterPosyandu dan masterDesa
        $query = PrediksiGizi::with([
            'pengukuran.balita.masterPosyandu',
            'pengukuran.balita.masterDesa'
        ]);

        // Jika bukan admin, filter berdasarkan posyandu
        if (!$user->isAdmin()) {
            $query->whereHas('pengukuran.balita', function ($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }

        $predictions = $query->get();

        // Tentukan path dan buat folder jika belum ada
        $csvPath = storage_path('app/ml/data_latih.csv');
        File::ensureDirectoryExists(dirname($csvPath));

        // Simpan data ke CSV
        $file = fopen($csvPath, 'w');
        fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8

        // ✅ Tambahkan kolom area, posyandu, dan desa
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

            // ✅ Tambahkan data area, posyandu, dan desa sesuai dengan balita controller
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
                $prediction->prediksi_status === 'stunting' ? 1 : 0
            ]);
        }

        fclose($file);

        // ✅ Jalankan Python script training menggunakan proc_open
        $python = 'python'; // Ganti ke full path python.exe jika perlu
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
                return back()->with('success', "✅ Model berhasil dilatih ulang!" );
            } else {
                return back()->with('error', "❌ Gagal melatih model.<strong>Error:</strong><pre>$stderr</pre>");
            }
        } else {
            return back()->with('error', "❌ Gagal menjalankan proses Python.");
        }
    }
}