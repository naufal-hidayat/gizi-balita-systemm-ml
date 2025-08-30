<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class PengukuranRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'balita_id' => 'required|exists:balita,id',
            'tanggal_pengukuran' => 'required|date|before_or_equal:today',
            'umur_bulan' => 'required|integer|min:0|max:60',
            'berat_badan' => 'required|numeric|min:1|max:50',
            'tinggi_badan' => 'required|numeric|min:30|max:150',
            'lingkar_kepala' => 'nullable|numeric|min:25|max:60',
            'lingkar_lengan' => 'nullable|numeric|min:8|max:25',
            'asi_eksklusif' => 'required|in:ya,tidak',
            'imunisasi_lengkap' => 'required|in:ya,tidak,tidak_lengkap',
            'pendapatan_keluarga' => 'required|string|in:lt_600k,600k_1m,1m_2m,2m_5m,gt_5m',
            'pendidikan_ibu' => 'required|in:sd,smp,sma,diploma,sarjana',
            'akses_air_bersih' => 'required|in:ya,tidak',
            'sanitasi_layak' => 'required|in:ya,tidak',
            'jumlah_anggota_keluarga' => 'required|integer|min:1|max:20',
            'riwayat_penyakit' => 'nullable|string|max:1000'
        ];
    }

    public function messages()
    {
        return [
            'balita_id.required' => 'Balita wajib dipilih',
            'balita_id.exists' => 'Balita tidak ditemukan',
            'tanggal_pengukuran.required' => 'Tanggal pengukuran wajib diisi',
            'tanggal_pengukuran.date' => 'Format tanggal tidak valid',
            'tanggal_pengukuran.before_or_equal' => 'Tanggal pengukuran tidak boleh lebih dari hari ini',
            'umur_bulan.required' => 'Umur dalam bulan wajib diisi',
            'umur_bulan.integer' => 'Umur harus berupa angka bulat',
            'umur_bulan.min' => 'Umur minimal 0 bulan',
            'umur_bulan.max' => 'Umur maksimal 60 bulan (5 tahun)',
            'berat_badan.required' => 'Berat badan wajib diisi',
            'berat_badan.numeric' => 'Berat badan harus berupa angka',
            'berat_badan.min' => 'Berat badan minimal 1 kg',
            'berat_badan.max' => 'Berat badan maksimal 50 kg',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi',
            'tinggi_badan.numeric' => 'Tinggi badan harus berupa angka',
            'tinggi_badan.min' => 'Tinggi badan minimal 30 cm',
            'tinggi_badan.max' => 'Tinggi badan maksimal 150 cm',
            'lingkar_kepala.numeric' => 'Lingkar kepala harus berupa angka',
            'lingkar_kepala.min' => 'Lingkar kepala minimal 25 cm',
            'lingkar_kepala.max' => 'Lingkar kepala maksimal 60 cm',
            'lingkar_lengan.numeric' => 'Lingkar lengan harus berupa angka',
            'lingkar_lengan.min' => 'Lingkar lengan minimal 8 cm',
            'lingkar_lengan.max' => 'Lingkar lengan maksimal 25 cm',
            'asi_eksklusif.required' => 'Status ASI eksklusif wajib dipilih',
            'asi_eksklusif.in' => 'Status ASI eksklusif tidak valid',
            'imunisasi_lengkap.required' => 'Status imunisasi wajib dipilih',
            'imunisasi_lengkap.in' => 'Status imunisasi tidak valid',
            'pendapatan_keluarga.required' => 'Pendapatan keluarga wajib diisi',
            'pendapatan_keluarga.integer' => 'Pendapatan keluarga harus berupa angka bulat',
            'pendapatan_keluarga.min' => 'Pendapatan keluarga minimal 0',
            'pendidikan_ibu.required' => 'Pendidikan ibu wajib dipilih',
            'pendidikan_ibu.in' => 'Pendidikan ibu tidak valid',
            'akses_air_bersih.required' => 'Status akses air bersih wajib dipilih',
            'akses_air_bersih.in' => 'Status akses air bersih tidak valid',
            'sanitasi_layak.required' => 'Status sanitasi wajib dipilih',
            'sanitasi_layak.in' => 'Status sanitasi tidak valid',
            'jumlah_anggota_keluarga.required' => 'Jumlah anggota keluarga wajib diisi',
            'jumlah_anggota_keluarga.integer' => 'Jumlah anggota keluarga harus berupa angka bulat',
            'jumlah_anggota_keluarga.min' => 'Jumlah anggota keluarga minimal 1',
            'jumlah_anggota_keluarga.max' => 'Jumlah anggota keluarga maksimal 20',
            'riwayat_penyakit.string' => 'Riwayat penyakit harus berupa teks',
            'riwayat_penyakit.max' => 'Riwayat penyakit maksimal 1000 karakter'
        ];
    }

    protected function prepareForValidation()
    {
        // Auto calculate age in months if not provided or if it's 0
        if ((!$this->umur_bulan || $this->umur_bulan == 0) && $this->balita_id && $this->tanggal_pengukuran) {
            try {
                $balita = \App\Models\Balita::find($this->balita_id);
                if ($balita && $balita->tanggal_lahir) {
                    $tanggalLahir = Carbon::parse($balita->tanggal_lahir);
                    $tanggalUkur = Carbon::parse($this->tanggal_pengukuran);
                    
                    // Calculate age in months
                    $umurBulan = $tanggalLahir->diffInMonths($tanggalUkur);
                    
                    // Ensure age is within valid range
                    $umurBulan = max(0, min(60, $umurBulan));
                    
                    $this->merge([
                        'umur_bulan' => $umurBulan
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Error calculating age: ' . $e->getMessage());
                // If calculation fails, keep the original value or set to 0
                if (!$this->umur_bulan) {
                    $this->merge(['umur_bulan' => 0]);
                }
            }
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'balita_id' => 'balita',
            'tanggal_pengukuran' => 'tanggal pengukuran',
            'umur_bulan' => 'umur (bulan)',
            'berat_badan' => 'berat badan',
            'tinggi_badan' => 'tinggi badan',
            'lingkar_kepala' => 'lingkar kepala',
            'lingkar_lengan' => 'lingkar lengan',
            'asi_eksklusif' => 'ASI eksklusif',
            'imunisasi_lengkap' => 'status imunisasi',
            'pendapatan_keluarga' => 'pendapatan keluarga',
            'pendidikan_ibu' => 'pendidikan ibu',
            'akses_air_bersih' => 'akses air bersih',
            'sanitasi_layak' => 'sanitasi layak',
            'jumlah_anggota_keluarga' => 'jumlah anggota keluarga',
            'riwayat_penyakit' => 'riwayat penyakit'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation: Check if measurement date is after birth date
            if ($this->balita_id && $this->tanggal_pengukuran) {
                try {
                    $balita = \App\Models\Balita::find($this->balita_id);
                    if ($balita && $balita->tanggal_lahir) {
                        $tanggalLahir = Carbon::parse($balita->tanggal_lahir);
                        $tanggalUkur = Carbon::parse($this->tanggal_pengukuran);
                        
                        if ($tanggalUkur->lt($tanggalLahir)) {
                            $validator->errors()->add('tanggal_pengukuran', 'Tanggal pengukuran tidak boleh sebelum tanggal lahir balita');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error validating measurement date: ' . $e->getMessage());
                }
            }

            // Validate weight and height ratio (basic sanity check)
            if ($this->berat_badan && $this->tinggi_badan) {
                $bmi = $this->berat_badan / (($this->tinggi_badan / 100) ** 2);
                if ($bmi < 5 || $bmi > 40) {
                    $validator->errors()->add('berat_badan', 'Rasio berat badan dan tinggi badan tampak tidak normal. Mohon periksa kembali.');
                }
            }

            // Validate age consistency
            if ($this->umur_bulan && $this->balita_id && $this->tanggal_pengukuran) {
                try {
                    $balita = \App\Models\Balita::find($this->balita_id);
                    if ($balita && $balita->tanggal_lahir) {
                        $calculatedAge = Carbon::parse($balita->tanggal_lahir)->diffInMonths(Carbon::parse($this->tanggal_pengukuran));
                        
                        // Allow small discrepancy (±1 month) for manual adjustments
                        if (abs($this->umur_bulan - $calculatedAge) > 1) {
                            $validator->errors()->add('umur_bulan', "Umur yang diinput ({$this->umur_bulan} bulan) tidak sesuai dengan perhitungan dari tanggal lahir ({$calculatedAge} bulan)");
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error validating age consistency: ' . $e->getMessage());
                }
            }
        });
    }
}