<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataLatih extends Model
{
    protected $table = 'data_latih';

    protected $fillable = [
        'nama',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'umur',
        'asi_eksklusif',
        'imunisasi_lengkap',
        'riwayat_penyakit',
        'akses_air_bersih',
        'sanitasi_layak',
        'status_gizi'
    ];
}
