<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AnggotaKeluarga extends Model
{
    use HasFactory;

    protected $table = 'anggota_keluarga';

    protected $fillable = [
        'keluarga_id',
        'nik',
        'nama_lengkap',
        'tanggal_lahir',
        'jenis_kelamin',
        'hubungan_keluarga',
        'pekerjaan',
        'pendidikan_terakhir',
        'is_balita',
        'is_active'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_balita' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function balita()
    {
        return $this->hasOne(Balita::class);
    }

    // Accessors
    public function getUmurAttribute()
    {
        if (!$this->tanggal_lahir) return 0;
        return $this->tanggal_lahir->diffInYears(now());
    }

    public function getUmurBulanAttribute()
    {
        if (!$this->tanggal_lahir) return 0;
        return $this->tanggal_lahir->diffInMonths(now());
    }

    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getHubunganKeluargaLabelAttribute()
    {
        $labels = [
            'kepala_keluarga' => 'Kepala Keluarga',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orangtua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili_lain' => 'Famili Lain',
            'pembantu' => 'Pembantu',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$this->hubungan_keluarga] ?? ucfirst($this->hubungan_keluarga);
    }

    public function getPendidikanLabelAttribute()
    {
        $labels = [
            'tidak_sekolah' => 'Tidak Sekolah',
            'tidak_tamat_sd' => 'Tidak Tamat SD',
            'sd' => 'SD',
            'smp' => 'SMP',
            'sma' => 'SMA',
            'diploma' => 'Diploma',
            'sarjana' => 'Sarjana',
            'magister' => 'Magister',
            'doktor' => 'Doktor'
        ];

        return $labels[$this->pendidikan_terakhir] ?? '-';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBalita($query)
    {
        return $query->where('is_balita', true);
    }

    public function scopeOrangTua($query)
    {
        return $query->whereIn('hubungan_keluarga', ['kepala_keluarga', 'istri']);
    }

    public function scopeAnak($query)
    {
        return $query->where('hubungan_keluarga', 'anak');
    }

    // Methods
    public function updateStatusBalita()
    {
        $umurBulan = $this->umur_bulan;
        $this->update(['is_balita' => $umurBulan <= 60]); // 0-5 tahun
    }

    public function isEligibleForBalita()
    {
        return $this->umur_bulan <= 60 && $this->hubungan_keluarga === 'anak';
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($anggota) {
            // Auto update status balita berdasarkan umur
            if ($anggota->tanggal_lahir) {
                $umurBulan = $anggota->tanggal_lahir->diffInMonths(now());
                $anggota->is_balita = ($umurBulan <= 60);
            }
        });
    }
}