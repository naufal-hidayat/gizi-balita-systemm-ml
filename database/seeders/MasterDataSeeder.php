<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPosyandu;
use App\Models\MasterDesa;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        // Master Posyandu Area Timur
        $posyandu1 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Melati Timur',
            'area' => 'timur',
            'alamat' => 'Jl. Timur Raya No. 15, Kecamatan Timur',
            'ketua_posyandu' => 'Ibu Sari Rahayu',
            'kontak' => '08123456789'
        ]);

        $posyandu2 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Mawar Timur',
            'area' => 'timur',
            'alamat' => 'Jl. Matahari No. 22, Kecamatan Timur',
            'ketua_posyandu' => 'Ibu Dewi Sartika',
            'kontak' => '08987654321'
        ]);

        $posyandu3 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Kenanga Timur',
            'area' => 'timur',
            'alamat' => 'Jl. Sunrise No. 8, Kecamatan Timur',
            'ketua_posyandu' => 'Ibu Rina Kartika',
            'kontak' => '08111222333'
        ]);

        // Master Posyandu Area Barat
        $posyandu4 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Anggrek Barat',
            'area' => 'barat',
            'alamat' => 'Jl. Barat Indah No. 8, Kecamatan Barat',
            'ketua_posyandu' => 'Ibu Maya Sari',
            'kontak' => '08444555666'
        ]);

        $posyandu5 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Dahlia Barat',
            'area' => 'barat',
            'alamat' => 'Jl. Sunset No. 12, Kecamatan Barat',
            'ketua_posyandu' => 'Ibu Lestari Wulan',
            'kontak' => '08777888999'
        ]);

        $posyandu6 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Cempaka Barat',
            'area' => 'barat',
            'alamat' => 'Jl. Harmoni No. 25, Kecamatan Barat',
            'ketua_posyandu' => 'Ibu Indah Permata',
            'kontak' => '08000111222'
        ]);

        $posyandu7 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Karang Anyar',
            'area' => 'barat',
            'alamat' => 'Karang Anyar, Desa Puncak, Kecamatan Cigugur',
            'ketua_posyandu' => 'Ibu Permata Sari',
            'kontak' => '081231112212'
        ]);

        // Master Posyandu Area Utara
        $posyandu8 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Seruni Utara',
            'area' => 'utara',
            'alamat' => 'Jl. Utara Sejahtera No. 5, Kecamatan Utara',
            'ketua_posyandu' => 'Ibu Fitri Handayani',
            'kontak' => '08333444555'
        ]);

        $posyandu9 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Jasmine Utara',
            'area' => 'utara',
            'alamat' => 'Jl. Pegunungan No. 18, Kecamatan Utara',
            'ketua_posyandu' => 'Ibu Ani Suryani',
            'kontak' => '08666777888'
        ]);

        // Master Posyandu Area Selatan
        $posyandu10 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Bougenville Selatan',
            'area' => 'selatan',
            'alamat' => 'Jl. Selatan Permai No. 10, Kecamatan Selatan',
            'ketua_posyandu' => 'Ibu Siti Aminah',
            'kontak' => '08999000111'
        ]);

        $posyandu11 = MasterPosyandu::create([
            'nama_posyandu' => 'Posyandu Teratai Selatan',
            'area' => 'selatan',
            'alamat' => 'Jl. Pantai No. 25, Kecamatan Selatan',
            'ketua_posyandu' => 'Ibu Nur Aini',
            'kontak' => '08222333444'
        ]);

        // Master Desa untuk setiap posyandu
        
        // Desa Area Timur
        MasterDesa::create(['nama_desa' => 'Desa Sukamaju', 'area' => 'timur', 'master_posyandu_id' => $posyandu1->id, 'jumlah_penduduk' => 1500]);
        MasterDesa::create(['nama_desa' => 'Desa Sejahtera', 'area' => 'timur', 'master_posyandu_id' => $posyandu1->id, 'jumlah_penduduk' => 1200]);
        MasterDesa::create(['nama_desa' => 'Desa Maju Jaya', 'area' => 'timur', 'master_posyandu_id' => $posyandu2->id, 'jumlah_penduduk' => 1800]);
        MasterDesa::create(['nama_desa' => 'Desa Timur Indah', 'area' => 'timur', 'master_posyandu_id' => $posyandu2->id, 'jumlah_penduduk' => 1350]);
        MasterDesa::create(['nama_desa' => 'Desa Sumber Rejeki', 'area' => 'timur', 'master_posyandu_id' => $posyandu3->id, 'jumlah_penduduk' => 1600]);
        MasterDesa::create(['nama_desa' => 'Desa Karya Mulya', 'area' => 'timur', 'master_posyandu_id' => $posyandu3->id, 'jumlah_penduduk' => 1400]);

        // Desa Area Barat
        MasterDesa::create(['nama_desa' => 'Desa Bahagia', 'area' => 'barat', 'master_posyandu_id' => $posyandu4->id, 'jumlah_penduduk' => 1650]);
        MasterDesa::create(['nama_desa' => 'Desa Harmoni', 'area' => 'barat', 'master_posyandu_id' => $posyandu4->id, 'jumlah_penduduk' => 1450]);
        MasterDesa::create(['nama_desa' => 'Desa Barat Makmur', 'area' => 'barat', 'master_posyandu_id' => $posyandu5->id, 'jumlah_penduduk' => 1750]);
        MasterDesa::create(['nama_desa' => 'Desa Sentosa', 'area' => 'barat', 'master_posyandu_id' => $posyandu5->id, 'jumlah_penduduk' => 1300]);
        MasterDesa::create(['nama_desa' => 'Desa Bina Sejahtera', 'area' => 'barat', 'master_posyandu_id' => $posyandu6->id, 'jumlah_penduduk' => 1550]);
        MasterDesa::create(['nama_desa' => 'Desa Mulia Jaya', 'area' => 'barat', 'master_posyandu_id' => $posyandu6->id, 'jumlah_penduduk' => 1420]);
        MasterDesa::create(['nama_desa' => 'Desa Sukamulya', 'area' => 'barat', 'master_posyandu_id' => $posyandu7->id, 'jumlah_penduduk' => 1000]);

        // Desa Area Utara
        MasterDesa::create(['nama_desa' => 'Desa Maju', 'area' => 'utara', 'master_posyandu_id' => $posyandu8->id, 'jumlah_penduduk' => 1700]);
        MasterDesa::create(['nama_desa' => 'Desa Damai', 'area' => 'utara', 'master_posyandu_id' => $posyandu8->id, 'jumlah_penduduk' => 1150]);
        MasterDesa::create(['nama_desa' => 'Desa Utara Jaya', 'area' => 'utara', 'master_posyandu_id' => $posyandu9->id, 'jumlah_penduduk' => 1900]);
        MasterDesa::create(['nama_desa' => 'Desa Sejahtera Utara', 'area' => 'utara', 'master_posyandu_id' => $posyandu9->id, 'jumlah_penduduk' => 1250]);

        // Desa Area Selatan
        MasterDesa::create(['nama_desa' => 'Desa Tentram', 'area' => 'selatan', 'master_posyandu_id' => $posyandu10->id, 'jumlah_penduduk' => 1550]);
        MasterDesa::create(['nama_desa' => 'Desa Rukun', 'area' => 'selatan', 'master_posyandu_id' => $posyandu10->id, 'jumlah_penduduk' => 1250]);
        MasterDesa::create(['nama_desa' => 'Desa Selatan Asri', 'area' => 'selatan', 'master_posyandu_id' => $posyandu11->id, 'jumlah_penduduk' => 1450]);
        MasterDesa::create(['nama_desa' => 'Desa Pantai Indah', 'area' => 'selatan', 'master_posyandu_id' => $posyandu11->id, 'jumlah_penduduk' => 1800]);
    }
}