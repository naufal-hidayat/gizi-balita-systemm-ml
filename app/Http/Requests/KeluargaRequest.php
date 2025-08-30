<?php
// app/Http/Requests/KeluargaRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeluargaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            // Data KK
            'no_kk' => 'required|string|size:16|unique:keluarga,no_kk',
            'nik_kepala_keluarga' => 'required|string|size:16|unique:keluarga,nik_kepala_keluarga',
            
            // Data Kepala Keluarga
            'nama_kepala_keluarga' => 'required|string|max:255',
            'jenis_kelamin_kepala_keluarga' => 'required|in:L,P',
            'tanggal_lahir_kepala_keluarga' => 'required|date|before:today',
            'pekerjaan_kepala_keluarga' => 'nullable|string|max:255',
            'pendidikan_kepala_keluarga' => 'nullable|in:tidak_sekolah,tidak_tamat_sd,sd,smp,sma,diploma,sarjana,magister,doktor',
            
            // Alamat
            'dusun_kampung_blok' => 'required|string|max:255',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'kelurahan_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten_kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|size:5',
        ];

        // Jika admin, area dan master data wajib diisi
        if (auth()->user()->isAdmin()) {
            $rules['area'] = 'required|in:timur,barat,utara,selatan';
            $rules['master_posyandu_id'] = 'required|exists:master_posyandu,id';
            $rules['master_desa_id'] = 'required|exists:master_desa,id';
        }

        // If updating, ignore current record for unique validation
        if ($this->route('keluarga')) {
            $rules['no_kk'] = 'required|string|size:16|unique:keluarga,no_kk,' . $this->route('keluarga')->id;
            $rules['nik_kepala_keluarga'] = 'required|string|size:16|unique:keluarga,nik_kepala_keluarga,' . $this->route('keluarga')->id;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            // Data KK
            'no_kk.required' => 'No Kartu Keluarga wajib diisi',
            'no_kk.size' => 'No Kartu Keluarga harus 16 digit',
            'no_kk.unique' => 'No Kartu Keluarga sudah terdaftar',
            'nik_kepala_keluarga.required' => 'NIK Kepala Keluarga wajib diisi',
            'nik_kepala_keluarga.size' => 'NIK Kepala Keluarga harus 16 digit',
            'nik_kepala_keluarga.unique' => 'NIK Kepala Keluarga sudah terdaftar',
            
            // Data Kepala Keluarga
            'nama_kepala_keluarga.required' => 'Nama Kepala Keluarga wajib diisi',
            'nama_kepala_keluarga.max' => 'Nama Kepala Keluarga maksimal 255 karakter',
            'jenis_kelamin_kepala_keluarga.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin_kepala_keluarga.in' => 'Jenis kelamin harus L atau P',
            'tanggal_lahir_kepala_keluarga.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir_kepala_keluarga.before' => 'Tanggal lahir harus sebelum hari ini',
            'pekerjaan_kepala_keluarga.max' => 'Pekerjaan maksimal 255 karakter',
            'pendidikan_kepala_keluarga.in' => 'Pendidikan tidak valid',
            
            // Alamat
            'dusun_kampung_blok.required' => 'Dusun/Kampung/Blok wajib diisi',
            'dusun_kampung_blok.max' => 'Dusun/Kampung/Blok maksimal 255 karakter',
            'rt.required' => 'RT wajib diisi',
            'rt.max' => 'RT maksimal 3 karakter',
            'rw.required' => 'RW wajib diisi',
            'rw.max' => 'RW maksimal 3 karakter',
            'kelurahan_desa.required' => 'Kelurahan/Desa wajib diisi',
            'kelurahan_desa.max' => 'Kelurahan/Desa maksimal 255 karakter',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'kecamatan.max' => 'Kecamatan maksimal 255 karakter',
            'kabupaten_kota.required' => 'Kabupaten/Kota wajib diisi',
            'kabupaten_kota.max' => 'Kabupaten/Kota maksimal 255 karakter',
            'provinsi.required' => 'Provinsi wajib diisi',
            'provinsi.max' => 'Provinsi maksimal 255 karakter',
            'kode_pos.size' => 'Kode pos harus 5 digit',
            
            // Area dan Posyandu
            'area.required' => 'Area wajib dipilih',
            'area.in' => 'Area harus timur, barat, utara, atau selatan',
            'master_posyandu_id.required' => 'Posyandu wajib dipilih',
            'master_posyandu_id.exists' => 'Posyandu tidak valid',
            'master_desa_id.required' => 'Desa wajib dipilih',
            'master_desa_id.exists' => 'Desa tidak valid'
        ];
    }

    protected function prepareForValidation()
    {
        // Format No KK - hanya angka
        if ($this->no_kk) {
            $this->merge([
                'no_kk' => preg_replace('/[^0-9]/', '', $this->no_kk)
            ]);
        }
        
        // Format NIK - hanya angka
        if ($this->nik_kepala_keluarga) {
            $this->merge([
                'nik_kepala_keluarga' => preg_replace('/[^0-9]/', '', $this->nik_kepala_keluarga)
            ]);
        }
        
        // Format nama - capitalize each word
        if ($this->nama_kepala_keluarga) {
            $this->merge([
                'nama_kepala_keluarga' => ucwords(strtolower($this->nama_kepala_keluarga))
            ]);
        }
        
        // Format alamat - capitalize each word
        $alamatFields = ['dusun_kampung_blok', 'kelurahan_desa', 'kecamatan', 'kabupaten_kota', 'provinsi'];
        
        foreach ($alamatFields as $field) {
            if ($this->{$field}) {
                $this->merge([
                    $field => ucwords(strtolower($this->{$field}))
                ]);
            }
        }
        
        // Format RT/RW - pad dengan 0
        if ($this->rt) {
            $this->merge([
                'rt' => str_pad($this->rt, 3, '0', STR_PAD_LEFT)
            ]);
        }
        
        if ($this->rw) {
            $this->merge([
                'rw' => str_pad($this->rw, 3, '0', STR_PAD_LEFT)
            ]);
        }
        
        // Format kode pos - hanya angka
        if ($this->kode_pos) {
            $this->merge([
                'kode_pos' => preg_replace('/[^0-9]/', '', $this->kode_pos)
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi umur kepala keluarga minimal 17 tahun
            if ($this->tanggal_lahir_kepala_keluarga) {
                $tanggalLahir = \Carbon\Carbon::parse($this->tanggal_lahir_kepala_keluarga);
                $umur = $tanggalLahir->diffInYears(now());
                
                if ($umur < 17) {
                    $validator->errors()->add('tanggal_lahir_kepala_keluarga', 'Kepala keluarga minimal berusia 17 tahun');
                }
            }
            
            // Validasi konsistensi area dengan posyandu (untuk admin)
            if (auth()->user()->isAdmin() && $this->area && $this->master_posyandu_id) {
                $posyandu = \App\Models\MasterPosyandu::find($this->master_posyandu_id);
                
                if ($posyandu && $posyandu->area !== $this->area) {
                    $validator->errors()->add('master_posyandu_id', 'Posyandu tidak sesuai dengan area yang dipilih');
                }
            }
            
            // Validasi konsistensi posyandu dengan desa (untuk admin)
            if (auth()->user()->isAdmin() && $this->master_posyandu_id && $this->master_desa_id) {
                $desa = \App\Models\MasterDesa::find($this->master_desa_id);
                
                if ($desa && $desa->master_posyandu_id != $this->master_posyandu_id) {
                    $validator->errors()->add('master_desa_id', 'Desa tidak sesuai dengan posyandu yang dipilih');
                }
            }
        });
    }
}