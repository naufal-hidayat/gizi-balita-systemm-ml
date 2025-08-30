<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PrediksiController extends Controller
{
    public function form()
    {
        return view('pengukuran.data_latih.prediksi');
    }

    public function submit(Request $request)
    {
        $umur = $request->umur;
        $berat = $request->berat;
        $tinggi = $request->tinggi;

        $validated = $request->validate([
            'nama' => 'required|string',
            'berat' => 'required|numeric',
            'tinggi' => 'required|numeric',
            'lingkar_kepala' => 'required|numeric',
            'lingkar_lengan' => 'required|numeric',
            'usia' => 'required|numeric',
            'asi_eksklusif' => 'required|in:0,1',
            'imunisasi_lengkap' => 'required|in:0,1',
            'riwayat_penyakit' => 'required|in:0,1',
            'akses_air_bersih' => 'required|in:0,1',
            'sanitasi_layak' => 'required|in:0,1',
        ]);

        $response = Http::post('http://127.0.0.1:5000/predict', [
            'berat' => $validated['berat'],
            'tinggi' => $validated['tinggi'],
            'lingkar_kepala' => $validated['lingkar_kepala'],
            'lingkar_lengan' => $validated['lingkar_lengan'],
            'usia' => $validated['usia'],
            'asi_eksklusif' => $validated['asi_eksklusif'],
            'imunisasi_lengkap' => $validated['imunisasi_lengkap'],
            'riwayat_penyakit' => $validated['riwayat_penyakit'],
            'akses_air_bersih' => $validated['akses_air_bersih'],
            'sanitasi_layak' => $validated['sanitasi_layak'],
        ]);

        if ($response->failed() || !isset($response['status_gizi'])) {
            return redirect()->back()->with('error', '❌ Gagal memproses prediksi. Pastikan server Flask aktif dan data valid.');
        }

        $hasil = $response['status_gizi'];

        return redirect()->route('prediksi.form')->with('hasil', $hasil);
    }

    public function prediksiSemua()
    {
        $filePath = storage_path('app/data_latih.csv');
        $modelPath = storage_path('app/model_rf_compatible.pkl');

        // Load dataset
        $df = new \Phpml\Dataset\CsvDataset($filePath, 12, true); // 12 fitur, true = header

        // Konversi ke DataFrame-like array
        $features = [];
        $labels = [];
        foreach ($df as $row) {
            $features[] = array_slice($row, 1, 11); // skip nama
            $labels[] = $row[12]; // status_stunting
        }

        // Load model dari Python (via subprocess Flask API atau langsung joblib via py::call, tergantung setup)
        // Simulasi prediksi:
        $predictions = $labels; // <- Ganti dengan hasil prediksi model

        // Hitung irisan untuk diagram Venn
        $pred_stunting = collect($predictions)->filter(fn($v, $k) => $v === 'stunting')->keys();
        $actual_stunting = collect($labels)->filter(fn($v, $k) => $v === 'stunting')->keys();

        // Kirim ke blade
        return view('data_latih.semua', compact('pred_stunting', 'actual_stunting'));
    }
}
