<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaKeluarga;
use App\Models\Keluarga;
use App\Http\Requests\AnggotaKeluargaRequest;
use Illuminate\Support\Facades\DB;

class AnggotaKeluargaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AnggotaKeluarga::with(['keluarga.masterPosyandu', 'keluarga.masterDesa']);

        // Filter berdasarkan role user
        if (!$user->isAdmin()) {
            $query->whereHas('keluarga', function($q) use ($user) {
                $q->where('area', $user->area);
            });
        }

        // Filter berdasarkan keluarga
        if ($request->filled('keluarga_id')) {
            $query->where('keluarga_id', $request->keluarga_id);
        }

        // Filter berdasarkan hubungan keluarga
        if ($request->filled('hubungan')) {
            $query->where('hubungan_keluarga', $request->hubungan);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter balita
        if ($request->filled('is_balita')) {
            $query->where('is_balita', $request->is_balita);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%')
                  ->orWhereHas('keluarga', function($subQ) use ($request) {
                      $subQ->where('no_kk', 'like', '%' . $request->search . '%')
                           ->orWhere('nama_kepala_keluarga', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Hanya yang aktif
        $query->where('is_active', true);
        
        $anggotaKeluarga = $query->latest()->paginate(15);

        // Data untuk filter
        $keluargaList = collect();
        $hubunganOptions = [
            'kepala_keluarga' => 'Kepala Keluarga',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orangtua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'lainnya' => 'Lainnya'
        ];

        if ($user->isAdmin()) {
            $keluargaList = Keluarga::active()->select('id', 'no_kk', 'nama_kepala_keluarga')->get();
        }
        
        return view('anggota-keluarga.index', compact('anggotaKeluarga', 'keluargaList', 'hubunganOptions'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Data untuk dropdown
        $keluargaList = collect();
        $hubunganOptions = [
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orangtua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'lainnya' => 'Lainnya'
        ];

        $pendidikanOptions = [
            'tidak_sekolah' => 'Tidak Sekolah',
            'tidak_tamat_sd' => 'Tidak Tamat SD',
            'sd' => 'SD/Sederajat',
            'smp' => 'SMP/Sederajat',
            'sma' => 'SMA/Sederajat',
            'diploma' => 'Diploma',
            'sarjana' => 'Sarjana',
            'magister' => 'Magister',
            'doktor' => 'Doktor'
        ];

        if ($user->isAdmin()) {
            $keluargaList = Keluarga::active()
                                  ->select('id', 'no_kk', 'nama_kepala_keluarga', 'area')
                                  ->get();
        } else {
            $keluargaList = Keluarga::active()
                                  ->where('area', $user->area)
                                  ->select('id', 'no_kk', 'nama_kepala_keluarga')
                                  ->get();
        }

        return view('anggota-keluarga.create', compact('keluargaList', 'hubunganOptions', 'pendidikanOptions'));
    }

    public function store(AnggotaKeluargaRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Auto-set is_balita berdasarkan umur
            $tanggalLahir = \Carbon\Carbon::parse($data['tanggal_lahir']);
            $umurBulan = $tanggalLahir->diffInMonths(now());
            $data['is_balita'] = ($umurBulan <= 60 && $data['hubungan_keluarga'] === 'anak');
            $data['is_active'] = true;

            $anggota = AnggotaKeluarga::create($data);

            DB::commit();
            
            return redirect()->route('anggota-keluarga.show', $anggota)
                ->with('success', 'Anggota keluarga berhasil ditambahkan');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(AnggotaKeluarga $anggota)
    {
        $anggota->load(['keluarga.anggotaKeluarga', 'balita.pengukuran.prediksiGizi']);
        
        return view('anggota-keluarga.show', compact('anggota'));
    }

    public function edit(AnggotaKeluarga $anggota)
    {
        $user = auth()->user();
        
        // Check authorization
        if (!$user->isAdmin() && $anggota->keluarga->area !== $user->area) {
            abort(403, 'Unauthorized');
        }

        $hubunganOptions = [
            'kepala_keluarga' => 'Kepala Keluarga',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orangtua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'lainnya' => 'Lainnya'
        ];

        $pendidikanOptions = [
            'tidak_sekolah' => 'Tidak Sekolah',
            'tidak_tamat_sd' => 'Tidak Tamat SD',
            'sd' => 'SD/Sederajat',
            'smp' => 'SMP/Sederajat',
            'sma' => 'SMA/Sederajat',
            'diploma' => 'Diploma',
            'sarjana' => 'Sarjana',
            'magister' => 'Magister',
            'doktor' => 'Doktor'
        ];

        return view('anggota-keluarga.edit', compact('anggota', 'hubunganOptions', 'pendidikanOptions'));
    }

    public function update(AnggotaKeluargaRequest $request, AnggotaKeluarga $anggota)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Check authorization
            if (!$user->isAdmin() && $anggota->keluarga->area !== $user->area) {
                abort(403, 'Unauthorized');
            }

            $data = $request->validated();
            
            // Don't allow changing keluarga_id in update
            unset($data['keluarga_id']);
            
            // Auto-update is_balita berdasarkan umur
            if (isset($data['tanggal_lahir'])) {
                $tanggalLahir = \Carbon\Carbon::parse($data['tanggal_lahir']);
                $umurBulan = $tanggalLahir->diffInMonths(now());
                $data['is_balita'] = ($umurBulan <= 60 && $data['hubungan_keluarga'] === 'anak');
            }

            $anggota->update($data);

            DB::commit();
            
            return redirect()->route('anggota-keluarga.show', $anggota)
                ->with('success', 'Data anggota keluarga berhasil diperbarui');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(AnggotaKeluarga $anggota)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            
            // Check authorization
            if (!$user->isAdmin() && $anggota->keluarga->area !== $user->area) {
                abort(403, 'Unauthorized');
            }

            // Don't allow deleting kepala keluarga
            if ($anggota->hubungan_keluarga === 'kepala_keluarga') {
                return back()->withErrors(['error' => 'Kepala keluarga tidak dapat dihapus']);
            }

            // Don't allow deleting if has balita data
            if ($anggota->balita) {
                return back()->withErrors(['error' => 'Anggota keluarga yang sudah terdaftar sebagai balita tidak dapat dihapus']);
            }

            // Soft delete
            $anggota->update(['is_active' => false]);

            DB::commit();
            
            return redirect()->route('anggota-keluarga.index')
                ->with('success', 'Anggota keluarga berhasil dihapus');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // API Methods
    public function getByKeluarga(Request $request)
    {
        $keluargaId = $request->get('keluarga_id');
        
        if (!$keluargaId) {
            return response()->json(['error' => 'Keluarga ID diperlukan'], 400);
        }

        $anggota = AnggotaKeluarga::where('keluarga_id', $keluargaId)
                                 ->where('is_active', true)
                                 ->get(['id', 'nik', 'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin', 'hubungan_keluarga', 'is_balita']);

        return response()->json($anggota);
    }

    public function getBalitaEligible(Request $request)
    {
        $keluargaId = $request->get('keluarga_id');
        
        if (!$keluargaId) {
            return response()->json(['error' => 'Keluarga ID diperlukan'], 400);
        }

        $anggota = AnggotaKeluarga::where('keluarga_id', $keluargaId)
                                 ->where('is_active', true)
                                 ->where('is_balita', true)
                                 ->where('hubungan_keluarga', 'anak')
                                 ->whereDoesntHave('balita')
                                 ->get(['id', 'nik', 'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin']);

        return response()->json($anggota->map(function($item) {
            return [
                'id' => $item->id,
                'nik' => $item->nik,
                'nama_lengkap' => $item->nama_lengkap,
                'tanggal_lahir' => $item->tanggal_lahir->format('Y-m-d'),
                'umur_bulan' => $item->umur_bulan,
                'jenis_kelamin' => $item->jenis_kelamin,
                'jenis_kelamin_label' => $item->jenis_kelamin_label,
                'eligible_for_balita' => true
            ];
        }));
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'anggota_ids' => 'required|array',
            'anggota_ids.*' => 'exists:anggota_keluarga,id',
            'status' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            $updated = AnggotaKeluarga::whereIn('id', $request->anggota_ids)
                                    ->update(['is_active' => $request->status]);

            DB::commit();

            $message = $request->status ? 'Anggota keluarga berhasil diaktifkan' : 'Anggota keluarga berhasil dinonaktifkan';
            
            return back()->with('success', $message . " ({$updated} data)");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $query = AnggotaKeluarga::with(['keluarga']);

        // Filter berdasarkan role user
        if (!$user->isAdmin()) {
            $query->whereHas('keluarga', function($q) use ($user) {
                $q->where('area', $user->area);
            });
        }

        // Apply filters
        if ($request->filled('keluarga_id')) {
            $query->where('keluarga_id', $request->keluarga_id);
        }

        if ($request->filled('hubungan')) {
            $query->where('hubungan_keluarga', $request->hubungan);
        }

        if ($request->filled('is_balita')) {
            $query->where('is_balita', $request->is_balita);
        }

        $anggotaKeluarga = $query->where('is_active', true)->get();

        $data = $anggotaKeluarga->map(function($anggota) {
            return [
                'nik' => $anggota->nik,
                'nama_lengkap' => $anggota->nama_lengkap,
                'tanggal_lahir' => $anggota->tanggal_lahir->format('Y-m-d'),
                'umur' => $anggota->umur,
                'jenis_kelamin' => $anggota->jenis_kelamin_label,
                'hubungan_keluarga' => $anggota->hubungan_keluarga_label,
                'pekerjaan' => $anggota->pekerjaan ?? '-',
                'pendidikan' => $anggota->pendidikan_label,
                'is_balita' => $anggota->is_balita ? 'Ya' : 'Tidak',
                'no_kk' => $anggota->keluarga->no_kk,
                'kepala_keluarga' => $anggota->keluarga->nama_kepala_keluarga,
                'alamat' => $anggota->keluarga->alamat_lengkap,
                'area' => $anggota->keluarga->area_label
            ];
        });

        $filename = 'anggota_keluarga_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // Utility Methods
    public function getStats()
    {
        $user = auth()->user();
        $query = AnggotaKeluarga::query();

        if (!$user->isAdmin()) {
            $query->whereHas('keluarga', function($q) use ($user) {
                $q->where('area', $user->area);
            });
        }

        $stats = [
            'total' => $query->where('is_active', true)->count(),
            'balita' => $query->where('is_active', true)->where('is_balita', true)->count(),
            'dewasa' => $query->where('is_active', true)->where('is_balita', false)->count(),
            'laki_laki' => $query->where('is_active', true)->where('jenis_kelamin', 'L')->count(),
            'perempuan' => $query->where('is_active', true)->where('jenis_kelamin', 'P')->count(),
            'kepala_keluarga' => $query->where('is_active', true)->where('hubungan_keluarga', 'kepala_keluarga')->count(),
        ];

        return response()->json($stats);
    }
}