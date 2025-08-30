<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pengukuran extends Model
{
    use HasFactory;

    protected $table = 'pengukuran';

    protected $fillable = [
        'balita_id',
        'user_id',
        'tanggal_pengukuran',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'asi_eksklusif',
        'imunisasi_lengkap',
        'pendapatan_keluarga',
        'pendidikan_ibu',
        'akses_air_bersih',
        'sanitasi_layak',
        'jumlah_anggota_keluarga',
        'riwayat_penyakit'
    ];

    protected $casts = [
        'tanggal_pengukuran' => 'date',
        'umur_bulan' => 'integer',
        'berat_badan' => 'decimal:1',
        'tinggi_badan' => 'decimal:1',
        'lingkar_kepala' => 'decimal:1',
        'lingkar_lengan' => 'decimal:1',
        'pendapatan_keluarga' => 'integer',
        'jumlah_anggota_keluarga' => 'integer'
    ];

    /**
     * Relationship with Balita
     */
    // public function balita()
    // {
    //     return $this->belongsTo(Balita::class);
    // }

    public function balita()
    {
        return $this->belongsTo(\App\Models\Balita::class, 'balita_id');
    }

    /**
     * Relationship with User (who performed the measurement)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with PrediksiGizi
     */
    // public function prediksiGizi()
    // {
    //     return $this->hasOne(PrediksiGizi::class);
    // }

    public function prediksiGizi()
    {
        return $this->hasOne(\App\Models\PrediksiGizi::class, 'pengukuran_id');
    }

    /**
     * Accessor for formatted measurement date
     */
    public function getFormattedDateAttribute()
    {
        return $this->tanggal_pengukuran->format('d/m/Y');
    }

    /**
     * Accessor for ASI Eksklusif label
     */
    public function getAsiEksklusifLabelAttribute()
    {
        return $this->asi_eksklusif === 'ya' ? 'Ya' : 'Tidak';
    }

    /**
     * Accessor for Imunisasi label
     */
    public function getImunisasiLabelAttribute()
    {
        $labels = [
            'ya' => 'Lengkap',
            'tidak_lengkap' => 'Tidak Lengkap',
            'tidak' => 'Belum Imunisasi'
        ];

        return $labels[$this->imunisasi_lengkap] ?? 'Unknown';
    }

    /**
     * Accessor for Pendidikan Ibu label
     */
    public function getPendidikanIbuLabelAttribute()
    {
        $labels = [
            'sd' => 'SD/Sederajat',
            'smp' => 'SMP/Sederajat',
            'sma' => 'SMA/Sederajat',
            'diploma' => 'Diploma',
            'sarjana' => 'Sarjana/Lebih Tinggi'
        ];

        return $labels[$this->pendidikan_ibu] ?? 'Unknown';
    }

    /**
     * Accessor for Akses Air Bersih label
     */
    public function getAksesAirBersihLabelAttribute()
    {
        return $this->akses_air_bersih === 'ya' ? 'Ya' : 'Tidak';
    }

    /**
     * Accessor for Sanitasi Layak label
     */
    public function getSanitasiLayakLabelAttribute()
    {
        return $this->sanitasi_layak === 'ya' ? 'Ya' : 'Tidak';
    }

    /**
     * Accessor for formatted income
     */
    public function getFormattedIncomeAttribute()
    {
        return 'Rp ' . number_format($this->pendapatan_keluarga, 0, ',', '.');
    }

    /**
     * Accessor for income per capita
     */
    public function getIncomePerCapitaAttribute()
    {
        if ($this->jumlah_anggota_keluarga > 0) {
            return $this->pendapatan_keluarga / $this->jumlah_anggota_keluarga;
        }
        return 0;
    }

    /**
     * Accessor for formatted income per capita
     */
    public function getFormattedIncomePerCapitaAttribute()
    {
        return 'Rp ' . number_format($this->income_per_capita, 0, ',', '.');
    }

    /**
     * Calculate BMI
     */
    public function getBmiAttribute()
    {
        if ($this->berat_badan && $this->tinggi_badan) {
            $heightInMeters = $this->tinggi_badan / 100;
            return round($this->berat_badan / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Get age in years and months format
     */
    public function getFormattedAgeAttribute()
    {
        $years = intval($this->umur_bulan / 12);
        $months = $this->umur_bulan % 12;

        if ($years > 0) {
            return $years . ' tahun ' . $months . ' bulan';
        }
        return $months . ' bulan';
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pengukuran', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by posyandu (via balita)
     */
    public function scopeByPosyandu($query, $posyandu)
    {
        return $query->whereHas('balita', function ($q) use ($posyandu) {
            $q->where('posyandu', $posyandu);
        });
    }

    /**
     * Scope for filtering by age group
     */
    public function scopeByAgeGroup($query, $minAge, $maxAge)
    {
        return $query->whereBetween('umur_bulan', [$minAge, $maxAge]);
    }

    /**
     * Scope for recent measurements
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('tanggal_pengukuran', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope for measurements with predictions
     */
    public function scopeWithPrediction($query)
    {
        return $query->has('prediksiGizi');
    }

    /**
     * Scope for measurements without predictions
     */
    public function scopeWithoutPrediction($query)
    {
        return $query->doesntHave('prediksiGizi');
    }

    /**
     * Check if measurement has high-risk prediction
     */
    public function hasHighRiskPrediction()
    {
        return $this->prediksiGizi &&
            ($this->prediksiGizi->prediksi_status === 'stunting' ||
                $this->prediksiGizi->prioritas === 'tinggi');
    }

    /**
     * Get nutritional status summary
     */
    public function getNutritionalStatusSummary()
    {
        if (!$this->prediksiGizi) {
            return [
                'status' => 'Belum ada prediksi',
                'color' => 'gray',
                'priority' => 'unknown'
            ];
        }

        $statusColors = [
            'stunting' => 'red',
            'berisiko_stunting' => 'yellow',
            'normal' => 'green',
            'gizi_lebih' => 'blue'
        ];

        return [
            'status' => $this->prediksiGizi->status_label,
            'color' => $statusColors[$this->prediksiGizi->prediksi_status] ?? 'gray',
            'priority' => $this->prediksiGizi->prioritas,
            'confidence' => $this->prediksiGizi->confidence_level
        ];
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a measurement is deleted, also delete its prediction
        static::deleting(function ($pengukuran) {
            if ($pengukuran->prediksiGizi) {
                $pengukuran->prediksiGizi->delete();
            }
        });
    }
}
