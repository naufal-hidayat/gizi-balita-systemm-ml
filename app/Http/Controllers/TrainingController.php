<?php

namespace App\Http\Controllers;

use App\Models\PrediksiGizi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function train()
    {
        // ✅ Batasi akses hanya untuk admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa melatih model.');
        }

        // ✅ Ambil data prediksi yang siap dilatih
        $predictions = PrediksiGizi::with(['pengukuran.balita'])->get();

        // ✅ Validasi: data harus ada
        if ($predictions->isEmpty()) {
            return back()->with('error', '❌ Data kosong, tidak bisa melatih model.');
        }

        // ✅ Siapkan path dan simpan data ke CSV
        $csvPath = storage_path('app/ml/data_latih.csv');
        File::ensureDirectoryExists(dirname($csvPath));

        $file = fopen($csvPath, 'w');
        fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8

        $columns = [
            'nama',
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

            fputcsv($file, [
                $b->nama_balita,
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

        // ✅ Jalankan Python script
        $python = 'python'; // ganti dengan path absolut jika perlu
        $scriptPath = base_path('ml/train_model.py');
        $logPath = storage_path('logs/training_log.txt');

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

            // ✅ Simpan log ke file
            $logContent = now()->format('Y-m-d H:i:s') . "\n" .
                "----------------------\n" .
                $stdout . $stderr . "\n\n";
            File::append($logPath, $logContent);

            if ($exitCode === 0) {
                // ✅ Simpan waktu retraining terakhir
                Storage::put('ml/last_training.txt', now()->toDateTimeString());
                return back()->with('success', '✅ Model berhasil dilatih ulang!');
            } else {
                return back()->with('error', "❌ Gagal melatih model.<pre>$stderr</pre>");
            }
        }

        return back()->with('error', '❌ Gagal menjalankan proses training.');
    }
}
