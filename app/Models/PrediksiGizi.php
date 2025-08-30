<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrediksiGizi extends Model
{
    use HasFactory;

    protected $table = 'prediksi_gizi';

    protected $fillable = [
        'pengukuran_id',
        'zscore_bb_u',
        'zscore_tb_u',
        'zscore_bb_tb',
        'status_bb_u',
        'status_tb_u',
        'status_bb_tb',
        'fuzzy_weights',
        'fuzzy_scores',
        'final_score',
        'prediksi_status',
        'confidence_level',
        'rekomendasi',
        'prioritas'
    ];

    protected $casts = [
        'zscore_bb_u' => 'decimal:2',
        'zscore_tb_u' => 'decimal:2',
        'zscore_bb_tb' => 'decimal:2',
        'fuzzy_weights' => 'array',
        'fuzzy_scores' => 'array',
        'final_score' => 'decimal:3',
        'confidence_level' => 'decimal:1'
    ];

    /**
     * Relationship with Pengukuran
     */
    public function pengukuran()
    {
        return $this->belongsTo(Pengukuran::class);
    }

    /**
     * Get formatted status labels
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'stunting' => 'Stunting',
            'berisiko_stunting' => 'Berisiko Stunting',
            'normal' => 'Normal',
            'gizi_lebih' => 'Gizi Lebih'
        ];
        
        return $labels[$this->prediksi_status] ?? 'Unknown';
    }

    /**
     * Get priority label with color
     */
    public function getPriorityLabelAttribute()
    {
        $labels = [
            'tinggi' => ['label' => 'Tinggi', 'color' => 'red'],
            'sedang' => ['label' => 'Sedang', 'color' => 'yellow'],
            'rendah' => ['label' => 'Rendah', 'color' => 'green']
        ];
        
        return $labels[$this->prioritas] ?? ['label' => 'Unknown', 'color' => 'gray'];
    }

    /**
     * Get BB/U status label
     */
    public function getBbUStatusLabelAttribute()
    {
        $labels = [
            'gizi_buruk' => 'Gizi Buruk',
            'gizi_kurang' => 'Gizi Kurang',
            'gizi_baik' => 'Gizi Baik',
            'gizi_lebih' => 'Gizi Lebih'
        ];
        
        return $labels[$this->status_bb_u] ?? 'Unknown';
    }

    /**
     * Get TB/U status label
     */
    public function getTbUStatusLabelAttribute()
    {
        $labels = [
            'sangat_pendek' => 'Sangat Pendek',
            'pendek' => 'Pendek (Stunted)',
            'normal' => 'Normal',
            'tinggi' => 'Tinggi'
        ];
        
        return $labels[$this->status_tb_u] ?? 'Unknown';
    }

    /**
     * Get BB/TB status label
     */
    public function getBbTbStatusLabelAttribute()
    {
        $labels = [
            'sangat_kurus' => 'Sangat Kurus',
            'kurus' => 'Kurus (Wasted)',
            'normal' => 'Normal',
            'gemuk' => 'Gemuk'
        ];
        
        return $labels[$this->status_bb_tb] ?? 'Unknown';
    }

    /**
     * Get confidence level description
     */
    public function getConfidenceDescriptionAttribute()
    {
        if ($this->confidence_level >= 80) {
            return 'Sangat Tinggi';
        } elseif ($this->confidence_level >= 70) {
            return 'Tinggi';
        } elseif ($this->confidence_level >= 60) {
            return 'Sedang';
        } else {
            return 'Rendah';
        }
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('prediksi_status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('prioritas', $priority);
    }

    /**
     * Scope for high-risk cases
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('prediksi_status', ['stunting', 'berisiko_stunting'])
                    ->orWhere('prioritas', 'tinggi');
    }
}