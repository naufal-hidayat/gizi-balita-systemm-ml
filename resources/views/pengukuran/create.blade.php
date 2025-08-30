@extends('layouts.app')

@section('title', 'Input Pengukuran - Sistem Prediksi Gizi Balita')
@section('page-title', 'Input Pengukuran')

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
    <span class="text-gray-500">Tambah Pengukuran</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- QR Scanner --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Scan QR Balita</h3>
        <div id="reader" style="width: 300px"></div>
    </div>

    <form action="{{ route('pengukuran.store') }}" method="POST" x-data="pengukuranForm()" class="space-y-8">
        @csrf

        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Input Data Pengukuran</h2>
                    <p class="text-sm text-gray-600">Masukkan data antropometri dan faktor pendukung untuk prediksi status gizi balita</p>
                </div>
            </div>
        </div>

        <!-- Pilih Balita & Data Dasar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Identitas Balita & Data Pengukuran
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="balita_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Balita *</label>
                    <!-- Pilih Balita -->
                    <select name="balita_id" id="balita_id" x-model="selectedBalita" @change="updateBalitaInfo()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('balita_id') border-red-300 @enderror">
                        <option value="">-- Pilih Balita --</option>
                        @foreach($balitaList as $balita)
                            <option value="{{ $balita->id }}" 
                                data-nik="{{ $balita->nik_balita }}"
                                data-nama="{{ $balita->nama_balita }}"
                                data-tanggal-lahir="{{ $balita->tanggal_lahir->format('Y-m-d') }}"
                                data-jenis-kelamin="{{ $balita->jenis_kelamin }}">
                            {{ $balita->nama_balita }} - {{ $balita->nik_balita }}
                            </option>

                        @endforeach
                    </select>
                    @error('balita_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_pengukuran" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengukuran *</label>
                    <input type="date" name="tanggal_pengukuran" id="tanggal_pengukuran"
                        value="{{ old('tanggal_pengukuran', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"
                        @change="calculateAge()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_pengukuran') border-red-300 @enderror">
                    @error('tanggal_pengukuran')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="umur_bulan" class="block text-sm font-medium text-gray-700 mb-2">Umur (bulan) *</label>
                    <input type="number" name="umur_bulan" id="umur_bulan" min="0" max="60"
                        value="{{ old('umur_bulan') }}"
                        x-model="calculatedAge"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('umur_bulan') border-red-300 @enderror"
                        placeholder="Akan dihitung otomatis">
                    @error('umur_bulan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Umur akan dihitung otomatis berdasarkan tanggal lahir dan tanggal pengukuran</p>
                </div>

                <!-- Info Balita -->
                <div x-show="balitaInfo.nama" class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Info Balita Terpilih:</h4>
                    <div class="text-sm text-blue-800">
                        <p><strong>Nama:</strong> <span x-text="balitaInfo.nama"></span></p>
                        <p><strong>Jenis Kelamin:</strong> <span x-text="balitaInfo.jenisKelamin === 'L' ? 'Laki-laki' : 'Perempuan'"></span></p>
                        <p><strong>Tanggal Lahir:</strong> <span x-text="balitaInfo.tanggalLahir"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Antropometri -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Data Antropometri
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg) *</label>
                    <input type="number" name="berat_badan" id="berat_badan" step="0.1" min="2" max="50"
                        value="{{ old('berat_badan') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('berat_badan') border-red-300 @enderror"
                        placeholder="Contoh: 12.5">
                    @error('berat_badan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-2">Tinggi Badan (cm) *</label>
                    <input type="number" name="tinggi_badan" id="tinggi_badan" step="0.1" min="30" max="150"
                        value="{{ old('tinggi_badan') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tinggi_badan') border-red-300 @enderror"
                        placeholder="Contoh: 85.5">
                    @error('tinggi_badan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lingkar_kepala" class="block text-sm font-medium text-gray-700 mb-2">Lingkar Kepala (cm) <span class="text-gray-400">(Opsional)</span></label>
                    <input type="number" name="lingkar_kepala" id="lingkar_kepala" step="0.1" min="25" max="60"
                        value="{{ old('lingkar_kepala') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: 48.0">
                </div>

                <div>
                    <label for="lingkar_lengan" class="block text-sm font-medium text-gray-700 mb-2">Lingkar Lengan (cm) <span class="text-gray-400">(Opsional)</span></label>
                    <input type="number" name="lingkar_lengan" id="lingkar_lengan" step="0.1" min="8" max="25"
                        value="{{ old('lingkar_lengan') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Contoh: 15.5">
                </div>
            </div>
        </div>

        <!-- Faktor Kesehatan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Faktor Kesehatan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="asi_eksklusif" class="block text-sm font-medium text-gray-700 mb-2">ASI Eksklusif (0-6 bulan) *</label>
                    <select name="asi_eksklusif" id="asi_eksklusif"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('asi_eksklusif') border-red-300 @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="ya" {{ old('asi_eksklusif') == 'ya' ? 'selected' : '' }}>Ya</option>
                        <option value="tidak" {{ old('asi_eksklusif') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('asi_eksklusif')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="imunisasi_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Status Imunisasi *</label>
                    <select name="imunisasi_lengkap" id="imunisasi_lengkap"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('imunisasi_lengkap') border-red-300 @enderror">
                        <option value="">-- Pilih --</option>
                        <option value="ya" {{ old('imunisasi_lengkap') == 'ya' ? 'selected' : '' }}>Lengkap</option>
                        <option value="tidak_lengkap" {{ old('imunisasi_lengkap') == 'tidak_lengkap' ? 'selected' : '' }}>Tidak Lengkap</option>
                        <option value="tidak" {{ old('imunisasi_lengkap') == 'tidak' ? 'selected' : '' }}>Belum Imunisasi</option>
                    </select>
                    @error('imunisasi_lengkap')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="riwayat_penyakit" class="block text-sm font-medium text-gray-700 mb-2">Riwayat Penyakit <span class="text-gray-400">(Opsional)</span></label>
                    <textarea name="riwayat_penyakit" id="riwayat_penyakit" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Masukkan riwayat penyakit balita jika ada...">{{ old('riwayat_penyakit') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Faktor Sosial-Ekonomi -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Faktor Sosial-Ekonomi
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- <div>
                    <label for="pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Pendapatan Keluarga (Rp/bulan) *</label>
                    <input type="number" name="pendapatan_keluarga" id="pendapatan_keluarga" min="0" step="10000"
                           value="{{ old('pendapatan_keluarga') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pendapatan_keluarga') border-red-300 @enderror"
                placeholder="Contoh: 2500000">
                @error('pendapatan_keluarga')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div> --}}

            <div>
                <label for="pendapatan_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Pendapatan Keluarga (per bulan) *</label>
                <select name="pendapatan_keluarga" id="pendapatan_keluarga"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pendapatan_keluarga') border-red-300 @enderror">
                    <option value="">-- Pilih Kelompok Pendapatan --</option>
                    @foreach($pendapatanGroups as $key => $label)
                    <option value="{{ $key }}" {{ old('pendapatan_keluarga') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('pendapatan_keluarga')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pendidikan_ibu" class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Ibu *</label>
                <select name="pendidikan_ibu" id="pendidikan_ibu"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('pendidikan_ibu') border-red-300 @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="sd" {{ old('pendidikan_ibu') == 'sd' ? 'selected' : '' }}>SD/Sederajat</option>
                    <option value="smp" {{ old('pendidikan_ibu') == 'smp' ? 'selected' : '' }}>SMP/Sederajat</option>
                    <option value="sma" {{ old('pendidikan_ibu') == 'sma' ? 'selected' : '' }}>SMA/Sederajat</option>
                    <option value="diploma" {{ old('pendidikan_ibu') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                    <option value="sarjana" {{ old('pendidikan_ibu') == 'sarjana' ? 'selected' : '' }}>Sarjana/Lebih Tinggi</option>
                </select>
                @error('pendidikan_ibu')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah_anggota_keluarga" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Anggota Keluarga *</label>
                <input type="number" name="jumlah_anggota_keluarga" id="jumlah_anggota_keluarga" min="1" max="20"
                    value="{{ old('jumlah_anggota_keluarga') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jumlah_anggota_keluarga') border-red-300 @enderror"
                    placeholder="Contoh: 4">
                @error('jumlah_anggota_keluarga')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
</div>

<!-- Faktor Lingkungan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Faktor Lingkungan
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="akses_air_bersih" class="block text-sm font-medium text-gray-700 mb-2">Akses Air Bersih *</label>
            <select name="akses_air_bersih" id="akses_air_bersih"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('akses_air_bersih') border-red-300 @enderror">
                <option value="">-- Pilih --</option>
                <option value="ya" {{ old('akses_air_bersih') == 'ya' ? 'selected' : '' }}>Ya</option>
                <option value="tidak" {{ old('akses_air_bersih') == 'tidak' ? 'selected' : '' }}>Tidak</option>
            </select>
            @error('akses_air_bersih')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sanitasi_layak" class="block text-sm font-medium text-gray-700 mb-2">Sanitasi Layak *</label>
            <select name="sanitasi_layak" id="sanitasi_layak"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sanitasi_layak') border-red-300 @enderror">
                <option value="">-- Pilih --</option>
                <option value="ya" {{ old('sanitasi_layak') == 'ya' ? 'selected' : '' }}>Ya</option>
                <option value="tidak" {{ old('sanitasi_layak') == 'tidak' ? 'selected' : '' }}>Tidak</option>
            </select>
            @error('sanitasi_layak')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Submit Buttons -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Sistem akan otomatis menghitung prediksi status gizi</span>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('pengukuran.index') }}"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Simpan & Prediksi</span>
            </button>
        </div>
    </div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
function pengukuranForm() {
    return {
        selectedBalita: '{{ old('balita_id', $balita->id ?? '') }}',
        calculatedAge: {{ old('umur_bulan', 0) }},
        balitaInfo: {
            nama: '',
            jenisKelamin: '',
            tanggalLahir: ''
        },
        
        init() {
            if (this.selectedBalita) {
                this.updateBalitaInfo();
            }
            this.calculateAge();
        },
        
        updateBalitaInfo() {
            if (!this.selectedBalita) {
                this.balitaInfo = { nama: '', jenisKelamin: '', tanggalLahir: '' };
                this.calculatedAge = 0;
                return;
            }
            
            const select = document.getElementById('balita_id');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                this.balitaInfo = {
                    nama: selectedOption.dataset.nama || '',
                    jenisKelamin: selectedOption.dataset.jenisKelamin || '',
                    tanggalLahir: selectedOption.dataset.tanggalLahir || ''
                };
                this.calculateAge();
            }
        },
        
        calculateAge() {
            if (!this.balitaInfo.tanggalLahir) return;
            
            const tanggalPengukuran = document.getElementById('tanggal_pengukuran').value;
            if (!tanggalPengukuran) return;
            
            const birthDate = new Date(this.balitaInfo.tanggalLahir);
            const measureDate = new Date(tanggalPengukuran);
            
            if (measureDate < birthDate) {
                this.calculatedAge = 0;
                return;
            }
            
            let ageMonths = 0;
            let currentDate = new Date(birthDate);
            
            while (currentDate < measureDate) {
                currentDate.setMonth(currentDate.getMonth() + 1);
                ageMonths++;
            }
            
            // Adjust if we went over
            if (currentDate > measureDate) {
                ageMonths--;
            }
            
            this.calculatedAge = Math.max(0, ageMonths);
        }
    }
}

// Set default date to today
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('tanggal_pengukuran');
    if (!dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
});

    // scanner
    function onScanSuccess(decodedText) {
        // Isi input nik kalau ada
        let nikInput = document.getElementById('nik');
        if (nikInput) nikInput.value = decodedText;

        // Cari option balita dengan data-nik sama
        const select = document.getElementById('balita_id');
        let found = false;

        for (let option of select.options) {
            if (option.dataset.nik == decodedText) {
                select.value = option.value;
                // trigger supaya Alpine.js / JS update jalan
                select.dispatchEvent(new Event('change'));
                found = true;
                break;
            }
        }

        if (!found) {
            alert("NIK tidak ditemukan di daftar balita!");
        }
    }

    // init scanner
    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("reader")) {
            var html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: 250
            });
            html5QrcodeScanner.render(onScanSuccess);
        }
    });
</script>
@endpush