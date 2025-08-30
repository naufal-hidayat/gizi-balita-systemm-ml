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
        $query = Pengukuran::with(['balita', 'prediksiGizi', 'user']);
        
        if (!$user->isAdmin()) {
            $query->whereHas('balita', function ($q) use ($user) {
                $q->where('posyandu', $user->posyandu_name);
            });
        }
        
        if ($request->filled('search')) {
            $query->whereHas('balita', function ($q) use ($request) {
                $q->where('nama_balita', 'like', '%' . $request->search . '%');
            });
        }
        
        $pengukuran = $query->latest('tanggal_pengukuran')->paginate(15);
        
        return view('pengukuran.index', compact('pengukuran'));
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

        return view('pengukuran.edit', compact('pengukuran', 'balitaList', 'pendapatanGroups'));
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
}