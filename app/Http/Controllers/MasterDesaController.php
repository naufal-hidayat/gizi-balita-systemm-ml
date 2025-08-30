<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterDesa::query()
            ->with('masterPosyandu')
            ->withCount('balitas');

        if ($request->filled('area'))     $query->byArea($request->area);
        if ($request->filled('posyandu')) $query->byPosyandu($request->posyandu);
        if ($request->filled('search'))   $query->where('nama_desa', 'like', '%' . $request->search . '%');

        $desas     = $query->paginate(10)->withQueryString();
        $areas     = ['utara', 'selatan', 'barat', 'timur'];
        $posyandus = MasterPosyandu::active()->orderBy('nama_posyandu')->get();
        $areaStats = MasterDesa::select('area', \DB::raw('count(*) as total'))
            ->groupBy('area')->pluck('total', 'area');

        return view('balita.master-desa.index', compact('desas', 'areas', 'posyandus', 'areaStats'));
    }

    public function create()
    {
        $areas     = ['utara', 'selatan', 'barat', 'timur'];
        $posyandus = MasterPosyandu::active()->orderBy('nama_posyandu')->get();
        return view('balita.master-desa.create', compact('areas', 'posyandus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_desa'          => ['required', 'string', 'max:255'],
            'area'               => ['required', Rule::in(['utara', 'selatan', 'barat', 'timur'])],
            'master_posyandu_id' => ['required', 'exists:master_posyandu,id'],
            'jumlah_penduduk'    => ['nullable', 'integer', 'min:0'],
            'keterangan'         => ['nullable', 'string'],
        ]);

        MasterDesa::create($data + ['is_active' => true]);

        return redirect()->route('balita.master-desa.index')
            ->with('success', 'Desa berhasil ditambahkan.');
    }


    public function show(MasterDesa $masterDesa)
    {
        $masterDesa->load(['posyandu', 'balitas' => function ($query) {
            $query->with('pengukurans')->latest();
        }]);

        return view('balita.master-desa.show', compact('masterDesa'));
    }

    public function edit(MasterDesa $masterDesa)
    {
        $areas = ['utara', 'selatan', 'barat', 'timur'];
        $posyandus = MasterPosyandu::orderBy('nama_posyandu')->get();

        return view('balita.master-desa.edit', compact('masterDesa', 'areas', 'posyandus'));
    }

    public function update(Request $request, MasterDesa $master_desa)
    {
        $data = $request->validate([
            'area'               => ['required', Rule::in(['utara', 'selatan', 'barat', 'timur'])],
            'master_posyandu_id' => ['required', 'integer', Rule::exists('master_posyandu', 'id')], // <-- SINGULAR
            'nama_desa'          => ['required', 'string', 'max:255'],
            'jumlah_penduduk'    => ['nullable', 'integer', 'min:0'],
            'keterangan'         => ['nullable', 'string'],
            'is_active'          => ['sometimes', 'boolean'],
        ]);

        $master_desa->update($data);

        return redirect()->route('balita.master-desa.index')
            ->with('success', 'Desa berhasil diperbarui.');
    }

    public function destroy(MasterDesa $masterDesa)
    {
        // Check if desa has balitas
        if ($masterDesa->balitas()->count() > 0) {
            return redirect()->route('master-desa.index')
                ->with('error', 'Tidak dapat menghapus desa yang masih memiliki data balita');
        }

        $masterDesa->delete();

        return redirect()->route('master-desa.index')
            ->with('success', 'Desa berhasil dihapus');
    }
}
