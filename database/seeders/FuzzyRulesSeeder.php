<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuzzyRule;

class FuzzyRulesSeeder extends Seeder
{
    public function run()
    {
        $rules = [
            [
                'rule_name' => 'Rule 1: Stunting Severe',
                'conditions' => [
                    'TB_U' => 'severely_stunted',
                    'BB_U' => 'severely_underweight',
                    'ekonomi' => 'sangat_rendah'
                ],
                'conclusion' => 'stunting',
                'weight' => 1.0,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 2: Stunting Moderate',
                'conditions' => [
                    'TB_U' => 'stunted',
                    'BB_U' => 'underweight',
                    'pendidikan' => 'rendah'
                ],
                'conclusion' => 'stunting',
                'weight' => 0.9,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 3: Berisiko Stunting',
                'conditions' => [
                    'TB_U' => 'at_risk',
                    'ASI' => 'tidak',
                    'lingkungan' => 'buruk'
                ],
                'conclusion' => 'berisiko_stunting',
                'weight' => 0.8,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 4: Normal dengan Faktor Risiko',
                'conditions' => [
                    'TB_U' => 'normal',
                    'BB_U' => 'normal',
                    'ekonomi' => 'rendah',
                    'imunisasi' => 'tidak_lengkap'
                ],
                'conclusion' => 'berisiko_stunting',
                'weight' => 0.6,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 5: Status Normal',
                'conditions' => [
                    'TB_U' => 'normal',
                    'BB_U' => 'normal',
                    'BB_TB' => 'normal',
                    'ASI' => 'ya',
                    'imunisasi' => 'lengkap'
                ],
                'conclusion' => 'normal',
                'weight' => 1.0,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 6: Gizi Lebih',
                'conditions' => [
                    'BB_U' => 'overweight',
                    'BB_TB' => 'obese'
                ],
                'conclusion' => 'gizi_lebih',
                'weight' => 0.9,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 7: Wasting dengan Stunting',
                'conditions' => [
                    'TB_U' => 'stunted',
                    'BB_TB' => 'wasted',
                    'ekonomi' => 'rendah'
                ],
                'conclusion' => 'stunting',
                'weight' => 0.95,
                'is_active' => true,
            ],
            [
                'rule_name' => 'Rule 8: Lingkungan Buruk',
                'conditions' => [
                    'air_bersih' => 'tidak',
                    'sanitasi' => 'tidak',
                    'pendidikan' => 'rendah'
                ],
                'conclusion' => 'berisiko_stunting',
                'weight' => 0.7,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            FuzzyRule::create($rule);
        }
    }
}