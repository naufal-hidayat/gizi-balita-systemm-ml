<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Balita extends Model
{
    use HasFactory;

    protected $table = 'balita';

    protected $fillable = [
        'nama_balita',
        'nik_balita',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_orang_tua',
        'alamat_lengkap', // alamat lama untuk backward compatibility
        'rt',
        'rw',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten',
        'posyandu',
        'area',
        'desa',
        'master_posyandu_id',
        'master_desa_id',
        'user_id'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [];
        
        if ($this->rt) $parts[] = "RT {$this->rt}";
        if ($this->rw) $parts[] = "RW {$this->rw}";
        if ($this->dusun) $parts[] = "Dusun {$this->dusun}";
        if ($this->desa_kelurahan) $parts[] = $this->desa_kelurahan;
        if ($this->kecamatan) $parts[] = "Kec. {$this->kecamatan}";
        if ($this->kabupaten) $parts[] = $this->kabupaten;
        
        return implode(', ', array_filter($parts));
    }

    /**
     * Get short address (RT/RW, Desa)
     */
    public function getShortAddressAttribute()
    {
        $parts = [];
        
        if ($this->rt && $this->rw) {
            $parts[] = "RT {$this->rt}/RW {$this->rw}";
        } elseif ($this->rt) {
            $parts[] = "RT {$this->rt}";
        } elseif ($this->rw) {
            $parts[] = "RW {$this->rw}";
        }
        
        if ($this->desa_kelurahan) {
            $parts[] = $this->desa_kelurahan;
        }
        
        return implode(', ', array_filter($parts));
    }

    /**
     * Get complete address for official documents
     */
    public function getCompleteAddressAttribute()
    {
        $address = $this->formatted_address;
        
        // Fallback to old address if new format is empty
        if (empty(trim($address)) && $this->alamat_lengkap) {
            return $this->alamat_lengkap;
        }
        
        return $address;
    }

    /**
     * Check if address is complete
     */
    public function isAddressComplete()
    {
        return !empty($this->rt) && 
               !empty($this->rw) && 
               !empty($this->desa_kelurahan) && 
               !empty($this->kecamatan) && 
               !empty($this->kabupaten);
    }

    /**
     * Scope for filtering by kabupaten
     */
    public function scopeByKabupaten($query, $kabupaten)
    {
        return $query->where('kabupaten', $kabupaten);
    }

    /**
     * Scope for filtering by kecamatan
     */
    public function scopeByKecamatan($query, $kecamatan)
    {
        return $query->where('kecamatan', $kecamatan);
    }

    /**
     * Scope for filtering by desa/kelurahan
     */
    public function scopeByDesaKelurahan($query, $desaKelurahan)
    {
        return $query->where('desa_kelurahan', $desaKelurahan);
    }

    /**
     * Relationship with User (who registered the balita)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Pengukuran
     */
    public function pengukuran()
    {
        return $this->hasMany(Pengukuran::class);
    }

    /**
     * Get the latest measurement
     */
    public function latestPengukuran()
    {
        return $this->hasOne(Pengukuran::class)->latest('tanggal_pengukuran');
    }

    /**
     * Get measurements with predictions
     */
    public function pengukuranWithPrediction()
    {
        return $this->hasMany(Pengukuran::class)->has('prediksiGizi');
    }

    /**
     * Calculate current age in months
     */
    public function getUmurBulanAttribute()
    {
        return $this->tanggal_lahir 
        ? floor($this->tanggal_lahir->diffInMonths(now())) 
        : 0;
    }

    /**
     * Calculate age in years and months
     */
    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return '-';

        $totalMonths = $this->umur_bulan;
        $years = intval($totalMonths / 12);
        $months = $totalMonths % 12;

        return $years > 0
            ? "{$years} tahun {$months} bulan"
            : "{$months} bulan";
    }

    /**
     * Get formatted birth date
     */
    public function getFormattedBirthDateAttribute()
    {
        return $this->tanggal_lahir->format('d/m/Y');
    }

    /**
     * Get gender label
     */
    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get area label
     */
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

    /**
     * Get latest nutritional status
     */
    public function getLatestNutritionalStatusAttribute()
    {
        $latestMeasurement = $this->latestPengukuran;
        
        if (!$latestMeasurement || !$latestMeasurement->prediksiGizi) {
            return [
                'status' => 'Belum ada data',
                'color' => 'gray',
                'date' => null
            ];
        }

        $prediction = $latestMeasurement->prediksiGizi;
        $statusColors = [
            'stunting' => 'red',
            'berisiko_stunting' => 'yellow',
            'normal' => 'green',
            'gizi_lebih' => 'blue'
        ];

        return [
            'status' => $prediction->status_label,
            'color' => $statusColors[$prediction->prediksi_status] ?? 'gray',
            'date' => $latestMeasurement->tanggal_pengukuran,
            'priority' => $prediction->prioritas
        ];
    }

    /**
     * Check if balita is high risk
     */
    public function isHighRisk()
    {
        $latestMeasurement = $this->latestPengukuran;
        
        return $latestMeasurement && 
               $latestMeasurement->prediksiGizi && 
               ($latestMeasurement->prediksiGizi->prediksi_status === 'stunting' || 
                $latestMeasurement->prediksiGizi->prioritas === 'tinggi');
    }

    /**
     * Get growth trajectory (improvement/decline)
     */
    public function getGrowthTrend()
    {
        $measurements = $this->pengukuran()
                            ->with('prediksiGizi')
                            ->orderBy('tanggal_pengukuran', 'desc')
                            ->limit(2)
                            ->get();

        if ($measurements->count() < 2) {
            return 'insufficient_data';
        }

        $latest = $measurements->first();
        $previous = $measurements->last();

        if (!$latest->prediksiGizi || !$previous->prediksiGizi) {
            return 'no_prediction';
        }

        $statusPriority = [
            'normal' => 4,
            'gizi_lebih' => 3,
            'berisiko_stunting' => 2,
            'stunting' => 1
        ];

        $latestPriority = $statusPriority[$latest->prediksiGizi->prediksi_status] ?? 0;
        $previousPriority = $statusPriority[$previous->prediksiGizi->prediksi_status] ?? 0;

        if ($latestPriority > $previousPriority) {
            return 'improving';
        } elseif ($latestPriority < $previousPriority) {
            return 'declining';
        }
        
        return 'stable';
    }

    /**
     * Scope for filtering by area
     */
    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    /**
     * Scope for filtering by posyandu
     */
    public function scopeByPosyandu($query, $posyandu)
    {
        return $query->where('posyandu', $posyandu);
    }

    /**
     * Scope for filtering by desa
     */
    public function scopeByDesa($query, $desa)
    {
        return $query->where('desa', $desa);
    }

    /**
     * Scope for filtering by gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('jenis_kelamin', $gender);
    }

    /**
     * Scope for filtering by age range (in months)
     */
    public function scopeByAgeRange($query, $minMonths, $maxMonths)
    {
        $minDate = Carbon::now()->subMonths($maxMonths);
        $maxDate = Carbon::now()->subMonths($minMonths);
        
        return $query->whereBetween('tanggal_lahir', [$minDate, $maxDate]);
    }

    /**
     * Scope for balita with recent measurements
     */
    public function scopeWithRecentMeasurements($query, $days = 30)
    {
        return $query->whereHas('pengukuran', function($q) use ($days) {
            $q->where('tanggal_pengukuran', '>=', Carbon::now()->subDays($days));
        });
    }

    /**
     * Scope for high-risk balita
     */
    public function scopeHighRisk($query)
    {
        return $query->whereHas('latestPengukuran.prediksiGizi', function($q) {
            $q->where('prediksi_status', 'stunting')
              ->orWhere('prioritas', 'tinggi');
        });
    }

    /**
     * Scope for balita needing follow-up
     */
    public function scopeNeedingFollowUp($query, $days = 30)
    {
        return $query->whereDoesntHave('pengukuran', function($q) use ($days) {
            $q->where('tanggal_pengukuran', '>=', Carbon::now()->subDays($days));
        })->orWhereHas('latestPengukuran.prediksiGizi', function($q) {
            $q->whereIn('prediksi_status', ['stunting', 'berisiko_stunting'])
              ->where('created_at', '<=', Carbon::now()->subWeeks(2));
        });
    }

    /**
     * Get measurement count
     */
    public function getMeasurementCountAttribute()
    {
        return $this->pengukuran()->count();
    }

    /**
     * Check if balita needs urgent attention
     */
    public function needsUrgentAttention()
    {
        $latestMeasurement = $this->latestPengukuran;
        
        if (!$latestMeasurement || !$latestMeasurement->prediksiGizi) {
            // No measurement for more than 3 months
            return !$this->pengukuran()->where('tanggal_pengukuran', '>=', Carbon::now()->subMonths(3))->exists();
        }

        $prediction = $latestMeasurement->prediksiGizi;
        
        // Urgent if stunting or high priority
        if ($prediction->prediksi_status === 'stunting' || $prediction->prioritas === 'tinggi') {
            return true;
        }

        // Urgent if at risk and no follow-up for 2 weeks
        if ($prediction->prediksi_status === 'berisiko_stunting' && 
            $latestMeasurement->tanggal_pengukuran < Carbon::now()->subWeeks(2)) {
            return true;
        }

        return false;
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // When deleting balita, also delete all measurements and predictions
        static::deleting(function ($balita) {
            foreach ($balita->pengukuran as $pengukuran) {
                if ($pengukuran->prediksiGizi) {
                    $pengukuran->prediksiGizi->delete();
                }
                $pengukuran->delete();
            }
        });
    }

    // Relationship with master data
    public function masterPosyandu()
    {
        return $this->belongsTo(MasterPosyandu::class, 'master_posyandu_id');
    }

    public function masterDesa()
    {
        return $this->belongsTo(MasterDesa::class, 'master_desa_id');
    }
}