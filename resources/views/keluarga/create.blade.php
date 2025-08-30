{{-- resources/views/keluarga/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Keluarga')
@section('page-title', 'Daftarkan Keluarga Baru')

@section('content')
<div class="max-w-4xl mx-auto" x-data="keluargaCreateForm()">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Daftarkan Keluarga Baru
            </h3>
            <p class="text-sm text-gray-600 mt-1">Masukkan data keluarga untuk memudahkan pendaftaran balita nantinya</p>
        </div>

        <form method="POST" action="{{ route('keluarga.store') }}" class="space-y-6">
            @csrf
            
            <!-- Step 1: Data Kartu Keluarga -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">1</span>
                    Data Kartu Keluarga
                </h4>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h5 class="text-sm font-medium text-blue-800">Informasi:</h5>
                            <p class="text-xs text-blue-700 mt-1">
                                Pastikan data sesuai dengan Kartu Keluarga (KK) resmi. Data ini akan digunakan sebagai basis pendaftaran balita.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="no_kk" class="block text-sm font-medium text-gray-700 mb-2">
                            No Kartu Keluarga (KK) *
                        </label>
                        <input type="text" name="no_kk" id="no_kk" maxlength="16"
                               value="{{ old('no_kk') }}"
                               x-model="noKK"
                               @input="formatKKNumber()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('no_kk') border-red-300 @enderror"
                               placeholder="16 digit nomor KK" required>
                        @error('no_kk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nik_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            NIK Kepala Keluarga *
                        </label>
                        <input type="text" name="nik_kepala_keluarga" id="nik_kepala_keluarga" maxlength="16"
                               value="{{ old('nik_kepala_keluarga') }}"
                               x-model="nikKepala"
                               @input="formatNIKNumber()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik_kepala_keluarga') border-red-300 @enderror"
                               placeholder="16 digit NIK kepala keluarga" required>
                        @error('nik_kepala_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 2: Data Kepala Keluarga -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">2</span>
                    Data Kepala Keluarga
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap Kepala Keluarga *
                        </label>
                        <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga"
                               value="{{ old('nama_kepala_keluarga') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_kepala_keluarga') border-red-300 @enderror"
                               placeholder="Nama sesuai KTP" required>
                        @error('nama_kepala_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Kelamin *
                        </label>
                        <select name="jenis_kelamin_kepala_keluarga" id="jenis_kelamin_kepala_keluarga"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kelamin_kepala_keluarga') border-red-300 @enderror" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin_kepala_keluarga') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin_kepala_keluarga') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin_kepala_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Lahir *
                        </label>
                        <input type="date" name="tanggal_lahir_kepala_keluarga" id="tanggal_lahir_kepala_keluarga"
                               value="{{ old('tanggal_lahir_kepala_keluarga') }}"
                               max="{{ date('Y-m-d', strtotime('-17 years')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir_kepala_keluarga') border-red-300 @enderror" required>
                        @error('tanggal_lahir_kepala_keluarga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pekerjaan_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Pekerjaan
                        </label>
                        <input type="text" name="pekerjaan_kepala_keluarga" id="pekerjaan_kepala_keluarga"
                               value="{{ old('pekerjaan_kepala_keluarga') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Contoh: Petani, Wiraswasta, PNS">
                    </div>

                    <div>
                        <label for="pendidikan_kepala_keluarga" class="block text-sm font-medium text-gray-700 mb-2">
                            Pendidikan Terakhir
                        </label>
                        <select name="pendidikan_kepala_keluarga" id="pendidikan_kepala_keluarga"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Pendidikan --</option>
                            <option value="tidak_sekolah" {{ old('pendidikan_kepala_keluarga') == 'tidak_sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                            <option value="tidak_tamat_sd" {{ old('pendidikan_kepala_keluarga') == 'tidak_tamat_sd' ? 'selected' : '' }}>Tidak Tamat SD</option>
                            <option value="sd" {{ old('pendidikan_kepala_keluarga') == 'sd' ? 'selected' : '' }}>SD/Sederajat</option>
                            <option value="smp" {{ old('pendidikan_kepala_keluarga') == 'smp' ? 'selected' : '' }}>SMP/Sederajat</option>
                            <option value="sma" {{ old('pendidikan_kepala_keluarga') == 'sma' ? 'selected' : '' }}>SMA/Sederajat</option>
                            <option value="diploma" {{ old('pendidikan_kepala_keluarga') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="sarjana" {{ old('pendidikan_kepala_keluarga') == 'sarjana' ? 'selected' : '' }}>Sarjana</option>
                            <option value="magister" {{ old('pendidikan_kepala_keluarga') == 'magister' ? 'selected' : '' }}>Magister</option>
                            <option value="doktor" {{ old('pendidikan_kepala_keluarga') == 'doktor' ? 'selected' : '' }}>Doktor</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 3: Alamat Terstruktur -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">3</span>
                    Alamat Lengkap (Sesuai KTP)
                </h4>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h5 class="text-sm font-medium text-yellow-800">Format Alamat KTP:</h5>
                            <p class="text-xs text-yellow-700 mt-1">
                                Alamat dipisah sesuai struktur KTP Indonesia untuk memudahkan analisis data posyandu per wilayah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="dusun_kampung_blok" class="block text-sm font-medium text-gray-700 mb-2">
                            Dusun/Kampung/Blok *
                        </label>
                        <input type="text" name="dusun_kampung_blok" id="dusun_kampung_blok"
                               value="{{ old('dusun_kampung_blok') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dusun_kampung_blok') border-red-300 @enderror"
                               placeholder="Contoh: Dusun Mawar" required>
                        @error('dusun_kampung_blok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT *</label>
                        <input type="text" name="rt" id="rt" maxlength="3"
                               value="{{ old('rt') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rt') border-red-300 @enderror"
                               placeholder="001" required>
                        @error('rt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW *</label>
                        <input type="text" name="rw" id="rw" maxlength="3"
                               value="{{ old('rw') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rw') border-red-300 @enderror"
                               placeholder="002" required>
                        @error('rw')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kode_pos" class="block text-sm font-medium text-gray-700 mb-2">
                            Kode Pos
                        </label>
                        <input type="text" name="kode_pos" id="kode_pos" maxlength="5"
                               value="{{ old('kode_pos') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="12345">
                    </div>

                    <div>
                        <label for="kelurahan_desa" class="block text-sm font-medium text-gray-700 mb-2">
                            Kelurahan/Desa *
                        </label>
                        <input type="text" name="kelurahan_desa" id="kelurahan_desa"
                               value="{{ old('kelurahan_desa') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kelurahan_desa') border-red-300 @enderror"
                               placeholder="Nama Kelurahan/Desa" required>
                        @error('kelurahan_desa')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">
                            Kecamatan *
                        </label>
                        <input type="text" name="kecamatan" id="kecamatan"
                               value="{{ old('kecamatan') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kecamatan') border-red-300 @enderror"
                               placeholder="Nama Kecamatan" required>
                        @error('kecamatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kabupaten_kota" class="block text-sm font-medium text-gray-700 mb-2">
                            Kabupaten/Kota *
                        </label>
                        <input type="text" name="kabupaten_kota" id="kabupaten_kota"
                               value="{{ old('kabupaten_kota') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten_kota') border-red-300 @enderror"
                               placeholder="Nama Kabupaten/Kota" required>
                        @error('kabupaten_kota')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">
                            Provinsi *
                        </label>
                        <input type="text" name="provinsi" id="provinsi"
                               value="{{ old('provinsi') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('provinsi') border-red-300 @enderror"
                               placeholder="Nama Provinsi" required>
                        @error('provinsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview Alamat -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Preview Alamat Lengkap:</h5>
                    <p class="text-sm text-gray-600" x-text="previewAlamat || 'Alamat akan muncul saat Anda mengisi form...'"></p>
                </div>
            </div>

            <!-- Step 4: Area Posyandu -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">4</span>
                    Area dan Posyandu
                </h4>

                @if(auth()->user()->isAdmin())
                <!-- Untuk Admin: Dropdown cascade dari master data -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area *</label>
                        <select name="area" id="area" 
                                x-model="selectedArea"
                                @change="loadPosyandu()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('area') border-red-300 @enderror" required>
                            <option value="">-- Pilih Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>
                                    {{ ucfirst($area) }}
                                </option>
                            @endforeach
                        </select>
                        @error('area')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_posyandu_id" class="block text-sm font-medium text-gray-700 mb-2">Posyandu *</label>
                        <select name="master_posyandu_id" id="master_posyandu_id" 
                                x-model="selectedPosyandu"
                                @change="loadDesa()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_posyandu_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Posyandu --</option>
                            @foreach($posyandus as $posyandu)
                                <option value="{{ $posyandu->id }}" {{ old('master_posyandu_id') == $posyandu->id ? 'selected' : '' }}>
                                    {{ $posyandu->nama_posyandu }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_posyandu_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_desa_id" class="block text-sm font-medium text-gray-700 mb-2">Desa *</label>
                        <select name="master_desa_id" id="master_desa_id" 
                                x-model="selectedDesa"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_desa_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Desa --</option>
                            @foreach($desas as $desa)
                                <option value="{{ $desa->id }}" {{ old('master_desa_id') == $desa->id ? 'selected' : '' }}>
                                    {{ $desa->nama_desa }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_desa_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Tools untuk Admin -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Akses Admin</p>
                            <p class="text-xs text-yellow-700">Jika posyandu/desa belum tersedia, silahkan tambah melalui menu Master Data</p>
                            <div class="mt-2 space-x-2">
                                <a href="{{ route('master-posyandu.create') }}" 
                                   class="inline-flex items-center text-xs text-yellow-700 hover:text-yellow-900 underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Posyandu
                                </a>
                                <a href="{{ route('master-desa.create') }}"
                                   class="inline-flex items-center text-xs text-yellow-700 hover:text-yellow-900 underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Desa
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Untuk Non-Admin: Tampilkan data mereka -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-blue-800 mb-2">Informasi Area Anda:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-700">Area:</span>
                            <p class="text-blue-600">{{ auth()->user()->area ? ucfirst(auth()->user()->area) : 'Belum ditentukan' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Posyandu:</span>
                            <p class="text-blue-600">{{ auth()->user()->posyandu_name ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Desa:</span>
                            <p class="text-blue-600">{{ auth()->user()->village ?? 'Belum ditentukan' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Setelah keluarga terdaftar, Anda dapat menambahkan anggota keluarga dan mendaftarkan balita</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('keluarga.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Daftarkan Keluarga</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function keluargaCreateForm() {
    return {
        noKK: '{{ old('no_kk') }}',
        nikKepala: '{{ old('nik_kepala_keluarga') }}',
        selectedArea: '{{ old('area') }}',
        selectedPosyandu: '{{ old('master_posyandu_id') }}',
        selectedDesa: '{{ old('master_desa_id') }}',
        previewAlamat: '',
        
        init() {
            this.updatePreviewAlamat();
            this.watchAlamatInputs();
        },
        
        formatKKNumber() {
            // Hanya angka
            this.noKK = this.noKK.replace(/[^0-9]/g, '');
            if (this.noKK.length > 16) {
                this.noKK = this.noKK.slice(0, 16);
            }
        },
        
        formatNIKNumber() {
            // Hanya angka
            this.nikKepala = this.nikKepala.replace(/[^0-9]/g, '');
            if (this.nikKepala.length > 16) {
                this.nikKepala = this.nikKepala.slice(0, 16);
            }
        },
        
        watchAlamatInputs() {
            // Watch perubahan input alamat untuk update preview
            ['dusun_kampung_blok', 'rt', 'rw', 'kelurahan_desa', 'kecamatan', 'kabupaten_kota', 'provinsi'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', () => {
                        this.updatePreviewAlamat();
                    });
                }
            });
        },
        
        updatePreviewAlamat() {
            const dusun = document.getElementById('dusun_kampung_blok')?.value || '';
            const rt = document.getElementById('rt')?.value || '';
            const rw = document.getElementById('rw')?.value || '';
            const kelurahan = document.getElementById('kelurahan_desa')?.value || '';
            const kecamatan = document.getElementById('kecamatan')?.value || '';
            const kabupaten = document.getElementById('kabupaten_kota')?.value || '';
            const provinsi = document.getElementById('provinsi')?.value || '';
            
            let alamatParts = [];
            
            if (dusun) alamatParts.push(dusun);
            if (rt && rw) alamatParts.push(`RT ${rt}/RW ${rw}`);
            if (kelurahan) alamatParts.push(kelurahan);
            if (kecamatan) alamatParts.push(kecamatan);
            if (kabupaten) alamatParts.push(kabupaten);
            if (provinsi) alamatParts.push(provinsi);
            
            this.previewAlamat = alamatParts.join(', ');
        },
        
        @if(auth()->user()->isAdmin())
        async loadPosyandu() {
            if (!this.selectedArea) {
                document.getElementById('master_posyandu_id').innerHTML = '<option value="">-- Pilih Posyandu --</option>';
                document.getElementById('master_desa_id').innerHTML = '<option value="">-- Pilih Desa --</option>';
                return;
            }
            
            try {
                const response = await fetch(`/api/posyandu-by-area?area=${this.selectedArea}`);
                const posyandus = await response.json();
                
                const posyanduSelect = document.getElementById('master_posyandu_id');
                posyanduSelect.innerHTML = '<option value="">-- Pilih Posyandu --</option>';
                
                posyandus.forEach(posyandu => {
                    const option = document.createElement('option');
                    option.value = posyandu.id;
                    option.textContent = posyandu.nama_posyandu;
                    posyanduSelect.appendChild(option);
                });
                
                // Reset desa
                document.getElementById('master_desa_id').innerHTML = '<option value="">-- Pilih Desa --</option>';
                this.selectedPosyandu = '';
                this.selectedDesa = '';
                
            } catch (error) {
                console.error('Error loading posyandu:', error);
            }
        },
        
        async loadDesa() {
            if (!this.selectedPosyandu) {
                document.getElementById('master_desa_id').innerHTML = '<option value="">-- Pilih Desa --</option>';
                return;
            }
            
            try {
                const response = await fetch(`/api/desa-by-posyandu?posyandu_id=${this.selectedPosyandu}`);
                const desas = await response.json();
                
                const desaSelect = document.getElementById('master_desa_id');
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
                
                desas.forEach(desa => {
                    const option = document.createElement('option');
                    option.value = desa.id;
                    option.textContent = desa.nama_desa;
                    desaSelect.appendChild(option);
                });
                
                this.selectedDesa = '';
                
            } catch (error) {
                console.error('Error loading desa:', error);
            }
        }
        @endif
    }
}
</script>
@endsection