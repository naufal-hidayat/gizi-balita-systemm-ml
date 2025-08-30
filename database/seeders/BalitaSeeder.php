<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Balita;
use App\Models\Pengukuran;
use App\Models\PrediksiGizi;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BalitaSeeder extends Seeder
{
    private $riwayatPenyakit = [
        'Tidak ada riwayat penyakit serius',
        'Pernah demam tinggi',
        'Riwayat diare',
        'Pernah batuk pilek',
        'Riwayat alergi makanan',
        'Pernah infeksi telinga',
        'Riwayat konstipasi',
        'Pernah bronkitis ringan'
    ];

    public function run()
    {
        DB::transaction(function () {
            $this->command->info('Memulai seeding data balita sederhana...');

            // Data balita hardcoded untuk setiap posyandu
            $dataBalitaPerPosyandu = [
                'Posyandu Melati Timur' => [
                    ['nama' => 'Ahmad Rizki', 'jk' => 'L', 'ortu' => 'Bapak Agus & Ibu Sri', 'desa' => 'Desa Sukamaju'],
                    ['nama' => 'Aisha Putri', 'jk' => 'P', 'ortu' => 'Bapak Bambang & Ibu Siti', 'desa' => 'Desa Sukamaju'],
                    ['nama' => 'Muhammad Fajar', 'jk' => 'L', 'ortu' => 'Bapak Candra & Ibu Rina', 'desa' => 'Desa Sejahtera'],
                    ['nama' => 'Bella Cantika', 'jk' => 'P', 'ortu' => 'Bapak Dedi & Ibu Lina', 'desa' => 'Desa Sejahtera'],
                    ['nama' => 'Andi Pratama', 'jk' => 'L', 'ortu' => 'Bapak Eko & Ibu Maya', 'desa' => 'Desa Sukamaju'],
                    ['nama' => 'Citra Dewi', 'jk' => 'P', 'ortu' => 'Bapak Fajar & Ibu Nita', 'desa' => 'Desa Sukamaju'],
                    ['nama' => 'Budi Santoso', 'jk' => 'L', 'ortu' => 'Bapak Gunawan & Ibu Dewi', 'desa' => 'Desa Sejahtera'],
                    ['nama' => 'Dina Maharani', 'jk' => 'P', 'ortu' => 'Bapak Hadi & Ibu Ratna', 'desa' => 'Desa Sejahtera'],
                    ['nama' => 'Candra Wijaya', 'jk' => 'L', 'ortu' => 'Bapak Indra & Ibu Sari', 'desa' => 'Desa Sukamaju'],
                    ['nama' => 'Elsa Permata', 'jk' => 'P', 'ortu' => 'Bapak Joko & Ibu Tuti', 'desa' => 'Desa Sejahtera'],
                ],
                'Posyandu Mawar Timur' => [
                    ['nama' => 'Dedi Kusuma', 'jk' => 'L', 'ortu' => 'Bapak Kurnia & Ibu Wati', 'desa' => 'Desa Maju Jaya'],
                    ['nama' => 'Fitri Handayani', 'jk' => 'P', 'ortu' => 'Bapak Lukman & Ibu Yani', 'desa' => 'Desa Maju Jaya'],
                    ['nama' => 'Eko Saputra', 'jk' => 'L', 'ortu' => 'Bapak Made & Ibu Kadek', 'desa' => 'Desa Timur Indah'],
                    ['nama' => 'Gita Sari', 'jk' => 'P', 'ortu' => 'Bapak Nugroho & Ibu Ani', 'desa' => 'Desa Timur Indah'],
                    ['nama' => 'Fahri Rahman', 'jk' => 'L', 'ortu' => 'Bapak Omar & Ibu Putri', 'desa' => 'Desa Maju Jaya'],
                    ['nama' => 'Hana Cantika', 'jk' => 'P', 'ortu' => 'Bapak Parto & Ibu Qonita', 'desa' => 'Desa Maju Jaya'],
                    ['nama' => 'Gilang Pradana', 'jk' => 'L', 'ortu' => 'Bapak Rahmat & Ibu Retno', 'desa' => 'Desa Timur Indah'],
                    ['nama' => 'Indira Putri', 'jk' => 'P', 'ortu' => 'Bapak Susilo & Ibu Sinta', 'desa' => 'Desa Timur Indah'],
                    ['nama' => 'Hendra Putra', 'jk' => 'L', 'ortu' => 'Bapak Tono & Ibu Umi', 'desa' => 'Desa Maju Jaya'],
                    ['nama' => 'Jasmine Aura', 'jk' => 'P', 'ortu' => 'Bapak Wahyu & Ibu Vina', 'desa' => 'Desa Timur Indah'],
                ],
                'Posyandu Kenanga Timur' => [
                    ['nama' => 'Indra Gunawan', 'jk' => 'L', 'ortu' => 'Bapak Ahmad & Ibu Fatimah', 'desa' => 'Desa Sumber Rejeki'],
                    ['nama' => 'Kirana Dewi', 'jk' => 'P', 'ortu' => 'Bapak Budiman & Ibu Sari', 'desa' => 'Desa Sumber Rejeki'],
                    ['nama' => 'Joko Susilo', 'jk' => 'L', 'ortu' => 'Bapak Cahyono & Ibu Retno', 'desa' => 'Desa Karya Mulya'],
                    ['nama' => 'Luna Safira', 'jk' => 'P', 'ortu' => 'Bapak Darman & Ibu Lestari', 'desa' => 'Desa Karya Mulya'],
                    ['nama' => 'Krisna Dwi', 'jk' => 'L', 'ortu' => 'Bapak Edi & Ibu Murti', 'desa' => 'Desa Sumber Rejeki'],
                    ['nama' => 'Maya Salsabila', 'jk' => 'P', 'ortu' => 'Bapak Ferry & Ibu Novi', 'desa' => 'Desa Sumber Rejeki'],
                    ['nama' => 'Lukman Hakim', 'jk' => 'L', 'ortu' => 'Bapak Gatot & Ibu Dwi', 'desa' => 'Desa Karya Mulya'],
                    ['nama' => 'Nadia Azzahra', 'jk' => 'P', 'ortu' => 'Bapak Hendra & Ibu Rini', 'desa' => 'Desa Karya Mulya'],
                    ['nama' => 'Made Surya', 'jk' => 'L', 'ortu' => 'Bapak Ivan & Ibu Sinta', 'desa' => 'Desa Sumber Rejeki'],
                    ['nama' => 'Olivia Ramadhani', 'jk' => 'P', 'ortu' => 'Bapak Jaka & Ibu Tri', 'desa' => 'Desa Karya Mulya'],
                ],
                'Posyandu Anggrek Barat' => [
                    ['nama' => 'Nanda Arya', 'jk' => 'L', 'ortu' => 'Bapak Kartono & Ibu Widi', 'desa' => 'Desa Bahagia'],
                    ['nama' => 'Putri Anggraini', 'jk' => 'P', 'ortu' => 'Bapak Lambang & Ibu Yati', 'desa' => 'Desa Bahagia'],
                    ['nama' => 'Omar Khayyam', 'jk' => 'L', 'ortu' => 'Bapak Mukti & Ibu Zahra', 'desa' => 'Desa Harmoni'],
                    ['nama' => 'Qonita Zahra', 'jk' => 'P', 'ortu' => 'Bapak Nanang & Ibu Aida', 'desa' => 'Desa Harmoni'],
                    ['nama' => 'Putra Mahendra', 'jk' => 'L', 'ortu' => 'Bapak Ogi & Ibu Pita', 'desa' => 'Desa Bahagia'],
                    ['nama' => 'Rani Kusuma', 'jk' => 'P', 'ortu' => 'Bapak Qasim & Ibu Rika', 'desa' => 'Desa Bahagia'],
                    ['nama' => 'Qori Ramadan', 'jk' => 'L', 'ortu' => 'Bapak Ridwan & Ibu Siska', 'desa' => 'Desa Harmoni'],
                    ['nama' => 'Sari Melati', 'jk' => 'P', 'ortu' => 'Bapak Teguh & Ibu Ula', 'desa' => 'Desa Harmoni'],
                    ['nama' => 'Rio Alfian', 'jk' => 'L', 'ortu' => 'Bapak Udin & Ibu Vera', 'desa' => 'Desa Bahagia'],
                    ['nama' => 'Tika Wulandari', 'jk' => 'P', 'ortu' => 'Bapak Wawan & Ibu Xenia', 'desa' => 'Desa Harmoni'],
                ],
                'Posyandu Dahlia Barat' => [
                    ['nama' => 'Satrio Baskara', 'jk' => 'L', 'ortu' => 'Bapak Yanto & Ibu Yesi', 'desa' => 'Desa Barat Makmur'],
                    ['nama' => 'Aisyah Ramadhani', 'jk' => 'P', 'ortu' => 'Bapak Zaenal & Ibu Zumi', 'desa' => 'Desa Barat Makmur'],
                    ['nama' => 'Teguh Purnomo', 'jk' => 'L', 'ortu' => 'Bapak Arif & Ibu Bella', 'desa' => 'Desa Sentosa'],
                    ['nama' => 'Berlian Cahaya', 'jk' => 'P', 'ortu' => 'Bapak Beni & Ibu Cinta', 'desa' => 'Desa Sentosa'],
                    ['nama' => 'Usman Hakim', 'jk' => 'L', 'ortu' => 'Bapak Chandra & Ibu Dara', 'desa' => 'Desa Barat Makmur'],
                    ['nama' => 'Cantika Sari', 'jk' => 'P', 'ortu' => 'Bapak Danu & Ibu Elsa', 'desa' => 'Desa Barat Makmur'],
                    ['nama' => 'Victor Ardian', 'jk' => 'L', 'ortu' => 'Bapak Erwin & Ibu Fira', 'desa' => 'Desa Sentosa'],
                    ['nama' => 'Dira Purnama', 'jk' => 'P', 'ortu' => 'Bapak Fadli & Ibu Gita', 'desa' => 'Desa Sentosa'],
                    ['nama' => 'Wahyu Pratama', 'jk' => 'L', 'ortu' => 'Bapak Gani & Ibu Hana', 'desa' => 'Desa Barat Makmur'],
                    ['nama' => 'Eka Maharani', 'jk' => 'P', 'ortu' => 'Bapak Heri & Ibu Intan', 'desa' => 'Desa Sentosa'],
                ],
                'Posyandu Cempaka Barat' => [
                    ['nama' => 'Xavier Ananda', 'jk' => 'L', 'ortu' => 'Bapak Imam & Ibu Jihan', 'desa' => 'Desa Bina Sejahtera'],
                    ['nama' => 'Farah Diba', 'jk' => 'P', 'ortu' => 'Bapak Jaka & Ibu Kirana', 'desa' => 'Desa Bina Sejahtera'],
                    ['nama' => 'Yoga Permana', 'jk' => 'L', 'ortu' => 'Bapak Koko & Ibu Laila', 'desa' => 'Desa Mulia Jaya'],
                    ['nama' => 'Giselle Putri', 'jk' => 'P', 'ortu' => 'Bapak Luki & Ibu Maira', 'desa' => 'Desa Mulia Jaya'],
                    ['nama' => 'Zaki Rahman', 'jk' => 'L', 'ortu' => 'Bapak Maman & Ibu Naura', 'desa' => 'Desa Bina Sejahtera'],
                    ['nama' => 'Hasna Aulia', 'jk' => 'P', 'ortu' => 'Bapak Novan & Ibu Oktavia', 'desa' => 'Desa Bina Sejahtera'],
                    ['nama' => 'Arief Budiman', 'jk' => 'L', 'ortu' => 'Bapak Oscar & Ibu Pricilia', 'desa' => 'Desa Mulia Jaya'],
                    ['nama' => 'Intan Permata', 'jk' => 'P', 'ortu' => 'Bapak Qomar & Ibu Rania', 'desa' => 'Desa Mulia Jaya'],
                    ['nama' => 'Bayu Saputra', 'jk' => 'L', 'ortu' => 'Bapak Rafli & Ibu Sinta', 'desa' => 'Desa Bina Sejahtera'],
                    ['nama' => 'Jelita Sari', 'jk' => 'P', 'ortu' => 'Bapak Syahrul & Ibu Tiara', 'desa' => 'Desa Mulia Jaya'],
                ],
                'Posyandu Seruni Utara' => [
                    ['nama' => 'Chandra Kusuma', 'jk' => 'L', 'ortu' => 'Bapak Taufik & Ibu Ully', 'desa' => 'Desa Maju'],
                    ['nama' => 'Kamila Azzahra', 'jk' => 'P', 'ortu' => 'Bapak Ulil & Ibu Valencia', 'desa' => 'Desa Maju'],
                    ['nama' => 'Dafa Pratama', 'jk' => 'L', 'ortu' => 'Bapak Vino & Ibu Wulan', 'desa' => 'Desa Damai'],
                    ['nama' => 'Laila Cantika', 'jk' => 'P', 'ortu' => 'Bapak Wahid & Ibu Xara', 'desa' => 'Desa Damai'],
                    ['nama' => 'Erlangga Putra', 'jk' => 'L', 'ortu' => 'Bapak Yogi & Ibu Yasmin', 'desa' => 'Desa Maju'],
                    ['nama' => 'Maira Putri', 'jk' => 'P', 'ortu' => 'Bapak Zidan & Ibu Zahra', 'desa' => 'Desa Maju'],
                    ['nama' => 'Farel Darmawan', 'jk' => 'L', 'ortu' => 'Bapak Andi & Ibu Bella', 'desa' => 'Desa Damai'],
                    ['nama' => 'Naura Salsabila', 'jk' => 'P', 'ortu' => 'Bapak Bagas & Ibu Citra', 'desa' => 'Desa Damai'],
                    ['nama' => 'Gibran Alif', 'jk' => 'L', 'ortu' => 'Bapak Doni & Ibu Dinda', 'desa' => 'Desa Maju'],
                    ['nama' => 'Oktavia Dewi', 'jk' => 'P', 'ortu' => 'Bapak Eko & Ibu Fitri', 'desa' => 'Desa Damai'],
                ],
                'Posyandu Jasmine Utara' => [
                    ['nama' => 'Hafiz Maulana', 'jk' => 'L', 'ortu' => 'Bapak Galih & Ibu Hesti', 'desa' => 'Desa Utara Jaya'],
                    ['nama' => 'Pricilia Anggun', 'jk' => 'P', 'ortu' => 'Bapak Irfan & Ibu Jelita', 'desa' => 'Desa Utara Jaya'],
                    ['nama' => 'Ivan Setiawan', 'jk' => 'L', 'ortu' => 'Bapak Kevin & Ibu Linda', 'desa' => 'Desa Sejahtera Utara'],
                    ['nama' => 'Qurrota Aini', 'jk' => 'P', 'ortu' => 'Bapak Mario & Ibu Nisa', 'desa' => 'Desa Sejahtera Utara'],
                    ['nama' => 'Jefri Ramadan', 'jk' => 'L', 'ortu' => 'Bapak Oca & Ibu Putri', 'desa' => 'Desa Utara Jaya'],
                    ['nama' => 'Rania Safira', 'jk' => 'P', 'ortu' => 'Bapak Qori & Ibu Riska', 'desa' => 'Desa Utara Jaya'],
                    ['nama' => 'Kevin Aditya', 'jk' => 'L', 'ortu' => 'Bapak Santo & Ibu Tina', 'desa' => 'Desa Sejahtera Utara'],
                    ['nama' => 'Sinta Maharani', 'jk' => 'P', 'ortu' => 'Bapak Umar & Ibu Vera', 'desa' => 'Desa Sejahtera Utara'],
                    ['nama' => 'Luthfi Hakim', 'jk' => 'L', 'ortu' => 'Bapak Widi & Ibu Yuni', 'desa' => 'Desa Utara Jaya'],
                    ['nama' => 'Tiara Dewi', 'jk' => 'P', 'ortu' => 'Bapak Yusuf & Ibu Zahra', 'desa' => 'Desa Sejahtera Utara'],
                ],
                'Posyandu Bougenville Selatan' => [
                    ['nama' => 'Malik Ibrahim', 'jk' => 'L', 'ortu' => 'Bapak Adit & Ibu Bela', 'desa' => 'Desa Tentram'],
                    ['nama' => 'Ully Ramadhani', 'jk' => 'P', 'ortu' => 'Bapak Coki & Ibu Devi', 'desa' => 'Desa Tentram'],
                    ['nama' => 'Naufal Rizki', 'jk' => 'L', 'ortu' => 'Bapak Egi & Ibu Fani', 'desa' => 'Desa Rukun'],
                    ['nama' => 'Valencia Putri', 'jk' => 'P', 'ortu' => 'Bapak Gilang & Ibu Hani', 'desa' => 'Desa Rukun'],
                    ['nama' => 'Oscar Pratama', 'jk' => 'L', 'ortu' => 'Bapak Ikhsan & Ibu Jeni', 'desa' => 'Desa Tentram'],
                    ['nama' => 'Wulan Sari', 'jk' => 'P', 'ortu' => 'Bapak Koko & Ibu Leni', 'desa' => 'Desa Tentram'],
                    ['nama' => 'Pandu Wijaya', 'jk' => 'L', 'ortu' => 'Bapak Miko & Ibu Neni', 'desa' => 'Desa Rukun'],
                    ['nama' => 'Xara Cantika', 'jk' => 'P', 'ortu' => 'Bapak Oki & Ibu Peni', 'desa' => 'Desa Rukun'],
                    ['nama' => 'Qomar Syahid', 'jk' => 'L', 'ortu' => 'Bapak Riki & Ibu Seni', 'desa' => 'Desa Tentram'],
                    ['nama' => 'Yasmin Aulia', 'jk' => 'P', 'ortu' => 'Bapak Tomi & Ibu Uni', 'desa' => 'Desa Rukun'],
                ],
                'Posyandu Teratai Selatan' => [
                    ['nama' => 'Rafli Maulana', 'jk' => 'L', 'ortu' => 'Bapak Valdi & Ibu Weni', 'desa' => 'Desa Selatan Asri'],
                    ['nama' => 'Zahra Maharani', 'jk' => 'P', 'ortu' => 'Bapak Yadi & Ibu Zeni', 'desa' => 'Desa Selatan Asri'],
                    ['nama' => 'Syahrul Ramadan', 'jk' => 'L', 'ortu' => 'Bapak Aldi & Ibu Beni', 'desa' => 'Desa Pantai Indah'],
                    ['nama' => 'Almira Putri', 'jk' => 'P', 'ortu' => 'Bapak Dedi & Ibu Ceni', 'desa' => 'Desa Pantai Indah'],
                    ['nama' => 'Taufik Hidayat', 'jk' => 'L', 'ortu' => 'Bapak Ferdi & Ibu Geni', 'desa' => 'Desa Selatan Asri'],
                    ['nama' => 'Bianca Sari', 'jk' => 'P', 'ortu' => 'Bapak Hadi & Ibu Joni', 'desa' => 'Desa Selatan Asri'],
                    ['nama' => 'Ulil Amri', 'jk' => 'L', 'ortu' => 'Bapak Kurdi & Ibu Leni', 'desa' => 'Desa Pantai Indah'],
                    ['nama' => 'Clarissa Dewi', 'jk' => 'P', 'ortu' => 'Bapak Mirdi & Ibu Neni', 'desa' => 'Desa Pantai Indah'],
                    ['nama' => 'Vino Pratama', 'jk' => 'L', 'ortu' => 'Bapak Odi & Ibu Peni', 'desa' => 'Desa Selatan Asri'],
                    ['nama' => 'Deanara Cantika', 'jk' => 'P', 'ortu' => 'Bapak Rendi & Ibu Seni', 'desa' => 'Desa Pantai Indah'],
                ],
            ];

            $totalBalita = 0;
            $totalPengukuran = 0;

            // Loop untuk setiap posyandu
            foreach ($dataBalitaPerPosyandu as $namaPosyandu => $dataBalita) {
                $this->command->info("Memproses posyandu: {$namaPosyandu}");

                // Cari posyandu di database
                $posyandu = MasterPosyandu::where('nama_posyandu', $namaPosyandu)->first();
                if (!$posyandu) {
                    $this->command->warn("Posyandu {$namaPosyandu} tidak ditemukan di database");
                    continue;
                }

                // Cari user petugas untuk posyandu ini
                $petugas = User::where('posyandu_name', $namaPosyandu)->first();
                if (!$petugas) {
                    $this->command->warn("Petugas untuk posyandu {$namaPosyandu} tidak ditemukan");
                    continue;
                }

                // Buat 10 balita untuk posyandu ini
                $berhasilDibuat = 0;
                foreach ($dataBalita as $index => $data) {
                    // Generate NIK unik
                    $nik = '3201' . str_pad(($totalBalita + $index + 1000), 12, '0', STR_PAD_LEFT);
                    
                    // Pastikan NIK unik
                    while (Balita::where('nik_balita', $nik)->exists()) {
                        $nik = '3201' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
                    }

                    $balita = Balita::create([
                        'nama_balita' => $data['nama'],
                        'nik_balita' => $nik,
                        'tanggal_lahir' => $this->generateTanggalLahir(),
                        'jenis_kelamin' => $data['jk'],
                        'nama_orang_tua' => $data['ortu'],
                        'alamat' => $this->generateAlamat($data['desa'], $posyandu->area),
                        'posyandu' => $posyandu->nama_posyandu,
                        'area' => $posyandu->area,
                        'desa' => $data['desa'],
                        'user_id' => $petugas->id
                    ]);

                    // Buat 1 pengukuran untuk balita ini
                    $this->createSinglePengukuran($balita, $petugas);

                    $berhasilDibuat++;
                    $totalPengukuran++;
                }

                $totalBalita += $berhasilDibuat;
                $this->command->info("Selesai: {$namaPosyandu} - {$berhasilDibuat} balita");
            }

            $this->command->info("✅ Seeding selesai!");
            $this->command->info("📊 Total data yang dibuat:");
            $this->command->info("   - Balita: {$totalBalita}");
            $this->command->info("   - Pengukuran: {$totalPengukuran}");
            $this->command->info("   - Prediksi Gizi: {$totalPengukuran}");
            $this->command->info("📈 Rasio: 1 Balita = 1 Pengukuran = 1 Prediksi");
        });
    }

    private function generateTanggalLahir()
    {
        // Balita usia 6 bulan - 5 tahun
        $startDate = Carbon::now()->subYears(5);
        $endDate = Carbon::now()->subMonths(6);
        
        return Carbon::createFromTimestamp(
            rand($startDate->timestamp, $endDate->timestamp)
        )->format('Y-m-d');
    }

    private function generateAlamat($namaDesa, $area)
    {
        $jalan = [
            'Jl. Merdeka', 'Jl. Kartini', 'Jl. Diponegoro', 'Jl. Sudirman', 'Jl. Gajah Mada',
            'Jl. Pahlawan', 'Jl. Kemerdekaan', 'Jl. Proklamasi', 'Jl. Veteran', 'Jl. Pemuda'
        ];

        return $jalan[array_rand($jalan)] . ' No. ' . rand(1, 100) . ', RT.' . 
               str_pad(rand(1, 15), 2, '0', STR_PAD_LEFT) . '/RW.' . 
               str_pad(rand(1, 8), 2, '0', STR_PAD_LEFT) . ', ' . $namaDesa . 
               ', Kecamatan ' . ucfirst($area);
    }

    private function createSinglePengukuran($balita, $petugas)
    {
        $tanggalLahir = Carbon::parse($balita->tanggal_lahir);
        $tanggalPengukuran = Carbon::now()->subDays(rand(1, 30)); // 1-30 hari yang lalu
        $umurBulan = $tanggalLahir->diffInMonths($tanggalPengukuran);
        
        // Pastikan umur minimal 6 bulan
        if ($umurBulan < 6) {
            $tanggalPengukuran = $tanggalLahir->copy()->addMonths(6);
            $umurBulan = 6;
        }

        $pengukuran = Pengukuran::create([
            'balita_id' => $balita->id,
            'user_id' => $petugas->id,
            'tanggal_pengukuran' => $tanggalPengukuran->format('Y-m-d'),
            'umur_bulan' => $umurBulan,
            'berat_badan' => $this->generateBeratBadan($umurBulan, $balita->jenis_kelamin),
            'tinggi_badan' => $this->generateTinggiBadan($umurBulan, $balita->jenis_kelamin),
            'lingkar_kepala' => $this->generateLingkarKepala($umurBulan),
            'lingkar_lengan' => $this->generateLingkarLengan($umurBulan),
            'asi_eksklusif' => $umurBulan <= 6 ? (rand(0, 100) < 70 ? 'ya' : 'tidak') : 'tidak',
            'imunisasi_lengkap' => $this->generateStatusImunisasi($umurBulan),
            'riwayat_penyakit' => $this->riwayatPenyakit[array_rand($this->riwayatPenyakit)],
            'pendapatan_keluarga' => $this->generatePendapatanKeluarga(),
            'pendidikan_ibu' => $this->generatePendidikanIbu(),
            'jumlah_anggota_keluarga' => rand(3, 7),
            'akses_air_bersih' => rand(0, 100) < 85 ? 'ya' : 'tidak',
            'sanitasi_layak' => rand(0, 100) < 75 ? 'ya' : 'tidak'
        ]);

        // Buat prediksi gizi untuk pengukuran ini
        $this->createPrediksiGizi($pengukuran);
    }

    private function generateBeratBadan($umurBulan, $jenisKelamin)
    {
        $baseWeight = [
            6 => ['L' => 7.9, 'P' => 7.3],
            12 => ['L' => 9.6, 'P' => 9.0],
            24 => ['L' => 12.2, 'P' => 11.5],
            36 => ['L' => 14.3, 'P' => 13.9],
            48 => ['L' => 16.3, 'P' => 16.1],
            60 => ['L' => 18.3, 'P' => 18.2]
        ];

        $weight = $this->interpolateValue($baseWeight, $umurBulan, $jenisKelamin);
        $variation = $this->getWeightVariation();
        
        return round($weight * $variation, 1);
    }

    private function generateTinggiBadan($umurBulan, $jenisKelamin)
    {
        $baseHeight = [
            6 => ['L' => 67.6, 'P' => 65.7],
            12 => ['L' => 75.7, 'P' => 74.0],
            24 => ['L' => 87.1, 'P' => 86.4],
            36 => ['L' => 96.1, 'P' => 95.6],
            48 => ['L' => 103.3, 'P' => 103.0],
            60 => ['L' => 110.0, 'P' => 109.4]
        ];

        $height = $this->interpolateValue($baseHeight, $umurBulan, $jenisKelamin);
        $variation = $this->getHeightVariation();
        
        return round($height * $variation, 1);
    }

    private function generateLingkarKepala($umurBulan)
    {
        $baseCircumference = min(40 + ($umurBulan * 0.3), 52);
        return round($baseCircumference + rand(-20, 20) / 10, 1);
    }

    private function generateLingkarLengan($umurBulan)
    {
        $baseArm = min(13 + ($umurBulan * 0.1), 17);
        return round($baseArm + rand(-15, 15) / 10, 1);
    }

    private function generateStatusImunisasi($umurBulan)
    {
        if ($umurBulan < 12) {
            return rand(0, 100) < 60 ? 'tidak_lengkap' : 'ya';
        } else {
            return rand(0, 100) < 80 ? 'ya' : 'tidak_lengkap';
        }
    }

    private function generatePendapatanKeluarga()
    {
        $ranges = [
            [1500000, 2500000], // Rendah
            [2500000, 4000000], // Sedang  
            [4000000, 6000000], // Menengah
            [6000000, 10000000] // Tinggi
        ];
        
        $range = $ranges[array_rand($ranges)];
        return rand($range[0], $range[1]);
    }

    private function generatePendidikanIbu()
    {
        $pendidikan = ['sd', 'smp', 'sma', 'diploma', 'sarjana'];
        $weights = [20, 25, 35, 10, 10];
        
        return $this->weightedRandom($pendidikan, $weights);
    }

    private function interpolateValue($baseValues, $umurBulan, $jenisKelamin)
    {
        $ages = array_keys($baseValues);
        
        if ($umurBulan <= $ages[0]) {
            return $baseValues[$ages[0]][$jenisKelamin];
        }
        
        if ($umurBulan >= end($ages)) {
            return $baseValues[end($ages)][$jenisKelamin];
        }
        
        for ($i = 0; $i < count($ages) - 1; $i++) {
            if ($umurBulan >= $ages[$i] && $umurBulan <= $ages[$i + 1]) {
                $ratio = ($umurBulan - $ages[$i]) / ($ages[$i + 1] - $ages[$i]);
                $value1 = $baseValues[$ages[$i]][$jenisKelamin];
                $value2 = $baseValues[$ages[$i + 1]][$jenisKelamin];
                return $value1 + ($value2 - $value1) * $ratio;
            }
        }
        
        return $baseValues[$ages[0]][$jenisKelamin];
    }

    private function getWeightVariation()
    {
        $rand = rand(1, 100);
        if ($rand <= 30) {
            return rand(70, 85) / 100; // Underweight
        } elseif ($rand <= 80) {
            return rand(90, 110) / 100; // Normal
        } else {
            return rand(115, 135) / 100; // Overweight
        }
    }

    private function getHeightVariation()
    {
        $rand = rand(1, 100);
        if ($rand <= 25) {
            return rand(80, 90) / 100; // Stunted
        } elseif ($rand <= 85) {
            return rand(95, 105) / 100; // Normal
        } else {
            return rand(105, 115) / 100; // Tall
        }
    }

    private function weightedRandom($array, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        for ($i = 0; $i < count($array); $i++) {
            $currentWeight += $weights[$i];
            if ($random <= $currentWeight) {
                return $array[$i];
            }
        }
        
        return $array[0];
    }

    private function createPrediksiGizi($pengukuran)
    {
        $zscoreBBU = $this->calculateSimpleZScore($pengukuran->berat_badan, 
                                                 $this->getStandardWeight($pengukuran->umur_bulan));
        $zscoreTBU = $this->calculateSimpleZScore($pengukuran->tinggi_badan, 
                                                 $this->getStandardHeight($pengukuran->umur_bulan));
        $zscoreBBTB = $this->calculateSimpleZScore($pengukuran->berat_badan, 
                                                  $this->getWeightForHeight($pengukuran->tinggi_badan));

        $statusBBU = $this->getStatusBBU($zscoreBBU);
        $statusTBU = $this->getStatusTBU($zscoreTBU);
        $statusBBTB = $this->getStatusBBTB($zscoreBBTB);

        $prediksiData = $this->generateFuzzyAHPData($pengukuran, $zscoreTBU, $zscoreBBU);

        PrediksiGizi::create([
            'pengukuran_id' => $pengukuran->id,
            'zscore_bb_u' => round($zscoreBBU, 2),
            'zscore_tb_u' => round($zscoreTBU, 2),
            'zscore_bb_tb' => round($zscoreBBTB, 2),
            'status_bb_u' => $statusBBU,
            'status_tb_u' => $statusTBU,
            'status_bb_tb' => $statusBBTB,
            'fuzzy_weights' => json_encode($prediksiData['fuzzy_weights']),
            'fuzzy_scores' => json_encode($prediksiData['fuzzy_scores']),
            'final_score' => $prediksiData['final_score'],
            'prediksi_status' => $prediksiData['prediksi_status'],
            'confidence_level' => $prediksiData['confidence_level'],
            'rekomendasi' => $prediksiData['rekomendasi'],
            'prioritas' => $prediksiData['prioritas']
        ]);
    }

    private function calculateSimpleZScore($actual, $standard)
    {
        $sd = $standard * 0.15;
        return ($actual - $standard) / $sd;
    }

    private function getStandardWeight($umurBulan)
    {
        return 7.5 + ($umurBulan * 0.2);
    }

    private function getStandardHeight($umurBulan)
    {
        return 65 + ($umurBulan * 1.2);
    }

    private function getWeightForHeight($tinggi)
    {
        return ($tinggi / 100) * ($tinggi / 100) * 16;
    }

    private function getStatusBBU($zscore)
    {
        if ($zscore < -3) return 'gizi_buruk';
        if ($zscore < -2) return 'gizi_kurang';
        if ($zscore > 2) return 'gizi_lebih';
        return 'gizi_baik';
    }

    private function getStatusTBU($zscore)
    {
        if ($zscore < -3) return 'sangat_pendek';
        if ($zscore < -2) return 'pendek';
        if ($zscore > 2) return 'tinggi';
        return 'normal';
    }

    private function getStatusBBTB($zscore)
    {
        if ($zscore < -3) return 'sangat_kurus';
        if ($zscore < -2) return 'kurus';
        if ($zscore > 2) return 'gemuk';
        return 'normal';
    }

    private function generateFuzzyAHPData($pengukuran, $zscoreTBU, $zscoreBBU)
    {
        $fuzzyWeights = [
            'antropometri' => round(rand(25, 35) / 100, 3),
            'kesehatan' => round(rand(20, 30) / 100, 3),
            'sosial_ekonomi' => round(rand(15, 25) / 100, 3),
            'lingkungan' => round(rand(15, 25) / 100, 3)
        ];

        $totalWeight = array_sum($fuzzyWeights);
        foreach ($fuzzyWeights as &$weight) {
            $weight = round($weight / $totalWeight, 3);
        }

        $fuzzyScores = [
            'zscore_tinggi' => $this->scoreFromZScore($zscoreTBU),
            'zscore_berat' => $this->scoreFromZScore($zscoreBBU),
            'asi_eksklusif' => $pengukuran->asi_eksklusif === 'ya' ? 0.8 : 0.3,
            'imunisasi' => $pengukuran->imunisasi_lengkap === 'ya' ? 0.9 : 0.4,
            'pendapatan' => $this->scoreFromIncome($pengukuran->pendapatan_keluarga),
            'pendidikan_ibu' => $this->scoreFromEducation($pengukuran->pendidikan_ibu),
            'air_bersih' => $pengukuran->akses_air_bersih === 'ya' ? 0.8 : 0.2,
            'sanitasi' => $pengukuran->sanitasi_layak === 'ya' ? 0.8 : 0.2
        ];

        $finalScore = 
            ($fuzzyScores['zscore_tinggi'] * 0.4 + $fuzzyScores['zscore_berat'] * 0.6) * $fuzzyWeights['antropometri'] +
            ($fuzzyScores['asi_eksklusif'] * 0.6 + $fuzzyScores['imunisasi'] * 0.4) * $fuzzyWeights['kesehatan'] +
            ($fuzzyScores['pendapatan'] * 0.6 + $fuzzyScores['pendidikan_ibu'] * 0.4) * $fuzzyWeights['sosial_ekonomi'] +
            ($fuzzyScores['air_bersih'] * 0.5 + $fuzzyScores['sanitasi'] * 0.5) * $fuzzyWeights['lingkungan'];

        $prediksiStatus = $this->determinePredictionStatus($finalScore, $zscoreTBU);
        $confidenceLevel = rand(60, 95);
        $prioritas = $this->determinePriority($prediksiStatus, $finalScore);
        $rekomendasi = $this->generateRecommendation($prediksiStatus, $pengukuran);

        return [
            'fuzzy_weights' => $fuzzyWeights,
            'fuzzy_scores' => $fuzzyScores,
            'final_score' => round($finalScore, 3),
            'prediksi_status' => $prediksiStatus,
            'confidence_level' => $confidenceLevel,
            'prioritas' => $prioritas,
            'rekomendasi' => $rekomendasi
        ];
    }

    private function scoreFromZScore($zscore)
    {
        if ($zscore >= 0) return 0.8;
        if ($zscore >= -1) return 0.6;
        if ($zscore >= -2) return 0.4;
        if ($zscore >= -3) return 0.2;
        return 0.1;
    }

    private function scoreFromIncome($income)
    {
        if ($income >= 5000000) return 0.9;
        if ($income >= 3000000) return 0.7;
        if ($income >= 2000000) return 0.5;
        if ($income >= 1500000) return 0.3;
        return 0.1;
    }

    private function scoreFromEducation($education)
    {
        $scores = [
            'sarjana' => 0.9,
            'diploma' => 0.8,
            'sma' => 0.6,
            'smp' => 0.4,
            'sd' => 0.2
        ];
        return $scores[$education] ?? 0.2;
    }

    private function determinePredictionStatus($finalScore, $zscoreTBU)
    {
        if ($zscoreTBU <= -2 && $finalScore < 0.5) {
            return 'stunting';
        }
        if ($zscoreTBU <= -1.5 || $finalScore < 0.4) {
            return 'berisiko_stunting';
        }
        if ($finalScore > 0.8) {
            return 'gizi_lebih';
        }
        return 'normal';
    }

    private function determinePriority($status, $score)
    {
        if ($status === 'stunting' || $score < 0.3) return 'tinggi';
        if ($status === 'berisiko_stunting' || $score < 0.5) return 'sedang';
        return 'rendah';
    }

    private function generateRecommendation($status, $pengukuran)
    {
        $recommendations = [
            'stunting' => 'Intervensi gizi intensif, konsultasi dokter anak, pemantauan ketat pertumbuhan, pemberian makanan bergizi tinggi.',
            'berisiko_stunting' => 'Perbaikan pola makan, pemberian makanan tambahan, edukasi gizi untuk orang tua, pemantauan rutin.',
            'normal' => 'Pertahankan pola makan sehat, lanjutkan pemantauan rutin, berikan stimulasi tumbuh kembang optimal.',
            'gizi_lebih' => 'Atur pola makan seimbang, tingkatkan aktivitas fisik, konsultasi ahli gizi untuk diet yang tepat.'
        ];

        return $recommendations[$status];
    }
}