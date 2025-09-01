<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Balita;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BalitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Data dari CSV yang sudah diparsing
        $balitaData = [
            ['nama' => 'Adiba el Putri', 'tanggal_lahir' => '23/06/2022', 'nama_orang_tua' => 'Titin', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'AHSAN', 'tanggal_lahir' => '07/11/2020', 'nama_orang_tua' => 'Andi', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'ALEEA SHANUM A', 'tanggal_lahir' => '08/03/2024', 'nama_orang_tua' => 'WIWIN/ONA', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'ALESHA NAURA', 'tanggal_lahir' => '17/08/2024', 'nama_orang_tua' => 'ANDI', 'jk' => 'P', 'rt' => '016', 'rw' => '006'],
            ['nama' => 'Alesya Auristela', 'tanggal_lahir' => '19/11/2022', 'nama_orang_tua' => 'Ulfi', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'Alfareza', 'tanggal_lahir' => '14/11/2022', 'nama_orang_tua' => 'Anggun', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'ALVIAN P', 'tanggal_lahir' => '18/02/2025', 'nama_orang_tua' => 'DIAN S', 'jk' => 'L', 'rt' => '015', 'rw' => '007'],
            ['nama' => 'ALZAIDAN F', 'tanggal_lahir' => '20/02/2025', 'nama_orang_tua' => 'MAYA', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'AMEENA MAULIDA', 'tanggal_lahir' => '29/09/2023', 'nama_orang_tua' => 'SANTI', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'ANISA NADHIRA', 'tanggal_lahir' => '23/10/2023', 'nama_orang_tua' => 'Ika', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'ARA SHAZA', 'tanggal_lahir' => '30/08/2023', 'nama_orang_tua' => 'Lilis', 'jk' => 'P', 'rt' => '016', 'rw' => '006'],
            ['nama' => 'Arfan m.fawaz', 'tanggal_lahir' => '30/09/2022', 'nama_orang_tua' => 'Sri', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'ARFAN ZAIDAN M', 'tanggal_lahir' => '06/07/2023', 'nama_orang_tua' => 'Nafi', 'jk' => 'L', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'ARHAN ARDIANSYAH', 'tanggal_lahir' => '05/06/2024', 'nama_orang_tua' => 'ROS/ANDRI', 'jk' => 'L', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'ARKANZA', 'tanggal_lahir' => '15/12/2024', 'nama_orang_tua' => 'DESI/MAMAT', 'jk' => 'L', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'ARRASYA', 'tanggal_lahir' => '24/08/2023', 'nama_orang_tua' => 'Yuli', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'ARRSYA', 'tanggal_lahir' => '29/04/2023', 'nama_orang_tua' => 'Wina', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'Arshaka Syapiq', 'tanggal_lahir' => '27/09/2022', 'nama_orang_tua' => 'Vika', 'jk' => 'L', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'ARSYAP', 'tanggal_lahir' => '20/03/2024', 'nama_orang_tua' => 'FITRI/SALAHUDIN', 'jk' => 'L', 'rt' => '015', 'rw' => '007'],
            ['nama' => 'AULIA', 'tanggal_lahir' => '19/02/2022', 'nama_orang_tua' => 'Maya', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'AURIEL', 'tanggal_lahir' => '15/07/2024', 'nama_orang_tua' => 'ulfi/azril', 'jk' => 'P', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'AVIDA', 'tanggal_lahir' => '08/05/2025', 'nama_orang_tua' => 'SARI C', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'AZKA NUR TAJUL ARIFIN', 'tanggal_lahir' => '04/02/2021', 'nama_orang_tua' => 'Esa/Oni', 'jk' => 'L', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'AZKIARA', 'tanggal_lahir' => '24/11/2024', 'nama_orang_tua' => 'ESA ONI', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'BILAL DAIFULLOH', 'tanggal_lahir' => '14/01/2023', 'nama_orang_tua' => 'Uwen', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'BILQIS', 'tanggal_lahir' => '08/10/2021', 'nama_orang_tua' => 'SANTI/AAN', 'jk' => 'P', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'DAFA OKTAPIAN', 'tanggal_lahir' => '21/10/2021', 'nama_orang_tua' => 'nur/samsuri', 'jk' => 'L', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'DAFFA', 'tanggal_lahir' => '04/04/2025', 'nama_orang_tua' => 'ENOK INDRI', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'GAISKA', 'tanggal_lahir' => '17/11/2021', 'nama_orang_tua' => 'pipit/angga', 'jk' => 'P', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'GEMA FAZIANDRI', 'tanggal_lahir' => '22/10/2023', 'nama_orang_tua' => 'Sumi', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'HANINDIRA', 'tanggal_lahir' => '23/07/2021', 'nama_orang_tua' => 'DIDI', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'HILYA', 'tanggal_lahir' => '20/11/2024', 'nama_orang_tua' => 'Ujang', 'jk' => 'P', 'rt' => '003', 'rw' => '003'],
            ['nama' => 'HISYAM', 'tanggal_lahir' => '19/06/2025', 'nama_orang_tua' => 'WAHYU', 'jk' => 'L', 'rt' => '003', 'rw' => '003'],
            ['nama' => 'HUMAIRA A', 'tanggal_lahir' => '20/07/2023', 'nama_orang_tua' => 'Siti', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'HYKAYATUL', 'tanggal_lahir' => '30/11/2022', 'nama_orang_tua' => 'DEWI', 'jk' => 'P', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'KANAKA', 'tanggal_lahir' => '24/08/2023', 'nama_orang_tua' => 'Titi', 'jk' => 'L', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'KHANIA', 'tanggal_lahir' => '01/12/2021', 'nama_orang_tua' => 'Tati', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'LAURA GANTARI', 'tanggal_lahir' => '09/06/2023', 'nama_orang_tua' => 'NANI', 'jk' => 'P', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'M. ARSHAKA S', 'tanggal_lahir' => '26/02/2025', 'nama_orang_tua' => 'VINA', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'M. ARSYA A', 'tanggal_lahir' => '16/09/2024', 'nama_orang_tua' => 'EVI/ARIF', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'M. AYDAN Z', 'tanggal_lahir' => '03/11/2021', 'nama_orang_tua' => 'Iin', 'jk' => 'L', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'M. QAIS RAMADAN', 'tanggal_lahir' => '24/03/2024', 'nama_orang_tua' => 'RENI/AEF', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'MARIAM SITI HAWA', 'tanggal_lahir' => '25/12/2022', 'nama_orang_tua' => 'SARI', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'MIKAYLA', 'tanggal_lahir' => '24/08/2021', 'nama_orang_tua' => 'DIDI SARDI', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'MIQDAD', 'tanggal_lahir' => '12/09/2023', 'nama_orang_tua' => 'Iyet', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'Naira Syabila', 'tanggal_lahir' => '16/01/2023', 'nama_orang_tua' => 'Yati', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'NATHAN', 'tanggal_lahir' => '12/05/2024', 'nama_orang_tua' => 'dani/maman', 'jk' => 'L', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'NAZIFA', 'tanggal_lahir' => '24/07/2024', 'nama_orang_tua' => 'erna / guntur', 'jk' => 'P', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'PELANGI', 'tanggal_lahir' => '11/03/2022', 'nama_orang_tua' => 'Erni', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'QAIREEN AZZALEA', 'tanggal_lahir' => '04/09/2020', 'nama_orang_tua' => 'Ikrim', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'QONITA', 'tanggal_lahir' => '24/04/2024', 'nama_orang_tua' => 'UUN/IWAN', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'Radja Al Muiz', 'tanggal_lahir' => '02/09/2022', 'nama_orang_tua' => 'Tatin', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'RAFKA A.', 'tanggal_lahir' => '19/04/2021', 'nama_orang_tua' => 'JAKA', 'jk' => 'P', 'rt' => '013', 'rw' => '006'],
            ['nama' => 'RAISA', 'tanggal_lahir' => '01/03/2023', 'nama_orang_tua' => 'Tuti', 'jk' => 'P', 'rt' => '016', 'rw' => '007'],
            ['nama' => 'RIYAZ M', 'tanggal_lahir' => '09/10/2024', 'nama_orang_tua' => 'RIKI', 'jk' => 'L', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'RUBY HAVVA', 'tanggal_lahir' => '19/08/2021', 'nama_orang_tua' => 'RIKI', 'jk' => 'P', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'SAKILA NUR', 'tanggal_lahir' => '27/09/2022', 'nama_orang_tua' => 'HALIMAH/TONI', 'jk' => 'P', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'Satria Qirom', 'tanggal_lahir' => '30/09/2022', 'nama_orang_tua' => 'Mamah', 'jk' => 'L', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'SELFI S.', 'tanggal_lahir' => '02/08/2021', 'nama_orang_tua' => 'IWAN', 'jk' => 'P', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'SHABIRA', 'tanggal_lahir' => '23/06/2021', 'nama_orang_tua' => 'Mila', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'SHAQUEENA ZAYAAN', 'tanggal_lahir' => '08/02/2021', 'nama_orang_tua' => 'Maman', 'jk' => 'P', 'rt' => '015', 'rw' => '007'],
            ['nama' => 'SIDQI', 'tanggal_lahir' => '26/03/2024', 'nama_orang_tua' => 'POPI/NUR', 'jk' => 'L', 'rt' => '013', 'rw' => '007'],
            ['nama' => 'SITI AISYAH', 'tanggal_lahir' => '27/11/2021', 'nama_orang_tua' => 'SANTI/AGUS', 'jk' => 'P', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'Siti Fauziah', 'tanggal_lahir' => '14/11/2022', 'nama_orang_tua' => 'Siti Aisyah', 'jk' => 'P', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'SYAUQHY', 'tanggal_lahir' => '13/10/2021', 'nama_orang_tua' => 'Iin', 'jk' => 'L', 'rt' => '015', 'rw' => '006'],
            ['nama' => 'ZAHIRA', 'tanggal_lahir' => '21/03/2021', 'nama_orang_tua' => 'NY', 'jk' => 'P', 'rt' => '014', 'rw' => '006'],
            ['nama' => 'ZAIDAN XAVIER', 'tanggal_lahir' => '03/07/2022', 'nama_orang_tua' => 'Yaneu', 'jk' => 'L', 'rt' => '017', 'rw' => '007'],
            ['nama' => 'ZIANDRA', 'tanggal_lahir' => '02/04/2023', 'nama_orang_tua' => 'Eni', 'jk' => 'L', 'rt' => '017', 'rw' => '007'],
        ];

        // Ambil master posyandu dan desa Karang Anyar
        $masterPosyandu = MasterPosyandu::where('nama_posyandu', 'Posyandu Karang Anyar')->first();
        $masterDesa = MasterDesa::where('nama_desa', 'Desa Sukamulya')->first();

        foreach ($balitaData as $index => $data) {
            // Parse tanggal lahir
            $tanggalLahir = Carbon::createFromFormat('d/m/Y', $data['tanggal_lahir']);
            
            // Generate NIK palsu dengan format yang realistis
            $nikBalita = $this->generateFakeNIK($tanggalLahir, $data['jk'], $index);
            
            // Generate alamat lengkap
            $alamatLengkap = sprintf(
                "RT %s RW %s, Karang Anyar, Desa Puncak, Kec. Cigugur, Kab. Kuningan",
                $data['rt'],
                $data['rw']
            );

            Balita::create([
                'nama_balita' => $data['nama'],
                'nik_balita' => $nikBalita,
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $data['jk'],
                'nama_orang_tua' => $data['nama_orang_tua'],
                'alamat_lengkap' => $alamatLengkap, // untuk backward compatibility
                'rt' => str_pad(ltrim($data['rt'], '0'), 3, '0', STR_PAD_LEFT), // format 016 -> 016
                'rw' => str_pad(ltrim($data['rw'], '0'), 3, '0', STR_PAD_LEFT), // format 007 -> 007
                'dusun' => 'Karang Anyar',
                'desa_kelurahan' => 'Desa Puncak',
                'kecamatan' => 'Cigugur',
                'kabupaten' => 'Kab. Kuningan',
                'posyandu' => 'Posyandu Karang Anyar',
                'area' => 'barat',
                'desa' => 'Desa Sukamulya',
                'master_posyandu_id' => $masterPosyandu?->id,
                'master_desa_id' => $masterDesa?->id,
                'user_id' => 1, // Diasumsikan user dengan ID 1 sudah ada (admin)
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Berhasil menambahkan ' . count($balitaData) . ' data balita');
    }

    /**
     * Generate NIK palsu yang realistis
     * Format NIK: PPKKSSDDMMYYGGGN
     * PP = Kode Provinsi (32 untuk Jawa Barat)  
     * KK = Kode Kabupaten (08 untuk Kuningan)
     * SS = Kode Kecamatan (18 untuk Cigugur - contoh)
     * DDMMYY = Tanggal lahir (DD+40 untuk perempuan)
     * GGG = Nomor urut kelahiran
     * N = Check digit
     */
    private function generateFakeNIK($tanggalLahir, $jenisKelamin, $index)
    {
        // Kode wilayah: 32 (Jabar) + 08 (Kuningan) + 18 (Cigugur - contoh)
        $kodeWilayah = '320818';
        
        // Format tanggal
        $hari = $tanggalLahir->format('d');
        if ($jenisKelamin === 'P') {
            $hari = (int)$hari + 40; // Untuk perempuan, tambahkan 40
        }
        
        $bulan = $tanggalLahir->format('m');
        $tahun = $tanggalLahir->format('y');
        $tanggalStr = str_pad($hari, 2, '0', STR_PAD_LEFT) . $bulan . $tahun;
        
        // Nomor urut (3 digit) - berdasarkan index + random
        $nomorUrut = str_pad(($index + 1), 4, '0', STR_PAD_LEFT);
        
        return $kodeWilayah . $tanggalStr . $nomorUrut;
    }
}