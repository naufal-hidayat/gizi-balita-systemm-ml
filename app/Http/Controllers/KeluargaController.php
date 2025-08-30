<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keluarga;
use App\Models\AnggotaKeluarga;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use App\Http\Requests\KeluargaRequest;
use Illuminate\Support\Facades\DB;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Keluarga::with(['anggotaKeluarga', 'masterPosyandu', 'masterDesa']);

        if (!$user->isAdmin()) {
            $query->where('area', $user->area);
        }

        // Filter berdasarkan area
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        // Filter berdasarkan posyandu
        if ($request->filled('posyandu')) {
            $query->where('master_posyandu_id', $request->posyandu);
        }

        // Filter berdasarkan desa
        if ($request->filled('desa')) {
            $query->where('master_desa_id', $request->desa);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('no_kk', 'like', '%' . $request->search . '%')
                  ->orWhere('nik_kepala_keluarga', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_kepala_keluarga', 'like', '%' . $request->search . '%');
            });
        }

        // Filter yang punya balita
        if ($request->filled('has_balita') && $request->has_balita == '1') {
            $query->has('balitaAktif');
        }
        
        $keluarga = $query->latest()->paginate(15);

        // Data untuk filter dropdown
        $areas = ['timur', 'barat', 'utara', 'selatan'];
        $posyandus = collect();
        $desas = collect();

        if ($user->isAdmin()) {
            $posyandusQuery = MasterPosyandu::active();
            $desasQuery = MasterDesa::active();

            if ($request->filled('area')) {
                $posyandusQuery->where('area', $request->area);
                $desasQuery->where('area', $request->area);
            }

            $posyandus = $posyandusQuery->get();
            $desas = $desasQuery->get();
        }
        
        return view('keluarga.index', compact('keluarga', 'areas', 'posyandus', 'desas'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Data untuk dropdown
        $areas = ['timur', 'barat', 'utara', 'selatan'];
        $posyandus = collect();
        $desas = collect();

        if ($user->isAdmin()) {
            $posyandus = MasterPosyandu::active()->get();
            $desas = MasterDesa::active()->get();
        } else {
            if ($user->area) {
                $areas = [$user->area];
                $posyandus = MasterPosyandu::active()->where('area', $user->area)->get();
                $desas = MasterDesa::active()->where('area', $user->area)->get();
            }
        }

        return view('keluarga.create', compact('areas', 'posyandus', 'desas'));
    }

    public function store(KeluargaRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();
            
            // Jika bukan admin, set data dari user
            if (!auth()->user()->isAdmin()) {
                $data['area'] = auth()->user()->area;
                
                if ($request->master_posyandu_id) {
                    $masterPosyandu = MasterPosyandu::find($request->master_posyandu_id);
                    if ($masterPosyandu) {
                        $data['area'] = $masterPosyandu->area;
                    }
                }
            }

            // Create keluarga
            $keluarga = Keluarga::create($data);

            // Create kepala keluarga sebagai anggota pertama
            AnggotaKeluarga::create([
                'keluarga_id' => $keluarga->id,
                'nik' => $data['nik_kepala_keluarga'],
                'nama_lengkap' => $data['nama_kepala_keluarga'],
                'tanggal_lahir' => $data['tanggal_lahir_kepala_keluarga'],
                'jenis_kelamin' => $data['jenis_kelamin_kepala_keluarga'],
                'hubungan_keluarga' => 'kepala_keluarga',
                'pekerjaan' => $data['pekerjaan_kepala_keluarga'] ?? null,
                'pendidikan_terakhir' => $data['pendidikan_kepala_keluarga'] ?? null,
                'is_active' => true
            ]);

            DB::commit();
            
            return redirect()->route('keluarga.show', $keluarga)
                ->with('success', 'Data keluarga berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(Keluarga $keluarga)
    {
        $keluarga->load([
            'anggotaKeluarga' => function($query) {
                $query->where('is_active', true)->with('balita');
            }, 
            'balita.latestPengukuran.prediksiGizi', 
            'masterPosyandu', 
            'masterDesa'
        ]);
        
        return view('keluarga.show', compact('keluarga'));
    }

    public function edit(Keluarga $keluarga)
    {
        $user = auth()->user();
        
        // Data untuk dropdown
        $areas = ['timur', 'barat', 'utara', 'selatan'];
        $posyandus = collect();
        $desas = collect();

        if ($user->isAdmin()) {
            $posyandus = MasterPosyandu::active()->get();
            $desas = MasterDesa::active()->get();
        } else {
            if ($user->area) {
                $areas = [$user->area];
                $posyandus = MasterPosyandu::active()->where('area', $user->area)->get();
                $desas = MasterDesa::active()->where('area', $user->area)->get();
            }
        }

        return view('keluarga.edit', compact('keluarga', 'areas', 'posyandus', 'desas'));
    }

    public function update(KeluargaRequest $request, Keluarga $keluarga)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Jika bukan admin, set data dari user
            if (!auth()->user()->isAdmin()) {
                $data['area'] = auth()->user()->area;
                
                if ($request->master_posyandu_id) {
                    $masterPosyandu = MasterPosyandu::find($request->master_posyandu_id);
                    if ($masterPosyandu) {
                        $data['area'] = $masterPosyandu->area;
                    }
                }
            }
            
            $keluarga->update($data);

            // Update data kepala keluarga
            $kepalaKeluarga = $keluarga->kepalaKeluarga();
            if ($kepalaKeluarga) {
                $kepalaKeluarga->update([
                    'nik' => $data['nik_kepala_keluarga'],
                    'nama_lengkap' => $data['nama_kepala_keluarga'],
                    'tanggal_lahir' => $data['tanggal_lahir_kepala_keluarga'] ?? $kepalaKeluarga->tanggal_lahir,
                    'jenis_kelamin' => $data['jenis_kelamin_kepala_keluarga'] ?? $kepalaKeluarga->jenis_kelamin,
                    'pekerjaan' => $data['pekerjaan_kepala_keluarga'] ?? $kepalaKeluarga->pekerjaan,
                    'pendidikan_terakhir' => $data['pendidikan_kepala_keluarga'] ?? $kepalaKeluarga->pendidikan_terakhir
                ]);
            }

            DB::commit();
            
            return redirect()->route('keluarga.show', $keluarga)
                ->with('success', 'Data keluarga berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Keluarga $keluarga)
    {
        try {
            DB::beginTransaction();

            // Soft delete - set is_active = false
            $keluarga->update(['is_active' => false]);
            $keluarga->anggotaKeluarga()->update(['is_active' => false]);

            DB::commit();
            
            return redirect()->route('keluarga.index')
                ->with('success', 'Data keluarga berhasil dinonaktifkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Manajemen Anggota Keluarga
    public function addAnggota(Request $request, Keluarga $keluarga)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:anggota_keluarga,nik',
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'hubungan_keluarga' => 'required|string',
            'pekerjaan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string'
        ]);

        try {
            $anggota = AnggotaKeluarga::create([
                'keluarga_id' => $keluarga->id,
                'nik' => $request->nik,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'hubungan_keluarga' => $request->hubungan_keluarga,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'is_active' => true
            ]);

            return back()->with('success', 'Anggota keluarga berhasil ditambahkan');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function updateAnggota(Request $request, Keluarga $keluarga, AnggotaKeluarga $anggota)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:anggota_keluarga,nik,' . $anggota->id,
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'hubungan_keluarga' => 'required|string',
            'pekerjaan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string'
        ]);

        try {
            $anggota->update($request->only([
                'nik', 'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin',
                'hubungan_keluarga', 'pekerjaan', 'pendidikan_terakhir'
            ]));

            return back()->with('success', 'Data anggota keluarga berhasil diperbarui');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function removeAnggota(Keluarga $keluarga, AnggotaKeluarga $anggota)
    {
        try {
            // Jangan hapus kepala keluarga
            if ($anggota->hubungan_keluarga === 'kepala_keluarga') {
                return back()->withErrors(['error' => 'Kepala keluarga tidak dapat dihapus']);
            }

            $anggota->update(['is_active' => false]);

            return back()->with('success', 'Anggota keluarga berhasil dinonaktifkan');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // API untuk mobile
    public function getKeluargaByKK(Request $request)
    {
        $noKK = $request->get('no_kk');
        
        if (!$noKK) {
            return response()->json(['error' => 'Nomor KK diperlukan'], 400);
        }

        $keluarga = Keluarga::with(['anggotaKeluarga' => function($query) {
            $query->where('is_active', true);
        }])->where('no_kk', $noKK)->first();

        if (!$keluarga) {
            return response()->json(['error' => 'Keluarga tidak ditemukan'], 404);
        }

        return response()->json([
            'keluarga' => $keluarga,
            'anggota' => $keluarga->anggotaKeluarga->map(function($anggota) {
                return [
                    'id' => $anggota->id,
                    'nik' => $anggota->nik,
                    'nama_lengkap' => $anggota->nama_lengkap,
                    'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
                    'umur_bulan' => $anggota->umur_bulan,
                    'jenis_kelamin' => $anggota->jenis_kelamin,
                    'hubungan_keluarga' => $anggota->hubungan_keluarga,
                    'is_balita' => $anggota->is_balita,
                    'eligible_for_balita' => $anggota->isEligibleForBalita()
                ];
            })
        ]);
    }

    public function getBalitaEligible(Request $request)
    {
        $noKK = $request->get('no_kk');
        
        $keluarga = Keluarga::with(['anggotaKeluarga' => function($query) {
            $query->where('is_active', true)
                  ->where('is_balita', true)
                  ->where('hubungan_keluarga', 'anak');
        }])->where('no_kk', $noKK)->first();

        if (!$keluarga) {
            return response()->json(['error' => 'Keluarga tidak ditemukan'], 404);
        }

        return response()->json([
            'balita_eligible' => $keluarga->anggotaKeluarga->map(function($anggota) {
                return [
                    'id' => $anggota->id,
                    'nik' => $anggota->nik,
                    'nama_lengkap' => $anggota->nama_lengkap,
                    'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
                    'umur_bulan' => $anggota->umur_bulan,
                    'jenis_kelamin' => $anggota->jenis_kelamin
                ];
            })
        ]);
    }
}