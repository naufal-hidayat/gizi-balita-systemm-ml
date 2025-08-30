<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $reportType = $request->input('report_type');
        $format = $request->input('format', 'view');
        $includeCharts = $request->isMethod('GET') ? true : $request->has('include_charts');

        if (!in_array($reportType, ['monthly', 'yearly', 'custom'])) {
            return back()->with('error', 'Jenis laporan tidak valid.');
        }

        // Hitung periode
        switch ($reportType) {
            case 'monthly':
                $monthInput = $request->month ?? now()->format('Y-m');
                try {
                    // Cek format: jika bukan Y-m, berarti coba F Y (e.g. "Juli 2025")
                    if (!preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
                        $date = Carbon::createFromFormat('F Y', $monthInput);
                    } else {
                        $date = Carbon::createFromFormat('Y-m', $monthInput);
                    }
                } catch (\Exception $e) {
                    return back()->with('error', 'Format bulan tidak valid: ' . $monthInput);
                }
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();

                break;

            case 'yearly':
                $year = $request->year ?? now()->year;
                $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
                $end = Carbon::createFromDate($year, 12, 31)->endOfYear();
                break;

            case 'custom':
                $request->validate([
                    'period_start' => 'required|date',
                    'period_end' => 'required|date|after_or_equal:period_start',
                ]);
                $start = Carbon::parse($request->period_start)->startOfDay();
                $end = Carbon::parse($request->period_end)->endOfDay();
                break;
        }

        // Ambil data laporan
        $reportData = $this->reportService->generateReport($reportType, $start, $end, $includeCharts);

        // Tambahkan metadata
        $reportData['report_type'] = $reportType;
        $reportData['period_start'] = $start;
        $reportData['period_end'] = $end;
        $reportData['generated_at'] = now();
        $reportData['generated_by'] = Auth::user()->name ?? 'Guest';

        // Return view/pdf/json
        switch ($format) {
            case 'view':
                return view('reports.result', compact('reportData'));

            case 'pdf':
                $pdf = \PDF::loadView('reports.pdf', compact('reportData'));
                return $pdf->download('laporan-gizi-balita.pdf');

            case 'excel':
                abort(501, 'Export Excel belum tersedia.');

            case 'json':
                return response()->json($reportData);

            default:
                abort(400, 'Format output tidak dikenali.');
        }
    }

    public function export(Request $request)
    {
        $reportType = $request->report_type;
        $start = Carbon::parse($request->period_start)->startOfDay();
        $end = Carbon::parse($request->period_end)->endOfDay();

        $includeCharts = true;
        $reportData = $this->reportService->generateReport($reportType, $start, $end, $includeCharts);

        $reportData['report_type'] = $reportType;
        $reportData['period_start'] = $start;
        $reportData['period_end'] = $end;
        $reportData['generated_at'] = now();
        $reportData['generated_by'] = Auth::user()->name ?? 'Guest';

        $pdf = \PDF::loadView('reports.pdf', compact('reportData'));
        return $pdf->download('laporan-gizi-balita.pdf');
    }
}