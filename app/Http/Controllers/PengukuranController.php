<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengukuran;
use App\Models\Balita;
use App\Services\FuzzyAhpService;
use App\Http\Requests\PengukuranRequest;

class PengukuranController extends Controller
{
    protected $fuzzyAhpService;

    public function __construct(FuzzyAhpService $fuzzyAhpService)
    {
        $this->fuzzyAhpService = $fuzzyAhpService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Base query untuk stats (semua data sesuai filter)
        $baseQuery = Pengukuran::with(['balita', 'prediksiGizi', 'user']);
        
        // Query untuk pagination (akan di-clone dari baseQuery)
        $paginationQuery = clone $baseQuery;
        
        // Apply role-based filter
        if (!$user->isAdmin()) {
            $baseQuery->whereHas('balita', function ($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
            
            $paginationQuery->whereHas('balita', function ($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $baseQuery->whereHas('balita', function ($q) use ($request) {
                $q->where('nama_balita', 'like', '%' . $request->search . '%');
            });
            
            $paginationQuery->whereHas('balita', function ($q) use ($request) {
                $q->where('nama_balita', 'like', '%' . $request->search . '%');
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $baseQuery->whereHas('prediksiGizi', function ($q) use ($request) {
                $q->where('prediksi_status', $request->status);
            });
            
            $paginationQuery->whereHas('prediksiGizi', function ($q) use ($request) {
                $q->where('prediksi_status', $request->status);
            });
        }
        
        // Apply date range filter
        if ($request->filled('start_date')) {
            $baseQuery->where('tanggal_pengukuran', '>=', $request->start_date);
            $paginationQuery->where('tanggal_pengukuran', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $baseQuery->where('tanggal_pengukuran', '<=', $request->end_date);
            $paginationQuery->where('tanggal_pengukuran', '<=', $request->end_date);
        }
        
        // Apply sorting
        if ($request->filled('sort')) {
            if ($request->sort === 'name_asc') {
                $baseQuery->join('balita', 'pengukuran.balita_id', '=', 'balita.id')
                          ->orderBy('balita.nama_balita', 'asc')
                          ->select('pengukuran.*');
                
                $paginationQuery->join('balita as b2', 'pengukuran.balita_id', '=', 'b2.id')
                               ->orderBy('b2.nama_balita', 'asc')
                               ->select('pengukuran.*');
            } elseif ($request->sort === 'name_desc') {
                $baseQuery->join('balita', 'pengukuran.balita_id', '=', 'balita.id')
                          ->orderBy('balita.nama_balita', 'desc')
                          ->select('pengukuran.*');
                
                $paginationQuery->join('balita as b2', 'pengukuran.balita_id', '=', 'b2.id')
                               ->orderBy('b2.nama_balita', 'desc')
                               ->select('pengukuran.*');
            } elseif ($request->sort === 'date_desc') {
                $baseQuery->orderBy('tanggal_pengukuran', 'desc');
                $paginationQuery->orderBy('tanggal_pengukuran', 'desc');
            } elseif ($request->sort === 'date_asc') {
                $baseQuery->orderBy('tanggal_pengukuran', 'asc');
                $paginationQuery->orderBy('tanggal_pengukuran', 'asc');
            }
        } else {
            // Default sorting by date desc
            $baseQuery->orderBy('tanggal_pengukuran', 'desc');
            $paginationQuery->orderBy('tanggal_pengukuran', 'desc');
        }
        
        // Get all data for stats calculation
        $allPengukuran = $baseQuery->get();
        
        // Calculate stats
        $stats = [
            'total' => $allPengukuran->count(),
            'stunting' => $allPengukuran->filter(function($p) {
                return $p->prediksiGizi && $p->prediksiGizi->prediksi_status === 'stunting';
            })->count(),
            'berisiko_stunting' => $allPengukuran->filter(function($p) {
                return $p->prediksiGizi && $p->prediksiGizi->prediksi_status === 'berisiko_stunting';
            })->count(),
            'normal' => $allPengukuran->filter(function($p) {
                return $p->prediksiGizi && $p->prediksiGizi->prediksi_status === 'normal';
            })->count(),
            'gizi_lebih' => $allPengukuran->filter(function($p) {
                return $p->prediksiGizi && $p->prediksiGizi->prediksi_status === 'gizi_lebih';
            })->count(),
        ];
        
        // Get paginated data
        $pengukuran = $paginationQuery->paginate(15);
        
        return view('pengukuran.index', compact('pengukuran', 'stats'));
    }

    public function create(Request $request)
    {
        $balitaId = $request->get('balita_id');
        $balita = $balitaId ? Balita::findOrFail($balitaId) : null;
        
        $user = auth()->user();
        $balitaList = $user->isAdmin() ? 
            Balita::all() : 
            Balita::where('posyandu', $user->posyandu_name)->get();
        
        $pendapatanGroups = [
            'lt_600k' => 'Kurang dari Rp 600.000',
            '600k_1m' => 'Rp 600.000 - Rp 1.000.000',
            '1m_2m'   => 'Rp 1.000.001 - Rp 2.000.000',
            '2m_5m'   => 'Rp 2.000.001 - Rp 5.000.000',
            'gt_5m'   => 'Di atas Rp 5.000.000',
        ];

        return view('pengukuran.create', compact('balita', 'balitaList', 'pendapatanGroups'));
    }

    public function store(PengukuranRequest $request)
    {
        try {
            $data = $request->validated();
            
            $incomeMap = [
                'lt_600k' => 599000,
                '600k_1m' => 600000,
                '1m_2m'   => 1000000,
                '2m_5m'   => 2000000,
                'gt_5m'   => 5000000,
            ];

            $data['pendapatan_keluarga'] = $incomeMap[$data['pendapatan_keluarga']];
            $data['user_id'] = auth()->id();
            
            $pengukuran = Pengukuran::create($data);
            
            $prediction = $this->fuzzyAhpService->predictNutritionalStatus($pengukuran);
            
            if ($prediction) {
                return redirect()->route('pengukuran.show', $pengukuran)
                    ->with('success', 'Data pengukuran berhasil disimpan dan prediksi telah dihasilkan');
            } else {
                return redirect()->route('pengukuran.show', $pengukuran)
                    ->with('warning', 'Data pengukuran berhasil disimpan tetapi prediksi gagal dihasilkan');
            }
        } catch (\Exception $e) {
            \Log::error('Error saving pengukuran: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(Pengukuran $pengukuran)
    {
        $pengukuran->load(['balita', 'prediksiGizi', 'user']);
        return view('pengukuran.show', compact('pengukuran'));
    }

    public function edit(Pengukuran $pengukuran)
    {
        $user = auth()->user();
        $balitaList = $user->isAdmin() ? 
            Balita::all() : 
            Balita::where('posyandu', $user->posyandu_name)->get();

        $pendapatanGroups = [
            'lt_600k' => 'Kurang dari Rp 600.000',
            '600k_1m' => 'Rp 600.000 - Rp 1.000.000',
            '1m_2m'   => 'Rp 1.000.001 - Rp 2.000.000',
            '2m_5m'   => 'Rp 2.000.001 - Rp 5.000.000',
            'gt_5m'   => 'Di atas Rp 5.000.000',
        ];

        // Convert pendapatan nilai ke group key
        $currentPendapatanGroup = $this->getPendapatanGroup($pengukuran->pendapatan_keluarga);

        return view('pengukuran.edit', compact('pengukuran', 'balitaList', 'pendapatanGroups', 'currentPendapatanGroup'));
    }

    public function update(PengukuranRequest $request, Pengukuran $pengukuran)
    {
        try {
            $data = $request->validated();
            
            $incomeMap = [
                'lt_600k' => 599000,
                '600k_1m' => 600000,
                '1m_2m'   => 1000000,
                '2m_5m'   => 2000000,
                'gt_5m'   => 5000000,
            ];
            $data['pendapatan_keluarga'] = $incomeMap[$data['pendapatan_keluarga']];
            
            $pengukuran->update($data);
            
            if ($pengukuran->prediksiGizi) {
                $pengukuran->prediksiGizi->delete();
            }
            
            $prediction = $this->fuzzyAhpService->predictNutritionalStatus($pengukuran);
            
            if ($prediction) {
                return redirect()->route('pengukuran.show', $pengukuran)
                    ->with('success', 'Data pengukuran berhasil diperbarui dan prediksi telah dihasilkan ulang');
            } else {
                return redirect()->route('pengukuran.show', $pengukuran)
                    ->with('warning', 'Data pengukuran berhasil diperbarui tetapi prediksi gagal dihasilkan');
            }
        } catch (\Exception $e) {
            \Log::error('Error updating pengukuran: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Pengukuran $pengukuran)
    {
        try {
            $pengukuran->delete();
            return redirect()->route('pengukuran.index')
                ->with('success', 'Data pengukuran berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting pengukuran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    private function getPendapatanGroup($pendapatan)
    {
        if ($pendapatan < 600000) {
            return 'lt_600k';
        } elseif ($pendapatan >= 600000 && $pendapatan < 1000000) {
            return '600k_1m';
        } elseif ($pendapatan >= 1000000 && $pendapatan < 2000000) {
            return '1m_2m';
        } elseif ($pendapatan >= 2000000 && $pendapatan < 5000000) {
            return '2m_5m';
        } else {
            return 'gt_5m';
        }
    }
}