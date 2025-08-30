@extends('layouts.app')

@section('title', 'Detail Pengukuran - ' . $pengukuran->balita->nama_balita)
@section('page-title', 'Detail Pengukuran')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
    <svg class="w-5 h-5 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('pengukuran.index') }}" class="text-gray-500 hover:text-gray-700">Pengukuran</a>
    <svg class="w-5 h-5 mx-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
</li>
<li class="inline-flex items-center">
    <span class="text-gray-500">Detail Pengukuran</span>
</li>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header with Balita Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-start">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $pengukuran->balita->nama_balita }}</h1>
                    <p class="text-gray-600">NIK: {{ $pengukuran->balita->nik_balita }}</p>
                    <p class="text-sm text-gray-500">Diukur pada {{ $pengukuran->formatted_date }} • {{ $pengukuran->formatted_age }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('pengukuran.edit', $pengukuran) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                </a>
                <a href="{{ route('pengukuran.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    @if($pengukuran->prediksiGizi)
    <!-- Prediction Results -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Hasil Prediksi Status Gizi (Fuzzy-AHP)
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Prediction -->
            <div class="lg:col-span-2">
                <div class="border rounded-lg p-4 
                    @if($pengukuran->prediksiGizi->prediksi_status === 'stunting') border-red-200 bg-red-50
                    @elseif($pengukuran->prediksiGizi->prediksi_status === 'berisiko_stunting') border-yellow-200 bg-yellow-50
                    @elseif($pengukuran->prediksiGizi->prediksi_status === 'normal') border-green-200 bg-green-50
                    @else border-blue-200 bg-blue-50 @endif">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold 
                            @if($pengukuran->prediksiGizi->prediksi_status === 'stunting') text-red-800
                            @elseif($pengukuran->prediksiGizi->prediksi_status === 'berisiko_stunting') text-yellow-800
                            @elseif($pengukuran->prediksiGizi->prediksi_status === 'normal') text-green-800
                            @else text-blue-800 @endif">
                            {{ $pengukuran->prediksiGizi->status_label }}
                        </h3>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($pengukuran->prediksiGizi->prioritas === 'tinggi') bg-red-100 text-red-800
                            @elseif($pengukuran->prediksiGizi->prioritas === 'sedang') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            Prioritas {{ ucfirst($pengukuran->prediksiGizi->prioritas) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Tingkat Kepercayaan</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->prediksiGizi->confidence_level }}%</p>
                            <p class="text-xs text-gray-500">{{ $pengukuran->prediksiGizi->confidence_description }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Skor Akhir</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($pengukuran->prediksiGizi->final_score, 3) }}</p>
                            <p class="text-xs text-gray-500">Hasil agregasi Fuzzy-AHP</p>
                        </div>
                    </div>

                    @if($pengukuran->prediksiGizi->rekomendasi)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Rekomendasi Tindakan:</h4>
                        <div class="text-sm text-gray-700 space-y-1">
                            @foreach(explode("\n", $pengukuran->prediksiGizi->rekomendasi) as $rekomendasi)
                            @if(trim($rekomendasi))
                            <p>{{ trim($rekomendasi) }}</p>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Z-Scores -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Indikator Antropometri (Z-Score)</h4>
                <div class="space-y-3">
                    <div class="border rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">BB/U</span>
                            <span class="text-sm font-bold {{ $pengukuran->prediksiGizi->zscore_bb_u < -2 ? 'text-red-600' : ($pengukuran->prediksiGizi->zscore_bb_u < -1 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($pengukuran->prediksiGizi->zscore_bb_u, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $pengukuran->prediksiGizi->bb_u_status_label }}</p>
                    </div>

                    <div class="border rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">TB/U</span>
                            <span class="text-sm font-bold {{ $pengukuran->prediksiGizi->zscore_tb_u < -2 ? 'text-red-600' : ($pengukuran->prediksiGizi->zscore_tb_u < -1 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($pengukuran->prediksiGizi->zscore_tb_u, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $pengukuran->prediksiGizi->tb_u_status_label }}</p>
                    </div>

                    <div class="border rounded-lg p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">BB/TB</span>
                            <span class="text-sm font-bold {{ $pengukuran->prediksiGizi->zscore_bb_tb < -2 ? 'text-red-600' : ($pengukuran->prediksiGizi->zscore_bb_tb < -1 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ number_format($pengukuran->prediksiGizi->zscore_bb_tb, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $pengukuran->prediksiGizi->bb_tb_status_label }}</p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600">
                        <strong>Interpretasi Z-Score:</strong><br>
                        ≥ -1: Normal<br>
                        -2 sampai -1: Berisiko<br>
                        < -2: Bermasalah
                            </p>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Prediction Available -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">Prediksi Belum Tersedia</h3>
                <p class="text-yellow-700">Sistem belum berhasil menghasilkan prediksi untuk data pengukuran ini.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Measurement Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Anthropometric Data -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Data Antropometri
            </h3>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Berat Badan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->berat_badan }} kg</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tinggi Badan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->tinggi_badan }} cm</p>
                    </div>
                </div>

                @if($pengukuran->bmi)
                <div>
                    <p class="text-sm text-gray-600">BMI</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->bmi }}</p>
                </div>
                @endif

                @if($pengukuran->lingkar_kepala)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Lingkar Kepala</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->lingkar_kepala }} cm</p>
                    </div>
                    @if($pengukuran->lingkar_lengan)
                    <div>
                        <p class="text-sm text-gray-600">Lingkar Lengan</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $pengukuran->lingkar_lengan }} cm</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Health Factors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Faktor Kesehatan
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">ASI Eksklusif</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $pengukuran->asi_eksklusif === 'ya' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $pengukuran->asi_eksklusif_label }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Status Imunisasi</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $pengukuran->imunisasi_lengkap === 'ya' ? 'bg-green-100 text-green-800' : ($pengukuran->imunisasi_lengkap === 'tidak_lengkap' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $pengukuran->imunisasi_label }}
                    </span>
                </div>

                @if($pengukuran->riwayat_penyakit)
                <div>
                    <span class="text-sm text-gray-600">Riwayat Penyakit</span>
                    <p class="text-sm text-gray-900 mt-1 p-2 bg-gray-50 rounded">{{ $pengukuran->riwayat_penyakit }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Socioeconomic Factors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Faktor Sosial-Ekonomi
            </h3>

            <div class="space-y-3">
                <div>
                    <span class="text-sm text-gray-600">Pendapatan Keluarga</span>
                    <p class="text-sm font-medium text-gray-900">{{ $pengukuran->formatted_income }}</p>
                    <p class="text-xs text-gray-500">{{ $pengukuran->formatted_income_per_capita }} per kapita</p>
                </div>

                <div>
                    <span class="text-sm text-gray-600">Pendidikan Ibu</span>
                    <p class="text-sm font-medium text-gray-900">{{ $pengukuran->pendidikan_ibu_label }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-600">Jumlah Anggota Keluarga</span>
                    <p class="text-sm font-medium text-gray-900">{{ $pengukuran->jumlah_anggota_keluarga }} orang</p>
                </div>
            </div>
        </div>

        <!-- Environmental Factors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Faktor Lingkungan
            </h3>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Akses Air Bersih</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $pengukuran->akses_air_bersih === 'ya' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $pengukuran->akses_air_bersih_label }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Sanitasi Layak</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $pengukuran->sanitasi_layak === 'ya' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $pengukuran->sanitasi_layak_label }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="bg-gray-50 rounded-xl p-6">
        <div class="flex justify-between items-center text-sm text-gray-600">
            <div>
                <p><strong>Petugas:</strong> {{ $pengukuran->user->name ?? 'Unknown' }}</p>
                <p><strong>Posyandu:</strong> {{ $pengukuran->balita->posyandu }}</p>
            </div>
            <div class="text-right">
                <p><strong>Dibuat:</strong> {{ $pengukuran->created_at->format('d/m/Y H:i') }}</p>
                @if($pengukuran->updated_at != $pengukuran->created_at)
                <p><strong>Diperbarui:</strong> {{ $pengukuran->updated_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection