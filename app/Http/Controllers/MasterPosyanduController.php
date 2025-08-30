<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use Illuminate\Support\Facades\DB;

class MasterPosyanduController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterPosyandu::query()->withCount('desas');
        
        // Filter by area
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_posyandu', 'like', "%{$search}%")
                  ->orWhere('ketua_posyandu', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        
        $posyandus = $query->orderBy('nama_posyandu')
                          ->paginate(10)
                          ->withQueryString();
        
        // Get available areas
        $areas = MasterPosyandu::distinct()
                              ->pluck('area')
                              ->filter()
                              ->sort()
                              ->values();
        
        // Get area statistics
        $areaStats = MasterPosyandu::select('area', DB::raw('count(*) as total'))
                                  ->whereNotNull('area')
                                  ->groupBy('area')
                                  ->pluck('total', 'area');
        
        return view('balita.master-posyandu.index', compact('posyandus', 'areas', 'areaStats'));
    }

    public function create()
    {
        // Get available areas - you might want to define these in config or database
        $areas = ['utara', 'selatan', 'barat', 'timur']; // or get from database/config
        
        return view('balita.master-posyandu.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'area' => 'required|string',
            'alamat' => 'required|string',
            'ketua_posyandu' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:15',
        ]);

        MasterPosyandu::create($validated);

        return redirect()->route('balita.master-posyandu.index')
                        ->with('success', 'Posyandu berhasil ditambahkan');
    }

    public function show(MasterPosyandu $masterPosyandu)
    {
        $masterPosyandu->load(['desas' => function($query) {
            $query->withCount('balitas');
        }]);
        
        return view('balita.master-posyandu.show', compact('masterPosyandu'));
    }

    public function edit(MasterPosyandu $masterPosyandu)
    {
        $areas = ['utara', 'selatan', 'barat', 'timur'];
        
        return view('balita.master-posyandu.edit', compact('masterPosyandu', 'areas'));
    }

    public function update(Request $request, MasterPosyandu $masterPosyandu)
    {
        $validated = $request->validate([
            'nama_posyandu' => 'required|string|max:255',
            'area' => 'required|string',
            'alamat' => 'required|string',
            'ketua_posyandu' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:15',
        ]);

        $masterPosyandu->update($validated);

        return redirect()->route('balita.master-posyandu.index')
                        ->with('success', 'Posyandu berhasil diperbarui');
    }

    public function destroy(MasterPosyandu $masterPosyandu)
    {
        // Check if posyandu has desas
        if ($masterPosyandu->desas()->count() > 0) {
            return redirect()->route('balita.master-posyandu.index')
                           ->with('error', 'Tidak dapat menghapus posyandu yang masih memiliki desa terkait');
        }

        $masterPosyandu->delete();

        return redirect()->route('balita.master-posyandu.index')
                        ->with('success', 'Posyandu berhasil dihapus');
    }
}