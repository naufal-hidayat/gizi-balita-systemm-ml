<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterPosyandu extends Model
{
    use HasFactory;

    protected $table = 'master_posyandu';

    protected $fillable = [
        'nama_posyandu',
        'area',
        'alamat',
        'ketua_posyandu',
        'kontak',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function masterDesas()
    {
        return $this->hasMany(MasterDesa::class);
    }

    public function balitas()
    {
        return $this->hasMany(Balita::class, 'master_posyandu_id');
    }

    public function desas()
    {
        return $this->hasMany(MasterDesa::class, 'master_posyandu_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function getAreaLabelAttribute()
    {
        $areas = [
            'timur' => 'Timur',
            'barat' => 'Barat', 
            'utara' => 'Utara',
            'selatan' => 'Selatan'
        ];

        return $areas[$this->area] ?? ucfirst($this->area);
    }

    public function getTotalBalitaAttribute()
    {
        return $this->balitas()->count();
    }

    public function getTotalDesaAttribute()
    {
        return $this->masterDesas()->active()->count();
    }

    // Statistik untuk machine learning
    public function getStuntingStatsAttribute()
    {
        $totalPengukuran = $this->balitas()
            ->with('latestPengukuran.prediksiGizi')
            ->get()
            ->filter(function($balita) {
                return $balita->latestPengukuran && $balita->latestPengukuran->prediksiGizi;
            });

        $stats = [
            'total' => $totalPengukuran->count(),
            'stunting' => $totalPengukuran->filter(function($balita) {
                return $balita->latestPengukuran->prediksiGizi->prediksi_status === 'stunting';
            })->count(),
            'berisiko_stunting' => $totalPengukuran->filter(function($balita) {
                return $balita->latestPengukuran->prediksiGizi->prediksi_status === 'berisiko_stunting';
            })->count(),
            'normal' => $totalPengukuran->filter(function($balita) {
                return $balita->latestPengukuran->prediksiGizi->prediksi_status === 'normal';
            })->count(),
            'gizi_lebih' => $totalPengukuran->filter(function($balita) {
                return $balita->latestPengukuran->prediksiGizi->prediksi_status === 'gizi_lebih';
            })->count(),
        ];

        return $stats;
    }
}