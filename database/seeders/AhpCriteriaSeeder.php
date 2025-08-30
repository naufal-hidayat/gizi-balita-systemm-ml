<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AhpCriteria;

class AhpCriteriaSeeder extends Seeder
{
    public function run()
    {
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Tinggi Badan menurut Umur (TB/U)',
                'description' => 'Indikator stunting berdasarkan z-score tinggi badan menurut umur',
                'weight' => 0.35,
                'is_active' => true,
            ],
            [
                'code' => 'C2', 
                'name' => 'Berat Badan menurut Umur (BB/U)',
                'description' => 'Indikator underweight berdasarkan z-score berat badan menurut umur',
                'weight' => 0.25,
                'is_active' => true,
            ],
            [
                'code' => 'C3',
                'name' => 'Berat Badan menurut Tinggi Badan (BB/TB)',
                'description' => 'Indikator wasting berdasarkan z-score berat badan menurut tinggi badan',
                'weight' => 0.20,
                'is_active' => true,
            ],
            [
                'code' => 'C4',
                'name' => 'Status Ekonomi Keluarga',
                'description' => 'Pendapatan per kapita dan jumlah anggota keluarga',
                'weight' => 0.10,
                'is_active' => true,
            ],
            [
                'code' => 'C5',
                'name' => 'Pendidikan Ibu',
                'description' => 'Tingkat pendidikan formal ibu',
                'weight' => 0.05,
                'is_active' => true,
            ],
            [
                'code' => 'C6',
                'name' => 'Kondisi Lingkungan',
                'description' => 'Akses air bersih dan sanitasi layak',
                'weight' => 0.03,
                'is_active' => true,
            ],
            [
                'code' => 'C7',
                'name' => 'ASI Eksklusif',
                'description' => 'Pemberian ASI eksklusif 0-6 bulan',
                'weight' => 0.01,
                'is_active' => true,
            ],
            [
                'code' => 'C8',
                'name' => 'Status Imunisasi',
                'description' => 'Kelengkapan imunisasi dasar',
                'weight' => 0.01,
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $criterion) {
            AhpCriteria::create($criterion);
        }
    }
}