<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\User;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use App\Http\Requests\BalitaRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class BalitaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
    
        // 1. Buat Query Dasar
        $baseQuery = Balita::with(['latestPengukuran', 'masterPosyandu', 'masterDesa']);
    
        // 2. Terapkan Filter berdasarkan Role
        if (!$user->isAdmin()) {
            $baseQuery->where('posyandu', $user->posyandu_name);
        }
    
        // 3. Terapkan semua filter dari request ke Query Dasar
        // Filter berdasarkan pencarian (search)
        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('nama_balita', 'like', '%' . $request->search . '%')
                    ->orWhere('nik_balita', 'like', '%' . $request->search . '%')
                    ->orWhere('nama_orang_tua', 'like', '%' . $request->search . '%')
                    ->orWhere('alamat_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('desa_kelurahan', 'like', '%' . $request->search . '%');
            });
        }
    
        // Filter lainnya
        if ($request->filled('posyandu')) {
            $baseQuery->where('posyandu', $request->posyandu);
        }
        if ($request->filled('kecamatan')) {
            $baseQuery->where('kecamatan', $request->kecamatan);
        }
        if ($request->filled('gender')) {
            $baseQuery->where('jenis_kelamin', $request->gender);
        }
        if ($request->filled('area')) {
            $baseQuery->where('area', $request->area);
        }
        if ($request->filled('desa')) {
            $baseQuery->where('desa', $request->desa);
        }
        if ($request->filled('alamat_status')) {
            if ($request->alamat_status == 'lengkap') {
                $baseQuery->where(function($q) {
                    $q->whereHas('masterDesa')
                      ->orWhere(function($subq) {
                          $subq->whereNotNull('rt')
                               ->whereNotNull('rw')
                               ->whereNotNull('desa_kelurahan')
                               ->whereNotNull('kecamatan');
                      });
                });
            } elseif ($request->alamat_status == 'tidak_lengkap') {
                $baseQuery->where(function($q) {
                    $q->whereNull('rt')
                      ->orWhereNull('rw')
                      ->orWhereNull('desa_kelurahan')
                      ->orWhereNull('kecamatan')
                      ->orWhereDoesntHave('masterDesa');
                });
            }
        }
    
        // 3.1. Terapkan Sort berdasarkan nama balita
        if ($request->filled('sort_name')) {
            if ($request->sort_name == 'asc') {
                $baseQuery->orderBy('nama_balita', 'asc');
            } elseif ($request->sort_name == 'desc') {
                $baseQuery->orderBy('nama_balita', 'desc');
            }
        } else {
            // Default sorting - terbaru dulu
            $baseQuery->latest();
        }
    
        // 4. Hitung Statistik menggunakan clone() dari Query Dasar
        // clone() penting agar setiap perhitungan tidak mempengaruhi perhitungan lainnya
        $stats = [
            'total' => $baseQuery->clone()->count(),
            'laki_laki' => $baseQuery->clone()->where('jenis_kelamin', 'L')->count(),
            'perempuan' => $baseQuery->clone()->where('jenis_kelamin', 'P')->count(),
            'alamat_lengkap' => $baseQuery->clone()->where(function($q) {
                $q->whereHas('masterDesa')
                  ->orWhere(function($subq) {
                      $subq->whereNotNull('rt')
                           ->whereNotNull('rw')
                           ->whereNotNull('desa_kelurahan')
                           ->whereNotNull('kecamatan');
                  });
            })->count()
        ];
        
        // Tambahkan statistik alamat tidak lengkap
        $stats['alamat_tidak_lengkap'] = $stats['total'] - $stats['alamat_lengkap'];
    
        // 5. Ambil data untuk ditampilkan dengan paginasi
        $balita = $baseQuery->paginate(15);
    
        // Data untuk filter dropdown
        $areas = ['timur', 'barat', 'utara', 'selatan'];
        $posyandus = collect();
        $desas = collect();
        $kecamatans = collect();
    
        if ($user->isAdmin()) {
            // Untuk admin, ambil semua data unik dari tabel
            $posyandus = Balita::whereNotNull('posyandu')->distinct()->orderBy('posyandu')->pluck('posyandu');
            $kecamatans = Balita::whereNotNull('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');
            $desas = Balita::whereNotNull('desa')->distinct()->orderBy('desa')->pluck('desa');
        } else {
            // Untuk non-admin, hanya dari posyandu mereka
            $kecamatans = Balita::where('posyandu', $user->posyandu_name)
                                ->whereNotNull('kecamatan')
                                ->distinct()
                                ->orderBy('kecamatan')
                                ->pluck('kecamatan');
            // Anda mungkin juga ingin membatasi desa berdasarkan posyandu pengguna
            $desas = Balita::where('posyandu', $user->posyandu_name)
                           ->whereNotNull('desa')
                           ->distinct()
                           ->orderBy('desa')
                           ->pluck('desa');
        }
    
        return view('balita.index', compact('balita', 'stats', 'areas', 'posyandus', 'desas', 'kecamatans'));
    }

    public function create()
    {
        $user = auth()->user();

        // Data untuk dropdown
        $areas = ['timur', 'barat', 'utara', 'selatan'];
        $posyandus = collect();
        $desas = collect();

        if ($user->isAdmin()) {
            // Admin bisa pilih semua area
            $posyandus = MasterPosyandu::active()->get();
            $desas = MasterDesa::active()->get();
        } else {
            // Non-admin hanya bisa pilih area mereka
            if ($user->area) {
                $areas = [$user->area];
                $posyandus = MasterPosyandu::active()->where('area', $user->area)->get();
                $desas = MasterDesa::active()->where('area', $user->area)->get();
            }
        }

        return view('balita.create', compact('areas', 'posyandus', 'desas'));
    }

    public function store(BalitaRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        // Jika bukan admin, set data dari user
        if (!auth()->user()->isAdmin()) {
            $data['area'] = auth()->user()->area;
            $data['posyandu'] = auth()->user()->posyandu_name;
            $data['desa'] = auth()->user()->village;

            // Cari master posyandu dan desa berdasarkan nama
            $masterPosyandu = MasterPosyandu::where('nama_posyandu', $data['posyandu'])->first();
            $masterDesa = MasterDesa::where('nama_desa', $data['desa'])->first();

            if ($masterPosyandu) {
                $data['master_posyandu_id'] = $masterPosyandu->id;
            }
            if ($masterDesa) {
                $data['master_desa_id'] = $masterDesa->id;
            }
        } else {
            // Untuk admin, set master data berdasarkan pilihan
            if ($request->master_posyandu_id) {
                $masterPosyandu = MasterPosyandu::find($request->master_posyandu_id);
                if ($masterPosyandu) {
                    $data['posyandu'] = $masterPosyandu->nama_posyandu;
                    $data['area'] = $masterPosyandu->area;
                }
            }

            if ($request->master_desa_id) {
                $masterDesa = MasterDesa::find($request->master_desa_id);
                if ($masterDesa) {
                    $data['desa'] = $masterDesa->nama_desa;
                }
            }
        }

        // Generate alamat_lengkap dari komponen alamat detail untuk backward compatibility
        $addressParts = [];
        if ($data['rt']) $addressParts[] = "RT " . str_pad($data['rt'], 3, '0', STR_PAD_LEFT);
        if ($data['rw']) $addressParts[] = "RW " . str_pad($data['rw'], 3, '0', STR_PAD_LEFT);
        if ($data['dusun']) $addressParts[] = "Dusun " . $data['dusun'];
        if ($data['desa_kelurahan']) $addressParts[] = $data['desa_kelurahan'];
        if ($data['kecamatan']) $addressParts[] = "Kec. " . $data['kecamatan'];
        if ($data['kabupaten']) $addressParts[] = $data['kabupaten'];
        
        $data['alamat_lengkap'] = implode(', ', array_filter($addressParts));

        Balita::create($data);

        return redirect()->route('balita.index')
            ->with('success', 'Data balita berhasil ditambahkan');
    }

    public function show(Balita $balita)
    {
        $balita->load(['pengukuran.prediksiGizi', 'pengukuran.user', 'masterPosyandu', 'masterDesa']);

        return view('balita.show', compact('balita'));
    }

    public function edit(Balita $balita)
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

        // If alamat detail belum ada, coba parse dari alamat lama
        if (!$balita->rt && !$balita->rw && $balita->alamat_lengkap) {
            $this->parseOldAddressToNewFormat($balita);
        }

        return view('balita.edit', compact('balita', 'areas', 'posyandus', 'desas'));
    }

    public function update(BalitaRequest $request, Balita $balita)
    {
        $data = $request->validated();

        // Jika bukan admin, set data dari user
        if (!auth()->user()->isAdmin()) {
            $data['area'] = auth()->user()->area;
            $data['posyandu'] = auth()->user()->posyandu_name;
            $data['desa'] = auth()->user()->village;

            // Update master data references
            $masterPosyandu = MasterPosyandu::where('nama_posyandu', $data['posyandu'])->first();
            $masterDesa = MasterDesa::where('nama_desa', $data['desa'])->first();

            if ($masterPosyandu) {
                $data['master_posyandu_id'] = $masterPosyandu->id;
            }
            if ($masterDesa) {
                $data['master_desa_id'] = $masterDesa->id;
            }
        } else {
            // Untuk admin, set master data berdasarkan pilihan
            if ($request->master_posyandu_id) {
                $masterPosyandu = MasterPosyandu::find($request->master_posyandu_id);
                if ($masterPosyandu) {
                    $data['posyandu'] = $masterPosyandu->nama_posyandu;
                    $data['area'] = $masterPosyandu->area;
                }
            }

            if ($request->master_desa_id) {
                $masterDesa = MasterDesa::find($request->master_desa_id);
                if ($masterDesa) {
                    $data['desa'] = $masterDesa->nama_desa;
                }
            }
        }

        // Update alamat_lengkap dari komponen alamat detail
        $addressParts = [];
        if ($data['rt']) $addressParts[] = "RT " . str_pad($data['rt'], 3, '0', STR_PAD_LEFT);
        if ($data['rw']) $addressParts[] = "RW " . str_pad($data['rw'], 3, '0', STR_PAD_LEFT);
        if ($data['dusun']) $addressParts[] = "Dusun " . $data['dusun'];
        if ($data['desa_kelurahan']) $addressParts[] = $data['desa_kelurahan'];
        if ($data['kecamatan']) $addressParts[] = "Kec. " . $data['kecamatan'];
        if ($data['kabupaten']) $addressParts[] = $data['kabupaten'];
        
        $data['alamat_lengkap'] = implode(', ', array_filter($addressParts));

        $balita->update($data);

        return redirect()->route('balita.index')
            ->with('success', 'Data balita berhasil diperbarui');
    }

    public function destroy(Balita $balita)
    {
        // Gunakan transaksi database untuk memastikan semua proses berjalan atau tidak sama sekali
        DB::beginTransaction();
        try {
            // Hapus dulu semua data pengukuran terkait balita ini
            // Ini sesuai dengan logika di event `deleting` pada model Anda
            foreach ($balita->pengukuran as $pengukuran) {
                if ($pengukuran->prediksiGizi) {
                    $pengukuran->prediksiGizi->delete();
                }
                $pengukuran->delete();
            }
    
            // Setelah data terkait aman untuk dihapus, baru hapus data balita utama
            $balita->forceDelete(); // Menggunakan forceDelete untuk memastikan hapus permanen
    
            // Jika semua berhasil, konfirmasi transaksi
            DB::commit();
    
            return redirect()->route('balita.index')
                ->with('success', 'Data balita berhasil dihapus secara permanen.');
    
        } catch (Exception $e) {
            // Jika ada error di tengah jalan, batalkan semua perubahan
            DB::rollBack();
    
            // Tampilkan pesan error agar kita tahu apa yang salah
            return redirect()->route('balita.index')
                ->with('error', 'Gagal menghapus data balita. Error: ' . $e->getMessage());
        }
    }

    // API untuk mendapatkan posyandu berdasarkan area (dari master data)
    public function getPosyanduByArea(Request $request)
    {
        $area = $request->get('area');

        $posyandus = MasterPosyandu::active()
            ->where('area', $area)
            ->get(['id', 'nama_posyandu']);

        return response()->json($posyandus);
    }

    // API untuk mendapatkan desa berdasarkan area
    public function getDesaByArea(Request $request)
    {
        $area = $request->get('area');

        $desas = MasterDesa::active()
            ->where('area', $area)
            ->get(['id', 'nama_desa', 'master_posyandu_id']);

        return response()->json($desas);
    }

    // API untuk mendapatkan desa berdasarkan posyandu
    public function getDesaByPosyandu(Request $request)
    {
        $posyanduId = $request->get('posyandu_id');

        $desas = MasterDesa::active()
            ->where('master_posyandu_id', $posyanduId)
            ->get(['id', 'nama_desa']);

        return response()->json($desas);
    }

    // API untuk mendapatkan daftar kecamatan
    public function getKecamatans(Request $request)
    {
        $query = Balita::whereNotNull('kecamatan')
                       ->distinct()
                       ->orderBy('kecamatan');
        
        // Filter by kabupaten if provided
        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }
        
        $kecamatans = $query->pluck('kecamatan');

        return response()->json($kecamatans);
    }

    // API untuk mendapatkan daftar kabupaten
    public function getKabupatens(Request $request)
    {
        $kabupatens = Balita::whereNotNull('kabupaten')
                           ->distinct()
                           ->orderBy('kabupaten')
                           ->pluck('kabupaten');

        return response()->json($kabupatens);
    }

    // API untuk mendapatkan semua posyandu aktif
    public function getAllPosyandu()
    {
        $posyandus = MasterPosyandu::active()
            ->with('masterDesas')
            ->get(['id', 'nama_posyandu', 'area']);

        return response()->json($posyandus);
    }

    // API untuk mendapatkan semua desa aktif
    public function getAllDesa()
    {
        $desas = MasterDesa::active()
            ->with('masterPosyandu')
            ->get(['id', 'nama_desa', 'area', 'master_posyandu_id']);

        return response()->json($desas);
    }

    // Export data balita untuk machine learning
    public function exportForML(Request $request)
    {
        $query = Balita::with([
            'masterPosyandu',
            'masterDesa',
            'pengukuran.prediksiGizi'
        ]);

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        if ($request->filled('kabupaten')) {
            $query->where('kabupaten', $request->kabupaten);
        }

        if ($request->filled('start_date')) {
            $query->whereHas('pengukuran', function ($q) use ($request) {
                $q->where('tanggal_pengukuran', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('pengukuran', function ($q) use ($request) {
                $q->where('tanggal_pengukuran', '<=', $request->end_date);
            });
        }

        $balitas = $query->get();
        $data = [];

        foreach ($balitas as $balita) {
            foreach ($balita->pengukuran as $pengukuran) {
                if ($pengukuran->prediksiGizi) {
                    $data[] = [
                        'area' => $balita->area,
                        'posyandu' => $balita->masterPosyandu->nama_posyandu ?? $balita->posyandu,
                        'desa' => $balita->masterDesa->nama_desa ?? $balita->desa,
                        'desa_kelurahan' => $balita->desa_kelurahan,
                        'kecamatan' => $balita->kecamatan,
                        'kabupaten' => $balita->kabupaten,
                        'rt_rw' => ($balita->rt && $balita->rw) ? "RT {$balita->rt}/RW {$balita->rw}" : null,
                        'balita_id' => $balita->id,
                        'umur_bulan' => $pengukuran->umur_bulan,
                        'jenis_kelamin' => $balita->jenis_kelamin,
                        'berat_badan' => $pengukuran->berat_badan,
                        'tinggi_badan' => $pengukuran->tinggi_badan,
                        'prediksi_status' => $pengukuran->prediksiGizi->prediksi_status,
                        'tanggal_pengukuran' => $pengukuran->tanggal_pengukuran->format('Y-m-d')
                    ];
                }
            }
        }

        $filename = 'balita_data_ml_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function qrCode(Balita $balita)
    {
        return view('balita.qr-code', compact('balita'));
    }

    public function regenerateQr(Balita $balita)
    {
        if (!$balita->nik_balita) {
            return back()->with('error', 'NIK balita belum ada, tidak bisa generate QR.');
        }

        // nama file QR disimpan sesuai NIK
        $qrCodeName = $balita->nik_balita . '.png';

        // generate QR ke storage/app/public/qr-codes/
        \QrCode::format('png')
            ->size(200)
            ->generate($balita->nik_balita, storage_path('app/public/qr-codes/' . $qrCodeName));

        // simpan nama file di DB
        $balita->qr_code = $balita->nik_balita;
        $balita->save();

        return back()->with('success', 'QR Code berhasil digenerate.');
    }

    public function downloadQr(Balita $balita)
    {
        if (!$balita->qr_code) {
            return back()->with('error', 'QR Code belum digenerate');
        }

        $path = "qr-codes/{$balita->qr_code}.png";
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File QR Code belum tersedia');
        }

        return response()->download(Storage::disk('public')->path($path), "QR_{$balita->nama_balita}.png");
    }

    /**
     * Helper function untuk parse alamat lama ke format baru
     */
    private function parseOldAddressToNewFormat(Balita $balita)
    {
        if (!$balita->alamat_lengkap) return;

        $address = $balita->alamat_lengkap;
        $updates = [];

        // Parse RT
        if (preg_match('/RT\s*(\d{1,3})/i', $address, $matches)) {
            $updates['rt'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
        }

        // Parse RW 
        if (preg_match('/RW\s*(\d{1,3})/i', $address, $matches)) {
            $updates['rw'] = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
        }

        // Parse Dusun
        if (preg_match('/Dusun\s+([^,]+)/i', $address, $matches)) {
            $updates['dusun'] = trim($matches[1]);
        }

        // Parse Kecamatan
        if (preg_match('/Kec\.?\s+([^,]+)/i', $address, $matches)) {
            $updates['kecamatan'] = trim($matches[1]);
        }

        // Jika ada update, simpan ke database
        if (!empty($updates)) {
            $balita->update($updates);
        }
    }

    /**
     * Bulk update alamat dari format lama ke format baru
     */
    public function migrateAddresses()
    {
        // Hanya admin yang bisa jalankan ini
        if (!auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Tidak memiliki akses');
        }

        $balitas = Balita::whereNotNull('alamat_lengkap')
                         ->where(function($query) {
                             $query->whereNull('rt')
                                   ->orWhereNull('rw')
                                   ->orWhereNull('kecamatan');
                         })
                         ->get();

        $migrated = 0;
        foreach ($balitas as $balita) {
            $this->parseOldAddressToNewFormat($balita);
            $migrated++;
        }

        return redirect()->back()->with('success', "Berhasil memigrate {$migrated} data alamat");
    }
}