<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrediksiGizi;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Pengukuran;


class ApiController extends Controller
{
    public function exportForEppgbm()
    {
        $user = auth()->user();

        $query = Pengukuran::with(['balita']);

        if (!$user->isAdmin()) {
            $query->whereHas('balita', function($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }

        $pengukurans = $query->get();

        $response = new StreamedResponse(function() use ($pengukurans) {
            $handle = fopen('php://output', 'w');

            // ✅ Header kolom CSV LENGKAP
            fputcsv($handle, [
                'NIK Balita',
                'Nama Balita',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'Posyandu',
                'Desa',
                'Area',
                'Tanggal Pengukuran',
                'Umur Bulan',
                'Berat Badan',
                'Tinggi Badan',
                'Lingkar Kepala',
                'Lingkar Lengan',
                'ASI Eksklusif',
                'Imunisasi Lengkap',
                'Riwayat Penyakit',
                'Pendapatan Keluarga',
                'Pendidikan Ibu',
                'Jumlah Anggota Keluarga',
                'Akses Air Bersih',
                'Sanitasi Layak'
            ]);

            foreach ($pengukurans as $pengukuran) {
                fputcsv($handle, [
                    $pengukuran->balita->nik_balita ?? '',
                    $pengukuran->balita->nama_balita ?? '',
                    $pengukuran->balita->tanggal_lahir ?? '',
                    $pengukuran->balita->jenis_kelamin ?? '',
                    $pengukuran->balita->posyandu ?? '',
                    $pengukuran->balita->desa ?? '',
                    $pengukuran->balita->area ?? '',
                    $pengukuran->tanggal_pengukuran ?? '',
                    $pengukuran->umur_bulan ?? '',
                    $pengukuran->berat_badan ?? '',
                    $pengukuran->tinggi_badan ?? '',
                    $pengukuran->lingkar_kepala ?? '',
                    $pengukuran->lingkar_lengan ?? '',
                    $pengukuran->asi_eksklusif ?? '',
                    $pengukuran->imunisasi_lengkap ?? '',
                    $pengukuran->riwayat_penyakit ?? '',
                    $pengukuran->pendapatan_keluarga ?? '',
                    $pengukuran->pendidikan_ibu ?? '',
                    $pengukuran->jumlah_anggota_keluarga ?? '',
                    $pengukuran->akses_air_bersih ?? '',
                    $pengukuran->sanitasi_layak ?? ''
                ]);
            }

            fclose($handle);
        });

        $fileName = 'data_lengkap_' . now()->format('Ymd_His') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}