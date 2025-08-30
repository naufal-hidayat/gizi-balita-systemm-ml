<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BalitaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nik_balita' => 'required|string|size:16|unique:balita,nik_balita',
            'nama_balita' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_orang_tua' => 'required|string|max:255',
            
            // Alamat detail - semua required
            'rt' => 'required|string|max:3|regex:/^[0-9]{1,3}$/',
            'rw' => 'required|string|max:3|regex:/^[0-9]{1,3}$/',
            'dusun' => 'nullable|string|max:100',
            'desa_kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kabupaten' => 'required|string|max:100',
            
            // Alamat lengkap optional (untuk backward compatibility)
            'alamat_lengkap' => 'nullable|string|max:500',
        ];

        // Jika admin, area dan master data wajib diisi
        if (auth()->user()->isAdmin()) {
            $rules['area'] = 'required|in:timur,barat,utara,selatan';
            $rules['master_posyandu_id'] = 'required|exists:master_posyandu,id';
            $rules['master_desa_id'] = 'required|exists:master_desa,id';
        }

        // If updating, ignore current record for unique validation
        if ($this->route('balita')) {
            $rules['nik_balita'] = 'required|string|size:16|unique:balita,nik_balita,' . $this->route('balita')->id;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nik_balita.required' => 'NIK Balita wajib diisi',
            'nik_balita.size' => 'NIK Balita harus 16 digit',
            'nik_balita.unique' => 'NIK Balita sudah terdaftar',
            'nama_balita.required' => 'Nama Balita wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'nama_orang_tua.required' => 'Nama orang tua wajib diisi',
            
            // Validasi alamat detail
            'rt.required' => 'RT wajib diisi',
            'rt.regex' => 'RT harus berupa angka 1-3 digit',
            'rt.max' => 'RT maksimal 3 karakter',
            'rw.required' => 'RW wajib diisi',
            'rw.regex' => 'RW harus berupa angka 1-3 digit',
            'rw.max' => 'RW maksimal 3 karakter',
            'dusun.max' => 'Nama dusun maksimal 100 karakter',
            'desa_kelurahan.required' => 'Desa/Kelurahan wajib diisi',
            'desa_kelurahan.max' => 'Nama desa/kelurahan maksimal 100 karakter',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'kecamatan.max' => 'Nama kecamatan maksimal 100 karakter',
            'kabupaten.required' => 'Kabupaten/Kota wajib diisi',
            'kabupaten.max' => 'Nama kabupaten/kota maksimal 100 karakter',
            
            'area.required' => 'Area wajib dipilih',
            'area.in' => 'Area harus timur, barat, utara, atau selatan',
            'master_posyandu_id.required' => 'Posyandu wajib dipilih',
            'master_posyandu_id.exists' => 'Posyandu tidak valid',
            'master_desa_id.required' => 'Desa wajib dipilih',
            'master_desa_id.exists' => 'Desa tidak valid',
        ];
    }

    protected function prepareForValidation()
    {
        // Format NIK - hanya angka
        if ($this->nik_balita) {
            $this->merge([
                'nik_balita' => preg_replace('/[^0-9]/', '', $this->nik_balita)
            ]);
        }
        
        // Format nama - capitalize each word
        if ($this->nama_balita) {
            $this->merge([
                'nama_balita' => ucwords(strtolower(trim($this->nama_balita)))
            ]);
        }
        
        if ($this->nama_orang_tua) {
            $this->merge([
                'nama_orang_tua' => ucwords(strtolower(trim($this->nama_orang_tua)))
            ]);
        }
        
        // Format RT/RW - remove leading zeros tapi keep as string
        if ($this->rt) {
            $this->merge([
                'rt' => str_pad(ltrim($this->rt, '0') ?: '0', 1, '0', STR_PAD_LEFT)
            ]);
        }
        
        if ($this->rw) {
            $this->merge([
                'rw' => str_pad(ltrim($this->rw, '0') ?: '0', 1, '0', STR_PAD_LEFT)
            ]);
        }
        
        // Format alamat components - capitalize each word
        $addressFields = ['dusun', 'desa_kelurahan', 'kecamatan', 'kabupaten'];
        foreach ($addressFields as $field) {
            if ($this->$field) {
                $this->merge([
                    $field => ucwords(strtolower(trim($this->$field)))
                ]);
            }
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'nik_balita' => 'NIK Balita',
            'nama_balita' => 'Nama Balita',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'nama_orang_tua' => 'Nama Orang Tua',
            'rt' => 'RT',
            'rw' => 'RW',
            'dusun' => 'Dusun',
            'desa_kelurahan' => 'Desa/Kelurahan',
            'kecamatan' => 'Kecamatan',
            'kabupaten' => 'Kabupaten/Kota',
            'area' => 'Area',
            'master_posyandu_id' => 'Posyandu',
            'master_desa_id' => 'Desa',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi umur balita (maksimal 5 tahun)
            if ($this->tanggal_lahir) {
                $birthDate = \Carbon\Carbon::parse($this->tanggal_lahir);
                $ageInMonths = $birthDate->diffInMonths(now());
                
                if ($ageInMonths > 60) { // 5 tahun = 60 bulan
                    $validator->errors()->add('tanggal_lahir', 'Balita maksimal berusia 5 tahun (60 bulan)');
                }
            }
            
            // Validasi RT tidak boleh 000
            if ($this->rt && $this->rt === '000') {
                $validator->errors()->add('rt', 'RT tidak boleh 000');
            }
            
            // Validasi RW tidak boleh 000
            if ($this->rw && $this->rw === '000') {
                $validator->errors()->add('rw', 'RW tidak boleh 000');
            }
            
            // Validasi kombinasi RT/RW yang masuk akal
            if ($this->rt && $this->rw) {
                if (intval($this->rt) > intval($this->rw) * 10) {
                    $validator->errors()->add('rt', 'Nomor RT tidak sesuai dengan RW yang dipilih');
                }
            }
        });
    }
}