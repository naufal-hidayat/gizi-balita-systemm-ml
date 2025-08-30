<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';

    protected $fillable = [
        'no_kk',
        'nik_kepala_keluarga', 
        'nama_kepala_keluarga',
        'dusun_kampung_blok',
        'rt',
        'rw',
        'kelurahan_desa',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'kode_pos',
        'area',
        'master_posyandu_id',
        'master_desa_id',
        'user_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function anggotaKeluarga()
    {
        return $this->hasMany(AnggotaKeluarga::class);
    }

    public function balita()
    {
        return $this->hasManyThrough(Balita::class, AnggotaKeluarga::class);
    }

    public function kepalaKeluarga()
    {
        return $this->hasOne(AnggotaKeluarga::class)->where('hubungan_keluarga', 'kepala_keluarga');
    }

    public function ayah()
    {
        return $this->hasMany(AnggotaKeluarga::class)->whereIn('hubungan_keluarga', ['kepala_keluarga'])->where('jenis_kelamin', 'L');
    }

    public function ibu()
    {
        return $this->hasMany(AnggotaKeluarga::class)->whereIn('hubungan_keluarga', ['istri', 'kepala_keluarga'])->where('jenis_kelamin', 'P');
    }

    public function anak()
    {
        return $this->hasMany(AnggotaKeluarga::class)->where('hubungan_keluarga', 'anak');
    }

    public function balitaAktif()
    {
        return $this->anggotaKeluarga()->where('is_balita', true)->where('is_active', true);
    }

    public function masterPosyandu()
    {
        return $this->belongsTo(MasterPosyandu::class);
    }

    public function masterDesa()
    {
        return $this->belongsTo(MasterDesa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getAlamatLengkapAttribute()
    {
        return trim(implode(', ', array_filter([
            $this->dusun_kampung_blok,
            "RT {$this->rt}/RW {$this->rw}",
            $this->kelurahan_desa,
            $this->kecamatan,
            $this->kabupaten_kota,
            $this->provinsi
        ])));
    }

    public function getJumlahAnggotaAttribute()
    {
        return $this->anggotaKeluarga()->where('is_active', true)->count();
    }

    public function getJumlahBalitaAttribute()
    {
        return $this->balitaAktif()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopeByPosyandu($query, $posyanduId)
    {
        return $query->where('master_posyandu_id', $posyanduId);
    }

    public function scopeHasBalita($query)
    {
        return $query->whereHas('balitaAktif');
    }
}