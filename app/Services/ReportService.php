<?php

namespace App\Services;

use App\Models\PrediksiGizi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function generateReport(string $type, Carbon $start, Carbon $end, bool $includeCharts = true): array
    {
        // Ambil prediksi berdasarkan created_at (fallback jika pengukuran bermasalah)
        $predictions = PrediksiGizi::with(['pengukuran.balita'])
            ->whereHas('pengukuran', function ($query) use ($start, $end) {
                $query->whereBetween('tanggal_pengukuran', [$start, $end]);
            })
            ->get();

        $stats = [
            'total_balita' => $predictions->count(),
            'stunting' => $predictions->where('prediksi_status', 'stunting')->count(),
            'berisiko_stunting' => $predictions->where('prediksi_status', 'berisiko_stunting')->count(),
            'normal' => $predictions->where('prediksi_status', 'normal')->count(),
        ];

        // Hitung persentase
        $stats['stunting_pct'] = $this->getPercentage($stats['stunting'], $stats['total_balita']);
        $stats['berisiko_pct'] = $this->getPercentage($stats['berisiko_stunting'], $stats['total_balita']);

        // Grafik
        $charts = [];
        if ($includeCharts) {
            $charts['monthly_trend'] = $this->generateMonthlyTrend($start, $end);
            $charts['status_distribution'] = $this->generateStatusDistribution($predictions);
        }

        return compact('predictions', 'stats', 'charts');
    }

    private function getPercentage($count, $total): float
    {
        return $total > 0 ? round(($count / $total) * 100, 1) : 0;
    }

    private function generateMonthlyTrend(Carbon $start, Carbon $end): array
    {
        $months = [];
        $period = $start->copy()->startOfMonth();

        while ($period->lessThanOrEqualTo($end)) {
            $monthStart = $period->copy()->startOfMonth();
            $monthEnd = $period->copy()->endOfMonth();

            $data = PrediksiGizi::with('pengukuran')
                ->whereHas('pengukuran', function ($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('tanggal_pengukuran', [$monthStart, $monthEnd]);
                })
                ->get();
            $months[] = [
                'month' => $period->translatedFormat('M Y'),
                'stunting' => $data->where('prediksi_status', 'stunting')->count(),
                'berisiko' => $data->where('prediksi_status', 'berisiko_stunting')->count(),
                'normal' => $data->where('prediksi_status', 'normal')->count(),
            ];

            $period->addMonth();
        }

        return $months;
    }

    private function generateStatusDistribution(Collection $predictions): array
    {
        return $predictions->groupBy('prediksi_status')->map(function ($group, $status) {
            return [
                'status' => ucwords(str_replace('_', ' ', $status)),
                'count' => $group->count(),
            ];
        })->values()->toArray();
    }
}