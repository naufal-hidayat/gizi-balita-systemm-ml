<!-- @extends('layouts.app')

@section('title', 'Prediksi Status Gizi')
@section('page-title', 'Form Prediksi Status Gizi (Random Forest)')

@section('content')
<form method="POST" action="{{ route('prediksi.submit') }}" class="max-w-xl bg-white p-6 rounded shadow space-y-4">
    @csrf

    <div>
        <label class="block font-semibold">Nama Balita</label>
        <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label>Berat Badan (kg)</label>
            <input type="number" name="berat" step="0.01" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Tinggi Badan (cm)</label>
            <input type="number" name="tinggi" step="0.01" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Lingkar Kepala (cm)</label>
            <input type="number" name="lingkar_kepala" step="0.01" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Lingkar Lengan (cm)</label>
            <input type="number" name="lingkar_lengan" step="0.01" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label>Usia (bulan)</label>
            <input type="number" name="usia" class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mt-4">
        @foreach(['asi_eksklusif' => 'ASI Eksklusif', 'imunisasi_lengkap' => 'Imunisasi Lengkap', 'riwayat_penyakit' => 'Riwayat Penyakit', 'akses_air_bersih' => 'Akses Air Bersih', 'sanitasi_layak' => 'Sanitasi Layak'] as $name => $label)
        <div>
            <label>{{ $label }}</label>
            <select name="{{ $name }}" class="w-full border rounded px-3 py-2" required>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
            </select>
        </div>
        @endforeach
    </div>

    <button type="submit"
        class="mt-4 w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition">
        Prediksi Status Gizi
    </button>
</form>
<!-- <a href="{{ route('prediksi.riwayat') }}" class="text-blue-600 hover:underline">📄 Lihat Riwayat Prediksi</a> -->

<!-- @if(session('hasil'))
@php
$isStunting = strtolower(session('hasil')) === 'stunting';
$bg = $isStunting ? 'bg-red-50 border-red-300 text-red-800' : 'bg-green-50 border-green-300 text-green-800';
@endphp
<div class="mt-6 p-4 border rounded shadow {{ $bg }}">
    <strong>Hasil Prediksi:</strong> {{ session('hasil') }}
</div>
@endif

@endsection --> -->