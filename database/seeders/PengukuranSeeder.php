<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengukuran;
use App\Models\Balita;
use App\Services\FuzzyAhpService;
use Carbon\Carbon;

class PengukuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data berat dan tinggi badan sesuai urutan balita (dari Adiba sampai Ziandra)
        $pengukuranData = [
            ['berat_badan' => 13, 'tinggi_badan' => 85.5],      // Adiba el Putri
            ['berat_badan' => 16, 'tinggi_badan' => 107],       // AHSAN
            ['berat_badan' => 10.6, 'tinggi_badan' => 75],      // ALEEA SHANUM A
            ['berat_badan' => 8.3, 'tinggi_badan' => 69.6],     // ALESHA NAURA
            ['berat_badan' => 14.6, 'tinggi_badan' => 92],      // Alesya Auristela
            ['berat_badan' => 10.8, 'tinggi_badan' => 88.5],    // Alfareza
            ['berat_badan' => 8.2, 'tinggi_badan' => 65],       // ALVIAN P
            ['berat_badan' => 7.9, 'tinggi_badan' => 62.5],     // ALZAIDAN F
            ['berat_badan' => 10.9, 'tinggi_badan' => 79.5],    // AMEENA MAULIDA
            ['berat_badan' => 9.8, 'tinggi_badan' => 80],       // ANISA NADHIRA
            ['berat_badan' => 10.7, 'tinggi_badan' => 83],      // ARA SHAZA
            ['berat_badan' => 10.5, 'tinggi_badan' => 90.5],    // Arfan m.fawaz
            ['berat_badan' => 13.5, 'tinggi_badan' => 84.5],    // ARFAN ZAIDAN M
            ['berat_badan' => 11, 'tinggi_badan' => 80],        // ARHAN ARDIANSYAH
            ['berat_badan' => 8.2, 'tinggi_badan' => 68.5],     // ARKANZA
            ['berat_badan' => 11.2, 'tinggi_badan' => 87.5],    // ARRASYA
            ['berat_badan' => 12.4, 'tinggi_badan' => 89],      // ARRSYA
            ['berat_badan' => 12.6, 'tinggi_badan' => 88],      // Arshaka Syapiq
            ['berat_badan' => 8.8, 'tinggi_badan' => 76.8],     // ARSYAP
            ['berat_badan' => 15, 'tinggi_badan' => 97],        // AULIA
            ['berat_badan' => 11.2, 'tinggi_badan' => 77],      // AURIEL
            ['berat_badan' => 5.7, 'tinggi_badan' => 57.1],     // AVIDA
            ['berat_badan' => 19, 'tinggi_badan' => 106.5],     // AZKA NUR TAJUL ARIFIN
            ['berat_badan' => 6.6, 'tinggi_badan' => 66],       // AZKIARA
            ['berat_badan' => 12.4, 'tinggi_badan' => 84.8],    // BILAL DAIFULLOH
            ['berat_badan' => 14.1, 'tinggi_badan' => 96.5],    // BILQIS
            ['berat_badan' => 16.2, 'tinggi_badan' => 101.5],   // DAFA OKTAPIAN
            ['berat_badan' => 7.2, 'tinggi_badan' => 58],       // DAFFA
            ['berat_badan' => 15.3, 'tinggi_badan' => 103.5],   // GAISKA
            ['berat_badan' => 12.8, 'tinggi_badan' => 84],      // GEMA FAZIANDRI
            ['berat_badan' => 15.5, 'tinggi_badan' => 95.5],    // HANINDIRA
            ['berat_badan' => 11.2, 'tinggi_badan' => 81.3],    // HILYA
            ['berat_badan' => 4.5, 'tinggi_badan' => 51],       // HISYAM
            ['berat_badan' => 10.3, 'tinggi_badan' => 80.5],    // HUMAIRA A
            ['berat_badan' => 11.8, 'tinggi_badan' => 94.5],    // HYKAYATUL
            ['berat_badan' => 11.2, 'tinggi_badan' => 82.8],    // KANAKA
            ['berat_badan' => 14, 'tinggi_badan' => 97],        // KHANIA
            ['berat_badan' => 12.5, 'tinggi_badan' => 87.5],    // LAURA GANTARI
            ['berat_badan' => 7.5, 'tinggi_badan' => 62],       // M. ARSHAKA S
            ['berat_badan' => 9.8, 'tinggi_badan' => 72],       // M. ARSYA A
            ['berat_badan' => 17.2, 'tinggi_badan' => 103.5],   // M. AYDAN Z
            ['berat_badan' => 9.8, 'tinggi_badan' => 80.8],     // M. QAIS RAMADAN
            ['berat_badan' => 11.7, 'tinggi_badan' => 87.8],    // MARIAM SITI HAWA
            ['berat_badan' => 14.8, 'tinggi_badan' => 103.5],   // MIKAYLA
            ['berat_badan' => 8.8, 'tinggi_badan' => 77.5],     // MIQDAD
            ['berat_badan' => 11.8, 'tinggi_badan' => 82],      // Naira Syabila
            ['berat_badan' => 9.8, 'tinggi_badan' => 76],       // NATHAN
            ['berat_badan' => 8.3, 'tinggi_badan' => 69.6],     // NAZIFA
            ['berat_badan' => 18.2, 'tinggi_badan' => 100],     // PELANGI
            ['berat_badan' => 15.7, 'tinggi_badan' => 102.5],   // QAIREEN AZZALEA
            ['berat_badan' => 9.2, 'tinggi_badan' => 73.5],     // QONITA
            ['berat_badan' => 13, 'tinggi_badan' => 90.5],      // Radja Al Muiz
            ['berat_badan' => 15, 'tinggi_badan' => 102],       // RAFKA A.
            ['berat_badan' => 12.5, 'tinggi_badan' => 84.5],    // RAISA
            ['berat_badan' => 8.5, 'tinggi_badan' => 69.5],     // RIYAZ M
            ['berat_badan' => 14.3, 'tinggi_badan' => 103.5],   // RUBY HAVVA
            ['berat_badan' => 10.5, 'tinggi_badan' => 88],      // SAKILA NUR
            ['berat_badan' => 12.2, 'tinggi_badan' => 90.5],    // Satria Qirom
            ['berat_badan' => 13.8, 'tinggi_badan' => 97.5],    // SELFI S.
            ['berat_badan' => 16.3, 'tinggi_badan' => 99.7],    // SHABIRA
            ['berat_badan' => 18.2, 'tinggi_badan' => 106.5],   // SHAQUEENA ZAYAAN
            ['berat_badan' => 12.2, 'tinggi_badan' => 90.5],    // SIDQI
            ['berat_badan' => 16.2, 'tinggi_badan' => 102],     // SITI AISYAH
            ['berat_badan' => 10.6, 'tinggi_badan' => 85.5],    // Siti Fauziah
            ['berat_badan' => 13.5, 'tinggi_badan' => 93.5],    // SYAUQHY
            ['berat_badan' => 15, 'tinggi_badan' => 102.5],     // ZAHIRA
            ['berat_badan' => 14.7, 'tinggi_badan' => 93],      // ZAIDAN XAVIER
            ['berat_badan' => 12.9, 'tinggi_badan' => 91.5],    // ZIANDRA
        ];

        // Ambil semua balita berdasarkan urutan nama (sesuai BalitaSeeder)
        $balitaList = Balita::orderBy('id')->get();
        
        if ($balitaList->count() !== count($pengukuranData)) {
            $this->command->error('Jumlah data balita (' . $balitaList->count() . ') tidak sesuai dengan data pengukuran (' . count($pengukuranData) . ')');
            return;
        }

        // Cek apakah user dengan ID 1 ada
        if (!\App\Models\User::find(1)) {
            $this->command->error('User dengan ID 1 tidak ditemukan. Pastikan user admin sudah dibuat.');
            return;
        }

        foreach ($balitaList as $index => $balita) {
            // Skip jika sudah ada pengukuran untuk balita ini
            if (Pengukuran::where('balita_id', $balita->id)->exists()) {
                $this->command->line('Pengukuran untuk balita ' . $balita->nama_balita . ' sudah ada, skip...');
                continue;
            }

            // Ambil data pengukuran sesuai index
            $pengukuran = $pengukuranData[$index];
            
            // Generate tanggal pengukuran yang realistis (1-3 bulan terakhir)
            $tanggalPengukuran = Carbon::now()->subDays(rand(30, 90));
            
            // Pastikan tanggal pengukuran tidak sebelum tanggal lahir balita
            $tanggalLahirBalita = Carbon::parse($balita->tanggal_lahir);
            if ($tanggalPengukuran->lt($tanggalLahirBalita)) {
                $tanggalPengukuran = $tanggalLahirBalita->copy()->addDays(rand(30, 60));
            }
            
            // Hitung umur dalam bulan berdasarkan tanggal lahir dan tanggal pengukuran
            $umurBulan = $tanggalLahirBalita->diffInMonths($tanggalPengukuran);
            
            // Pastikan umur dalam range yang valid (0-60 bulan)
            $umurBulan = max(0, min(60, $umurBulan));
            
            try {
                // Generate data tambahan sesuai ketentuan
                $dataSeeder = [
                    'balita_id' => $balita->id,
                    'tanggal_pengukuran' => $tanggalPengukuran->format('Y-m-d'),
                    'umur_bulan' => $umurBulan,
                    'berat_badan' => $pengukuran['berat_badan'],
                    'tinggi_badan' => $pengukuran['tinggi_badan'],
                    
                    // Lingkar kepala dan lingkar lengan bisa kosong (nullable)
                    'lingkar_kepala' => null,
                    'lingkar_lengan' => null,
                    
                    // ASI eksklusif semua 'ya'
                    'asi_eksklusif' => 'ya',
                    
                    // Status imunisasi lengkap semua 'ya'
                    'imunisasi_lengkap' => 'ya',
                    
                    // Riwayat penyakit kosong
                    'riwayat_penyakit' => null,
                    
                    // Pendapatan rata-rata semua di 1jt-2jt (1000000 berdasarkan mapping di controller)
                    'pendapatan_keluarga' => 1000000,
                    
                    // Pendidikan ibu rata-rata semua SMA/sederajat
                    'pendidikan_ibu' => 'sma',
                    
                    // Anggota keluarga tidak lebih dari 5 (random 3-5)
                    'jumlah_anggota_keluarga' => rand(3, 5),
                    
                    // Akses air bersih semua 'ya'
                    'akses_air_bersih' => 'ya',
                    
                    // Sanitasi layak semua 'ya'
                    'sanitasi_layak' => 'ya',
                    
                    // User ID (admin yang membuat)
                    'user_id' => 1,
                    
                    // Timestamps
                    'created_at' => $tanggalPengukuran,
                    'updated_at' => $tanggalPengukuran,
                ];
                
                Pengukuran::create($dataSeeder);
                $this->command->line('✓ Berhasil membuat pengukuran untuk: ' . $balita->nama_balita);
                
                // Generate prediksi gizi menggunakan FuzzyAhpService
                $pengukuranRecord = Pengukuran::where('balita_id', $balita->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($pengukuranRecord) {
                    try {
                        // Resolve FuzzyAhpService dari service container
                        $fuzzyAhpService = app(FuzzyAhpService::class);
                        $prediction = $fuzzyAhpService->predictNutritionalStatus($pengukuranRecord);
                        
                        if ($prediction) {
                            $this->command->line('  ✓ Prediksi gizi berhasil dibuat: ' . $prediction->status_gizi);
                        } else {
                            $this->command->warn('  ⚠ Gagal membuat prediksi gizi untuk: ' . $balita->nama_balita);
                        }
                    } catch (\Exception $predictionError) {
                        $this->command->error('  ✗ Error prediksi untuk ' . $balita->nama_balita . ': ' . $predictionError->getMessage());
                    }
                } else {
                    $this->command->error('  ✗ Tidak dapat menemukan data pengukuran yang baru dibuat untuk: ' . $balita->nama_balita);
                }
                
            } catch (\Exception $e) {
                $this->command->error('✗ Error untuk balita ' . $balita->nama_balita . ': ' . $e->getMessage());
                continue;
            }
        }

        $this->command->info('Berhasil menambahkan ' . count($pengukuranData) . ' data pengukuran');
        
        // Tampilkan statistik prediksi
        $this->showPredictionStatistics();
        
        $this->command->line('Detail seeder:');
        $this->command->line('- ASI Eksklusif: Semua YA');
        $this->command->line('- Status Imunisasi: Semua YA (Lengkap)');
        $this->command->line('- Pendapatan Keluarga: Semua Rp 1.000.000 - Rp 2.000.000');
        $this->command->line('- Pendidikan Ibu: Semua SMA/Sederajat');
        $this->command->line('- Jumlah Anggota Keluarga: 3-5 orang (random)');
        $this->command->line('- Akses Air Bersih: Semua YA');
        $this->command->line('- Sanitasi Layak: Semua YA');
        $this->command->line('- Lingkar Kepala & Lingkar Lengan: Kosong (NULL)');
        $this->command->line('- Riwayat Penyakit: Kosong (NULL)');
        $this->command->line('- Umur Bulan: Dihitung otomatis berdasarkan tanggal lahir dan tanggal pengukuran');
        $this->command->line('- Prediksi Gizi: Digenerate otomatis menggunakan FuzzyAhpService');
    }

    private function showPredictionStatistics()
    {
        try {
            // Ambil statistik prediksi yang baru dibuat
            $predictions = \App\Models\PrediksiGizi::whereDate('created_at', today())
                ->with('pengukuran.balita')
                ->get()
                ->groupBy('status_gizi');

            if ($predictions->isEmpty()) {
                $this->command->warn('Tidak ada prediksi yang berhasil dibuat hari ini');
                return;
            }

            $this->command->line('');
            $this->command->info('=== STATISTIK PREDIKSI GIZI ===');
            
            foreach ($predictions as $status => $items) {
                $count = $items->count();
                $percentage = round(($count / $predictions->flatten()->count()) * 100, 1);
                $this->command->line("- {$status}: {$count} balita ({$percentage}%)");
                
                // Tampilkan beberapa contoh nama balita
                $sampleNames = $items->take(3)->pluck('pengukuran.balita.nama_balita')->implode(', ');
                if ($items->count() > 3) {
                    $sampleNames .= ', dll.';
                }
                $this->command->line("  Contoh: {$sampleNames}");
            }
            
            $this->command->line('');
        } catch (\Exception $e) {
            $this->command->error('Error saat menampilkan statistik: ' . $e->getMessage());
        }
    }
}