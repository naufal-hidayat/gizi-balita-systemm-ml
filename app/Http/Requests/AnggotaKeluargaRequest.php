<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnggotaKeluargaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'keluarga_id' => 'required|exists:keluarga,id',
            'nik' => 'required|string|size:16|unique:anggota_keluarga,nik',
            'nama_lengkap' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'hubungan_keluarga' => 'required|in:kepala_keluarga,istri,anak,menantu,cucu,orangtua,mertua,famili_lain,lainnya',
            'pekerjaan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|in:tidak_sekolah,tidak_tamat_sd,sd,smp,sma,diploma,sarjana,magister,doktor',
        ];

        // If updating, ignore current record for unique validation
        if ($this->route('anggota')) {
            $rules['nik'] = 'required|string|size:16|unique:anggota_keluarga,nik,' . $this->route('anggota')->id;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'keluarga_id.required' => 'Keluarga wajib dipilih',
            'keluarga_id.exists' => 'Keluarga tidak valid',
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'hubungan_keluarga.required' => 'Hubungan keluarga wajib dipilih',
            'hubungan_keluarga.in' => 'Hubungan keluarga tidak valid',
            'pekerjaan.max' => 'Pekerjaan maksimal 255 karakter',
            'pendidikan_terakhir.in' => 'Pendidikan terakhir tidak valid'
        ];
    }

    protected function prepareForValidation()
    {
        // Format NIK - hanya angka
        if ($this->nik) {
            $this->merge([
                'nik' => preg_replace('/[^0-9]/', '', $this->nik)
            ]);
        }
        
        // Format nama - capitalize each word
        if ($this->nama_lengkap) {
            $this->merge([
                'nama_lengkap' => ucwords(strtolower($this->nama_lengkap))
            ]);
        }
        
        // Format pekerjaan - capitalize each word
        if ($this->pekerjaan) {
            $this->merge([
                'pekerjaan' => ucwords(strtolower($this->pekerjaan))
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi khusus untuk kepala keluarga
            if ($this->hubungan_keluarga === 'kepala_keluarga') {
                // Check if this family already has a kepala keluarga (except when updating same record)
                $existingKepala = \App\Models\AnggotaKeluarga::where('keluarga_id', $this->keluarga_id)
                                                           ->where('hubungan_keluarga', 'kepala_keluarga')
                                                           ->where('is_active', true);
                
                if ($this->route('anggota')) {
                    $existingKepala->where('id', '!=', $this->route('anggota')->id);
                }
                
                if ($existingKepala->exists()) {
                    $validator->errors()->add('hubungan_keluarga', 'Keluarga ini sudah memiliki kepala keluarga');
                }
                
                // Kepala keluarga harus minimal 17 tahun
                if ($this->tanggal_lahir) {
                    $tanggalLahir = \Carbon\Carbon::parse($this->tanggal_lahir);
                    $umur = $tanggalLahir->diffInYears(now());
                    
                    if ($umur < 17) {
                        $validator->errors()->add('tanggal_lahir', 'Kepala keluarga minimal berusia 17 tahun');
                    }
                }
            }
            
            // Validasi anak maksimal 60 bulan untuk auto-balita
            if ($this->hubungan_keluarga === 'anak' && $this->tanggal_lahir) {
                $tanggalLahir = \Carbon\Carbon::parse($this->tanggal_lahir);
                $umurBulan = $tanggalLahir->diffInMonths(now());
                
                if ($umurBulan > 60) {
                    // Just a warning, not an error
                    // Could add info message here
                }
            }
        });
    }
}