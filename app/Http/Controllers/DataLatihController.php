<?php

namespace App\Http\Controllers;

use App\Models\DataLatih;
use Illuminate\Http\Request;

class DataLatihController extends Controller
{
    public function index()
    {
        $dataLatih = DataLatih::latest()->get();

        $jumlahNormal = DataLatih::where('status_gizi', 'Normal')->count();
        $jumlahStunting = DataLatih::where('status_gizi', 'Stunting')->count();

        return view('pengukuran.data_latih.index', compact('dataLatih', 'jumlahNormal', 'jumlahStunting'));
    }

    public function create()
    {
        return view('pengukuran.data_latih.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'umur' => 'required|numeric',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'status_gizi' => 'required|string'
        ]);

        DataLatih::create($request->all());
        return redirect()->route('data-latih.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(DataLatih $dataLatih)
    {
        return view('pengukuran.data_latih.edit', compact('dataLatih'));
    }

    public function update(Request $request, DataLatih $dataLatih)
    {
        $dataLatih->update($request->all());
        return redirect()->route('data-latih.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(DataLatih $dataLatih)
    {
        $dataLatih->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }
}
