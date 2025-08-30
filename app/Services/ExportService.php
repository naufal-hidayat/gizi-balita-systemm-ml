<?php

namespace App\Services;

use App\Models\PrediksiGizi;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function exportForEppgbm($user, $format = 'json')
    {
        $query = PrediksiGizi::with(['pengukuran.balita']);
        
        if (!$user->isAdmin()) {
            $query->whereHas('pengukuran.balita', function($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }
        
        $predictions = $query->get();
        
        $exportData = $predictions->map(function($prediction) {
            return [
                'nik_balita' => $prediction->pengukuran->balita->nik_balita,
                'nama_balita' => $prediction->pengukuran->balita->nama_balita,
                'tanggal_lahir' => $prediction->pengukuran->balita->tanggal_lahir->format('Y-m-d'),
                'jenis_kelamin' => $prediction->pengukuran->balita->jenis_kelamin,
                'tanggal_pengukuran' => $prediction->pengukuran->tanggal_pengukuran->format('Y-m-d'),
                'umur_bulan' => $prediction->pengukuran->umur_bulan,
                'berat_badan' => $prediction->pengukuran->berat_badan,
                'tinggi_badan' => $prediction->pengukuran->tinggi_badan,
                'zscore_bb_u' => $prediction->zscore_bb_u,
                'zscore_tb_u' => $prediction->zscore_tb_u,
                'zscore_bb_tb' => $prediction->zscore_bb_tb,
                'status_gizi' => $prediction->prediksi_status,
                'confidence_level' => $prediction->confidence_level,
                'prioritas' => $prediction->prioritas,
                'rekomendasi' => $prediction->rekomendasi,
                'posyandu' => $prediction->pengukuran->balita->posyandu,
                'desa' => $prediction->pengukuran->balita->desa,
                'exported_at' => now()->toISOString(),
            ];
        });
        
        $result = [
            'metadata' => [
                'source_system' => 'Sistem Prediksi Gizi Balita Fuzzy-AHP',
                'target_system' => 'E-PPGBM',
                'export_date' => now()->toISOString(),
                'exported_by' => $user->name,
                'total_records' => $exportData->count(),
                'format_version' => '1.0',
            ],
            'data' => $exportData->toArray(),
        ];

        if ($format === 'file') {
            $filename = 'export_eppgbm_' . now()->format('Y_m_d_His') . '.json';
            Storage::disk('public')->put('exports/' . $filename, json_encode($result, JSON_PRETTY_PRINT));
            
            return [
                'status' => 'success',
                'file_path' => 'exports/' . $filename,
                'download_url' => Storage::url('exports/' . $filename),
                'metadata' => $result['metadata'],
            ];
        }

        return $result;
    }
}