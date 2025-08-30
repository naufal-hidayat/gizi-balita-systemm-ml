<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\Pengukuran;
use App\Models\PrediksiGizi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Total balita & total pengukuran (scope posyandu kalau non-admin)
        $totalBalita = $user->isAdmin()
            ? Balita::count()
            : Balita::where('posyandu', $user->posyandu_name)->count();

        $totalPengukuran = Pengukuran::query()
            ->when(!$user->isAdmin(), function ($q) use ($user) {
                $q->whereHas('balita', fn($qq) => $qq->where('posyandu', $user->posyandu_name));
            })
            ->count();

        // === Hitung status TERKINI per balita (biar tidak melebihi total dan tidak minus)
        // Balita harus punya latestPengukuran->prediksiGizi (relasi ini diasumsikan sudah ada di model Balita)
        $balitaLatest = Balita::with(['latestPengukuran.prediksiGizi'])
            ->when(!$user->isAdmin(), fn($q) => $q->where('posyandu', $user->posyandu_name))
            ->get();

        $stuntingCount = $balitaLatest->filter(function ($b) {
            return optional(optional($b->latestPengukuran)->prediksiGizi)->prediksi_status === 'stunting';
        })->count();

        $berisiko = $balitaLatest->filter(function ($b) {
            return optional(optional($b->latestPengukuran)->prediksiGizi)->prediksi_status === 'berisiko_stunting';
        })->count();

        $normalCount = max(0, $totalBalita - $stuntingCount - $berisiko);
        $normalPercent = $totalBalita > 0
            ? max(0, min(100, round(($normalCount / $totalBalita) * 100, 1)))
            : 0;

        // === Pengukuran terbaru (hindari orphan)
        $recentMeasurements = Pengukuran::with([
            'balita:id,nama_balita,jenis_kelamin',
            'prediksiGizi:id,pengukuran_id,prediksi_status,confidence_level'
        ])
            ->whereHas('balita')
            ->when(!$user->isAdmin(), function ($q) use ($user) {
                $q->whereHas('balita', fn($qq) => $qq->where('posyandu', $user->posyandu_name));
            })
            ->latest('tanggal_pengukuran')
            ->take(10)
            ->get();

        // === Data grafik 6 bulan terakhir (berdasarkan waktu dibuatnya prediksi)
        $chartData = $this->getChartData($user);

        return view('dashboard', compact(
            'totalBalita',
            'totalPengukuran',
            'stuntingCount',
            'berisiko',
            'normalCount',
            'normalPercent',
            'recentMeasurements',
            'chartData'
        ));
    }

    private function getChartData($user)
    {
        $months = [];
        $stuntingData = [];
        $berisiko = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $base = PrediksiGizi::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            if (!$user->isAdmin()) {
                $base->whereHas('pengukuran.balita', function ($q) use ($user) {
                    $q->where('posyandu', $user->posyandu_name);
                });
            }

            $stuntingData[] = (clone $base)->where('prediksi_status', 'stunting')->count();
            $berisiko[]     = (clone $base)->where('prediksi_status', 'berisiko_stunting')->count();
        }

        return [
            'months'   => $months,
            'stunting' => $stuntingData,
            'berisiko' => $berisiko,
        ];
    }
}
