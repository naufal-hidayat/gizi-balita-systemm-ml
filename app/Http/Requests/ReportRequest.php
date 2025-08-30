<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'report_type' => 'required|in:monthly,yearly,custom',
            'period_start' => 'required_if:report_type,custom|date',
            'period_end' => 'required_if:report_type,custom|date|after:period_start',
            'month' => 'required_if:report_type,monthly|date_format:Y-m',
            'year' => 'required_if:report_type,yearly|digits:4|integer|min:2020|max:' . date('Y'),
            'format' => 'nullable|in:pdf,excel,json',
            'include_charts' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'report_type.required' => 'Jenis laporan wajib dipilih',
            'period_start.required_if' => 'Tanggal mulai wajib diisi untuk laporan custom',
            'period_end.required_if' => 'Tanggal akhir wajib diisi untuk laporan custom',
            'period_end.after' => 'Tanggal akhir harus setelah tanggal mulai',
            'month.required_if' => 'Bulan wajib dipilih untuk laporan bulanan',
            'year.required_if' => 'Tahun wajib dipilih untuk laporan tahunan',
            'year.min' => 'Tahun minimal 2020',
            'year.max' => 'Tahun maksimal ' . date('Y'),
        ];
    }
}