<?php

namespace App\Services;

use App\Models\Pengukuran;
use App\Models\PrediksiGizi;
use Carbon\Carbon;

class FuzzyAhpService
{
    protected $ahpMatrix;
    protected $criteriaWeights;
    
    public function __construct()
    {
        $this->initializeAhpMatrix();
        $this->calculateAhpWeights();
    }

    /**
     * STEP 1: INITIALIZE AHP PAIRWISE COMPARISON MATRIX
     */
    private function initializeAhpMatrix()
    {
        // AHP Pairwise Comparison Matrix (9-point scale)
        // Berdasarkan prioritas faktor yang mempengaruhi status gizi balita
        
        $this->ahpMatrix = [
            //        TB/U  BB/U  BB/TB  Ekon  Eduk  Ling  ASI  Imun
            'TB_U'  => [1,    2,    3,     5,    7,    8,    9,   9],   // Tinggi/Umur (Stunting) - Prioritas tertinggi
            'BB_U'  => [0.5,  1,    2,     4,    6,    7,    8,   8],   // Berat/Umur  
            'BB_TB' => [0.33, 0.5,  1,     3,    5,    6,    7,   7],   // Berat/Tinggi (Wasting)
            'EKON'  => [0.2,  0.25, 0.33,  1,    3,    4,    5,   5],   // Ekonomi
            'EDUK'  => [0.14, 0.17, 0.2,   0.33, 1,    2,    3,   3],   // Pendidikan
            'LING'  => [0.125,0.14, 0.17,  0.25, 0.5,  1,    2,   2],   // Lingkungan
            'ASI'   => [0.11, 0.125,0.14,  0.2,  0.33, 0.5,  1,   1],   // ASI
            'IMUN'  => [0.11, 0.125,0.14,  0.2,  0.33, 0.5,  1,   1]    // Imunisasi
        ];
    }

    /**
     * STEP 2: CALCULATE AHP WEIGHTS USING EIGENVALUE METHOD
     */
    private function calculateAhpWeights()
    {
        $matrix = $this->ahpMatrix;
        $n = count($matrix);
        
        // Calculate column sums
        $columnSums = array_fill(0, $n, 0);
        
        $matrixValues = array_values($matrix);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $columnSums[$j] += $matrixValues[$i][$j];
            }
        }
        
        // Normalize matrix and calculate weights
        $normalizedMatrix = [];
        $criteriaNames = array_keys($matrix);
        
        for ($i = 0; $i < $n; $i++) {
            $normalizedMatrix[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $normalizedMatrix[$i][$j] = $matrixValues[$i][$j] / $columnSums[$j];
            }
        }
        
        // Calculate priority weights (row averages)
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSum = array_sum($normalizedMatrix[$i]);
            $weights[$criteriaNames[$i]] = $rowSum / $n;
        }
        
        // Calculate Consistency Ratio
        $cr = $this->calculateConsistencyRatio($matrix, $weights);
        
        $this->criteriaWeights = [
            'weights' => $weights,
            'consistency_ratio' => $cr,
            'is_consistent' => $cr <= 0.1
        ];
        
        return $this->criteriaWeights;
    }

    /**
     * STEP 3: CALCULATE CONSISTENCY RATIO
     */
    private function calculateConsistencyRatio($matrix, $weights)
    {
        $n = count($matrix);
        
        // Random Consistency Index (RI)
        $ri = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45, 1.49];
        
        if ($n < 3) return 0;
        
        // Calculate λmax
        $matrixValues = array_values($matrix);
        $criteriaNames = array_keys($matrix);
        $weightedSum = [];
        
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matrixValues[$i][$j] * $weights[$criteriaNames[$j]];
            }
            $weightedSum[] = $sum;
        }
        
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $weightedSum[$i] / $weights[$criteriaNames[$i]];
        }
        $lambdaMax = $lambdaMax / $n;
        
        // Calculate CI and CR
        $ci = ($lambdaMax - $n) / ($n - 1);
        $cr = $ci / $ri[$n - 1];
        
        return $cr;
    }

    /**
     * STEP 4: CALCULATE Z-SCORES USING WHO STANDARDS (SIMPLIFIED)
     */
    protected function calculateZScores(Pengukuran $pengukuran)
    {
        $umur = $pengukuran->umur_bulan;
        $bb = $pengukuran->berat_badan;
        $tb = $pengukuran->tinggi_badan;
        $jenisKelamin = $pengukuran->balita->jenis_kelamin;

        return [
            'bb_u' => $this->calculateBBU($bb, $umur, $jenisKelamin),
            'tb_u' => $this->calculateTBU($tb, $umur, $jenisKelamin),
            'bb_tb' => $this->calculateBBTB($bb, $tb, $jenisKelamin)
        ];
    }

    // Simplified WHO Weight-for-Age calculation
    protected function calculateBBU($bb, $umur, $jenisKelamin)
    {
        // Median weight based on WHO standards (simplified)
        $median = $jenisKelamin == 'L' ? 
            (3.3 + ($umur * 0.45)) : (3.2 + ($umur * 0.42));
        $sd = 1.2 + ($umur * 0.02); // Standard deviation increases with age
        
        return ($bb - $median) / $sd;
    }

    // Simplified WHO Height-for-Age calculation
    protected function calculateTBU($tb, $umur, $jenisKelamin)
    {
        // Median height based on WHO standards (simplified)
        $median = $jenisKelamin == 'L' ? 
            (50 + ($umur * 2.0)) : (49.5 + ($umur * 1.95));
        $sd = 2.3 + ($umur * 0.01);
        
        return ($tb - $median) / $sd;
    }

    // Simplified WHO Weight-for-Height calculation
    protected function calculateBBTB($bb, $tb, $jenisKelamin)
    {
        // Simplified calculation based on height
        $median = ($tb * 0.08) + 1.5;
        $sd = 1.0;
        
        return ($bb - $median) / $sd;
    }

    /**
     * STEP 5: FUZZY MEMBERSHIP FUNCTIONS
     */
    private function calculateFuzzyMembership(Pengukuran $pengukuran, $zScores)
    {
        return [
            'stunting' => $this->fuzzyStunting($zScores['tb_u']),
            'underweight' => $this->fuzzyUnderweight($zScores['bb_u']),
            'wasting' => $this->fuzzyWasting($zScores['bb_tb']),
            'ekonomi' => $this->fuzzyEkonomi($pengukuran->pendapatan_keluarga, $pengukuran->jumlah_anggota_keluarga),
            'pendidikan' => $this->fuzzyPendidikan($pengukuran->pendidikan_ibu),
            'lingkungan' => $this->fuzzyLingkungan($pengukuran->akses_air_bersih, $pengukuran->sanitasi_layak),
            'asi' => $this->fuzzyASI($pengukuran->asi_eksklusif),
            'imunisasi' => $this->fuzzyImunisasi($pengukuran->imunisasi_lengkap)
        ];
    }

    /**
     * FUZZY MEMBERSHIP FUNCTIONS
     */
    
    // Stunting Risk (Height-for-Age Z-Score)
    private function fuzzyStunting($zScore)
    {
        if ($zScore <= -3) return 1.0;      // Severely stunted
        if ($zScore <= -2) return 0.8;      // Stunted
        if ($zScore <= -1) return 0.5;      // At risk
        if ($zScore <= 0) return 0.2;       // Mild risk
        return 0.1;                         // Normal
    }

    // Underweight Risk (Weight-for-Age Z-Score)
    private function fuzzyUnderweight($zScore)
    {
        if ($zScore <= -3) return 1.0;      // Severely underweight
        if ($zScore <= -2) return 0.8;      // Underweight
        if ($zScore <= -1) return 0.5;      // At risk
        if ($zScore <= 0) return 0.2;       // Mild risk
        return 0.1;                         // Normal
    }

    // Wasting Risk (Weight-for-Height Z-Score)
    private function fuzzyWasting($zScore)
    {
        if ($zScore <= -3) return 1.0;      // Severely wasted
        if ($zScore <= -2) return 0.7;      // Wasted
        if ($zScore <= -1) return 0.4;      // At risk
        if ($zScore <= 0) return 0.2;       // Mild risk
        return 0.1;                         // Normal
    }

    // Economic Risk
    private function fuzzyEkonomi($pendapatan, $jumlahAnggota)
    {
        $pendapatanPerKapita = $pendapatan / $jumlahAnggota;
        
        if ($pendapatanPerKapita < 300000) return 0.9;      // Very poor
        if ($pendapatanPerKapita < 600000) return 0.7;      // Poor
        if ($pendapatanPerKapita < 1200000) return 0.5;     // Lower middle
        if ($pendapatanPerKapita < 2000000) return 0.3;     // Middle
        return 0.1;                                         // Well-off
    }

    // Education Risk
    private function fuzzyPendidikan($pendidikan)
    {
        $mapping = [
            'sd' => 0.8,        // Low education
            'smp' => 0.6,       // Lower secondary
            'sma' => 0.4,       // Upper secondary
            'diploma' => 0.2,   // Higher
            'sarjana' => 0.1    // University
        ];
        
        return $mapping[$pendidikan] ?? 0.5;
    }

    // Environment Risk
    private function fuzzyLingkungan($airBersih, $sanitasi)
    {
        $score = 0;
        if ($airBersih == 'tidak') $score += 0.5;
        if ($sanitasi == 'tidak') $score += 0.5;
        return $score;
    }

    // Breastfeeding Risk
    private function fuzzyASI($asiEksklusif)
    {
        return $asiEksklusif == 'tidak' ? 0.7 : 0.1;
    }

    // Immunization Risk
    private function fuzzyImunisasi($imunisasi)
    {
        $mapping = [
            'tidak' => 0.8,
            'tidak_lengkap' => 0.5,
            'ya' => 0.1
        ];
        
        return $mapping[$imunisasi] ?? 0.5;
    }

    /**
     * STEP 6: FUZZY INFERENCE AND AGGREGATION
     */
    private function fuzzyInference($fuzzyScores, $ahpWeights)
    {
        // Apply AHP weights to fuzzy scores
        $weightedScore = 0;
        $weightedScore += $fuzzyScores['stunting'] * $ahpWeights['TB_U'];
        $weightedScore += $fuzzyScores['underweight'] * $ahpWeights['BB_U'];
        $weightedScore += $fuzzyScores['wasting'] * $ahpWeights['BB_TB'];
        $weightedScore += $fuzzyScores['ekonomi'] * $ahpWeights['EKON'];
        $weightedScore += $fuzzyScores['pendidikan'] * $ahpWeights['EDUK'];
        $weightedScore += $fuzzyScores['lingkungan'] * $ahpWeights['LING'];
        $weightedScore += $fuzzyScores['asi'] * $ahpWeights['ASI'];
        $weightedScore += $fuzzyScores['imunisasi'] * $ahpWeights['IMUN'];
        
        // Defuzzification - convert to categorical result
        return $this->defuzzify($weightedScore, $fuzzyScores);
    }

    /**
     * STEP 7: DEFUZZIFICATION
     */
    private function defuzzify($weightedScore, $fuzzyScores)
    {
        // Determine status based on weighted score and individual indicators
        $status = 'normal';
        $confidence = 50;
        $priority = 'rendah';
        
        // Primary indicators (anthropometric)
        $anthropometricRisk = max($fuzzyScores['stunting'], $fuzzyScores['underweight'], $fuzzyScores['wasting']);
        
        if ($weightedScore >= 0.7 || $anthropometricRisk >= 0.8) {
            $status = 'stunting';
            $confidence = 85;
            $priority = 'tinggi';
        } elseif ($weightedScore >= 0.5 || $anthropometricRisk >= 0.6) {
            $status = 'berisiko_stunting';
            $confidence = 75;
            $priority = 'sedang';
        } elseif ($weightedScore >= 0.3 || $anthropometricRisk >= 0.4) {
            $status = 'berisiko_stunting';
            $confidence = 65;
            $priority = 'sedang';
        } elseif ($weightedScore <= 0.2 && $anthropometricRisk <= 0.2) {
            $status = 'normal';
            $confidence = 80;
            $priority = 'rendah';
        }
        
        // Check for overweight/obesity
        if ($fuzzyScores['wasting'] < 0.1 && $weightedScore < 0.3) {
            $status = 'gizi_lebih';
            $confidence = 70;
            $priority = 'sedang';
        }
        
        return [
            'status' => $status,
            'confidence' => min(95, max(50, $confidence)),
            'priority' => $priority,
            'final_score' => $weightedScore,
            'anthropometric_risk' => $anthropometricRisk
        ];
    }

    /**
     * STEP 8: GENERATE RECOMMENDATIONS
     */
    protected function generateRecommendations($pengukuran, $result, $zScores)
    {
        $rekomendasi = [];

        switch ($result['status']) {
            case 'stunting':
                $rekomendasi[] = "🚨 PRIORITAS TINGGI: Balita mengalami stunting";
                $rekomendasi[] = "Rujuk SEGERA ke fasilitas kesehatan untuk penanganan intensif";
                $rekomendasi[] = "Berikan makanan bergizi tinggi dengan protein hewani";
                $rekomendasi[] = "Pantau pertumbuhan setiap minggu";
                $rekomendasi[] = "Konseling gizi intensif untuk orang tua";
                break;
                
            case 'berisiko_stunting':
                $rekomendasi[] = "⚠️ Balita berisiko stunting, perlu pemantauan ketat";
                $rekomendasi[] = "Tingkatkan asupan gizi dengan makanan beragam";
                $rekomendasi[] = "Berikan makanan kaya protein, zat besi, dan zinc";
                $rekomendasi[] = "Konseling gizi untuk orang tua";
                $rekomendasi[] = "Kontrol ulang dalam 2 minggu";
                break;
                
            case 'normal':
                $rekomendasi[] = "✅ Status gizi normal, pertahankan pola makan sehat";
                $rekomendasi[] = "Lanjutkan pemantauan rutin setiap bulan";
                $rekomendasi[] = "Berikan stimulasi tumbuh kembang yang optimal";
                $rekomendasi[] = "Pertahankan pola hidup sehat keluarga";
                break;
                
            case 'gizi_lebih':
                $rekomendasi[] = "📈 Gizi lebih, atur pola makan dan aktivitas";
                $rekomendasi[] = "Konsultasi dengan ahli gizi";
                $rekomendasi[] = "Tingkatkan aktivitas fisik sesuai usia";
                $rekomendasi[] = "Batasi makanan tinggi gula dan lemak";
                break;
        }

        // Specific recommendations based on risk factors
        if ($pengukuran->asi_eksklusif == 'tidak') {
            $rekomendasi[] = "💡 Edukasi pentingnya ASI eksklusif untuk bayi < 6 bulan";
        }

        if ($pengukuran->imunisasi_lengkap != 'ya') {
            $rekomendasi[] = "💉 Lengkapi imunisasi sesuai jadwal";
        }

        if ($pengukuran->akses_air_bersih == 'tidak' || $pengukuran->sanitasi_layak == 'tidak') {
            $rekomendasi[] = "🚰 Perbaiki akses air bersih dan sanitasi";
        }

        if ($pengukuran->pendapatan_keluarga / $pengukuran->jumlah_anggota_keluarga < 500000) {
            $rekomendasi[] = "💰 Manfaatkan program bantuan gizi pemerintah";
        }

        if ($pengukuran->pendidikan_ibu == 'sd') {
            $rekomendasi[] = "📚 Perluas pengetahuan gizi melalui penyuluhan posyandu";
        }

        return implode("\n", $rekomendasi);
    }

    /**
     * MAIN PREDICTION METHOD
     */
    public function predictNutritionalStatus(Pengukuran $pengukuran)
    {
        try {
            // Step 1: Calculate Z-Scores
            $zScores = $this->calculateZScores($pengukuran);
            
            // Step 2: Get AHP Weights
            $ahpWeights = $this->criteriaWeights['weights'];
            
            // Step 3: Calculate Fuzzy Membership Values
            $fuzzyScores = $this->calculateFuzzyMembership($pengukuran, $zScores);
            
            // Step 4: Fuzzy Inference and Defuzzification
            $result = $this->fuzzyInference($fuzzyScores, $ahpWeights);
            
            // Step 5: Generate Recommendations
            $rekomendasi = $this->generateRecommendations($pengukuran, $result, $zScores);
            
            // Step 6: Save Prediction
            return $this->savePrediction($pengukuran, $zScores, $fuzzyScores, $ahpWeights, $result, $rekomendasi);
            
        } catch (\Exception $e) {
            \Log::error('Fuzzy-AHP Prediction Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * SAVE PREDICTION RESULT
     */
    protected function savePrediction($pengukuran, $zScores, $fuzzyScores, $ahpWeights, $result, $rekomendasi)
    {
        try {
            return PrediksiGizi::create([
                'pengukuran_id' => $pengukuran->id,
                'zscore_bb_u' => round($zScores['bb_u'], 2),
                'zscore_tb_u' => round($zScores['tb_u'], 2),
                'zscore_bb_tb' => round($zScores['bb_tb'], 2),
                'status_bb_u' => $this->getStatusFromZScore($zScores['bb_u'], 'weight'),
                'status_tb_u' => $this->getStatusFromZScore($zScores['tb_u'], 'height'),
                'status_bb_tb' => $this->getStatusFromZScore($zScores['bb_tb'], 'wht'),
                'fuzzy_weights' => $ahpWeights,
                'fuzzy_scores' => array_merge($fuzzyScores, [
                    'ahp_consistency_ratio' => $this->criteriaWeights['consistency_ratio'],
                    'ahp_is_consistent' => $this->criteriaWeights['is_consistent']
                ]),
                'final_score' => round($result['final_score'], 3),
                'prediksi_status' => $result['status'],
                'confidence_level' => $result['confidence'],
                'rekomendasi' => $rekomendasi,
                'prioritas' => $result['priority'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving prediction: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * GET STATUS FROM Z-SCORE
     */
    protected function getStatusFromZScore($zScore, $type)
    {
        switch ($type) {
            case 'weight':
                if ($zScore < -3) return 'gizi_buruk';
                if ($zScore < -2) return 'gizi_kurang';
                if ($zScore > 2) return 'gizi_lebih';
                return 'gizi_baik';
                
            case 'height':
                if ($zScore < -3) return 'sangat_pendek';
                if ($zScore < -2) return 'pendek';
                if ($zScore > 2) return 'tinggi';
                return 'normal';
                
            case 'wht':
                if ($zScore < -3) return 'sangat_kurus';
                if ($zScore < -2) return 'kurus';
                if ($zScore > 2) return 'gemuk';
                return 'normal';
                
            default:
                return 'normal';
        }
    }
}