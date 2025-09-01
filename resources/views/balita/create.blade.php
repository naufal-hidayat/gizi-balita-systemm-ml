{{-- resources/views/balita/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Balita')
@section('page-title', 'Tambah Data Balita')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Data Balita Baru
            </h3>
            <p class="text-sm text-gray-600 mt-1">Masukkan data lengkap balita untuk pendaftaran ke sistem</p>
        </div>

        <form method="POST" action="{{ route('balita.store') }}" class="space-y-6">
            @csrf

            <!-- Identitas Balita -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Identitas Balita</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nik_balita" class="block text-sm font-medium text-gray-700 mb-2">NIK Balita</label>
                        <input type="text" name="nik_balita" id="nik_balita" maxlength="16"
                            value="{{ old('nik_balita') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik_balita') border-red-300 @enderror"
                            placeholder="16 digit NIK" required>
                        @error('nik_balita')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_balita" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Balita</label>
                        <input type="text" name="nama_balita" id="nama_balita"
                            value="{{ old('nama_balita') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_balita') border-red-300 @enderror"
                            placeholder="Nama lengkap balita" required>
                        @error('nama_balita')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            value="{{ old('tanggal_lahir') }}"
                            max="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tanggal_lahir') border-red-300 @enderror" required>
                        @error('tanggal_lahir')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_kelamin') border-red-300 @enderror" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Data Orang Tua</h4>

                <div>
                    <label for="nama_orang_tua" class="block text-sm font-medium text-gray-700 mb-2">Nama Orang Tua/Wali</label>
                    <input type="text" name="nama_orang_tua" id="nama_orang_tua"
                        value="{{ old('nama_orang_tua') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_orang_tua') border-red-300 @enderror"
                        placeholder="Nama lengkap ayah/ibu/wali" required>
                    @error('nama_orang_tua')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Alamat Detail -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Alamat Lengkap</h4>

                <!-- RT & RW -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                        <input type="text" name="rt" id="rt" maxlength="3"
                            value="{{ old('rt') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rt') border-red-300 @enderror"
                            placeholder="001" required>
                        @error('rt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                        <input type="text" name="rw" id="rw" maxlength="3"
                            value="{{ old('rw') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rw') border-red-300 @enderror"
                            placeholder="001" required>
                        @error('rw')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dusun (Optional) -->
                <div>
                    <label for="dusun" class="block text-sm font-medium text-gray-700 mb-2">Dusun/Lingkungan <span class="text-gray-500 text-xs">(opsional)</span></label>
                    <input type="text" name="dusun" id="dusun"
                        value="{{ old('dusun') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dusun') border-red-300 @enderror"
                        placeholder="Nama dusun/lingkungan">
                    @error('dusun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Desa/Kelurahan, Kecamatan, Kabupaten -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="desa_kelurahan" class="block text-sm font-medium text-gray-700 mb-2">Desa/Kelurahan</label>
                        <input type="text" name="desa_kelurahan" id="desa_kelurahan"
                            value="{{ old('desa_kelurahan') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('desa_kelurahan') border-red-300 @enderror"
                            placeholder="Nama desa/kelurahan" required>
                        @error('desa_kelurahan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan"
                            value="{{ old('kecamatan') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kecamatan') border-red-300 @enderror"
                            placeholder="Nama kecamatan" required>
                        @error('kecamatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" id="kabupaten"
                            value="{{ old('kabupaten') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten') border-red-300 @enderror"
                            placeholder="Nama kabupaten/kota" required>
                        @error('kabupaten')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview Alamat -->
                <div id="alamat-preview" class="bg-gray-50 border border-gray-200 rounded-lg p-3" style="display: none;">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Preview Alamat:</span>
                    </div>
                    <p class="text-sm text-gray-600" id="alamat-preview-text">-</p>
                </div>
            </div>

            <!-- Lokasi Posyandu -->
            <!-- Lokasi Posyandu -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Lokasi Posyandu</h4>
                
                @if(auth()->user()->isAdmin())
                <!-- Untuk Admin: Dropdown cascade dari master data -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                        <select name="area" id="area" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('area') border-red-300 @enderror" required>
                            <option value="">-- Pilih Area --</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>
                                    {{ ucfirst($area) }}
                                </option>
                            @endforeach
                        </select>
                        @error('area')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('area') }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_posyandu_id" class="block text-sm font-medium text-gray-700 mb-2">Posyandu</label>
                        <select name="master_posyandu_id" id="master_posyandu_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_posyandu_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Posyandu --</option>
                            @foreach($posyandus as $posyandu)
                                <option value="{{ $posyandu->id }}" {{ old('master_posyandu_id') == $posyandu->id ? 'selected' : '' }}>
                                    {{ $posyandu->nama_posyandu }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_posyandu_id')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('master_posyandu_id') }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_desa_id" class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                        <select name="master_desa_id" id="master_desa_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_desa_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Desa --</option>
                            @foreach($desas as $desa)
                                <option value="{{ $desa->id }}" {{ old('master_desa_id') == $desa->id ? 'selected' : '' }}>
                                    {{ $desa->nama_desa }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_desa_id')
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('master_desa_id') }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Admin Tools -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Akses Admin</p>
                            <p class="text-xs text-yellow-700">Jika posyandu/desa belum tersedia, silahkan tambah melalui menu Master Data</p>
                            <div class="mt-2 space-x-2">
                                <a href="{{ route('balita.master-posyandu.create') }}" 
                                   class="inline-flex items-center text-xs text-yellow-700 hover:text-yellow-900 underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Posyandu
                                </a>
                                <a href="{{ route('balita.master-desa.create') }}"
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
                    <h5 class="text-sm font-medium text-blue-800 mb-2">Informasi Lokasi Anda</h5>
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
                    @if(!auth()->user()->area || !auth()->user()->posyandu_name || !auth()->user()->village)
                        <p class="text-xs text-blue-600 mt-2">
                            <em>Data lokasi akan otomatis terisi dari profil Anda</em>
                        </p>
                    @endif
                </div>
                @endif
            </div>

            <!-- Info Lokasi Terpilih (untuk admin) -->
            @if(auth()->user()->isAdmin())
            <div id="location-info" class="bg-green-50 border border-green-200 rounded-lg p-4" style="display: none;">
                <h6 class="text-sm font-medium text-green-800 mb-2">Informasi Lokasi Terpilih</h6>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-green-700">Area:</span>
                        <p class="text-green-600" id="info-area-name">-</p>
                    </div>
                    <div>
                        <span class="font-medium text-green-700">Posyandu:</span>
                        <p class="text-green-600" id="info-posyandu-name">-</p>
                        <p class="text-xs text-green-500" id="info-posyandu-alamat">-</p>
                    </div>
                    <div>
                        <span class="font-medium text-green-700">Desa:</span>
                        <p class="text-green-600" id="info-desa-name">-</p>
                        <p class="text-xs text-green-500" id="info-desa-penduduk">-</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('balita.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Data Balita</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto format NIK input
        const nikInput = document.getElementById('nik_balita');
        if (nikInput) {
            nikInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 16) {
                    this.value = this.value.slice(0, 16);
                }
            });
        }

        // Auto capitalize names
        const namaBalitaInput = document.getElementById('nama_balita');
        if (namaBalitaInput) {
            namaBalitaInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        const namaOrangTuaInput = document.getElementById('nama_orang_tua');
        if (namaOrangTuaInput) {
            namaOrangTuaInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        // Format RT/RW input - only numbers
        const rtInput = document.getElementById('rt');
        const rwInput = document.getElementById('rw');
        
        [rtInput, rwInput].forEach(input => {
            if (input) {
                // Event 'input' - hanya validasi angka dan batasi panjang, JANGAN auto-format
                input.addEventListener('input', function(e) {
                    // Hanya izinkan angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Batasi maksimal 3 karakter
                    if (this.value.length > 3) {
                        this.value = this.value.slice(0, 3);
                    }
                    
                    // Update preview tanpa format padding
                    updateAddressPreview();
                });
                
                // Event 'blur' - format dengan leading zero HANYA ketika selesai input
                input.addEventListener('blur', function() {
                    if (this.value && this.value !== '000' && this.value.length > 0) {
                        // Hilangkan leading zero dulu, baru format ulang
                        const cleanValue = parseInt(this.value, 10);
                        if (cleanValue > 0) {
                            this.value = cleanValue.toString().padStart(3, '0');
                        }
                        updateAddressPreview();
                    }
                });
                
                // Event 'focus' - hilangkan leading zero untuk memudahkan edit
                input.addEventListener('focus', function() {
                    if (this.value && this.value.startsWith('0')) {
                        const cleanValue = parseInt(this.value, 10);
                        if (cleanValue > 0) {
                            this.value = cleanValue.toString();
                        }
                    }
                });
            }
        });

        // Auto capitalize address components
        const addressFields = ['dusun', 'desa_kelurahan', 'kecamatan', 'kabupaten'];
        addressFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
                    updateAddressPreview();
                });
            }
        });

        // Update alamat preview
        function updateAddressPreview() {
            const rt = document.getElementById('rt').value;
            const rw = document.getElementById('rw').value;
            const dusun = document.getElementById('dusun').value;
            const desa = document.getElementById('desa_kelurahan').value;
            const kecamatan = document.getElementById('kecamatan').value;
            const kabupaten = document.getElementById('kabupaten').value;
            
            const addressParts = [];
            
            // Format untuk preview (dengan padding jika ada nilai)
            if (rt) {
                const rtFormatted = rt.length > 0 ? rt.padStart(3, '0') : rt;
                addressParts.push(`RT ${rtFormatted}`);
            }
            if (rw) {
                const rwFormatted = rw.length > 0 ? rw.padStart(3, '0') : rw;
                addressParts.push(`RW ${rwFormatted}`);
            }
            if (dusun) addressParts.push(`Dusun ${dusun}`);
            if (desa) addressParts.push(desa);
            if (kecamatan) addressParts.push(`Kec. ${kecamatan}`);
            if (kabupaten) addressParts.push(kabupaten);
            
            const previewDiv = document.getElementById('alamat-preview');
            const previewText = document.getElementById('alamat-preview-text');
            
            if (previewDiv && previewText) {
                if (addressParts.length > 0) {
                    previewText.textContent = addressParts.join(', ');
                    previewDiv.style.display = 'block';
                } else {
                    previewDiv.style.display = 'none';
                }
            }
        }

        // Calculate age when birth date changes
        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        if (tanggalLahirInput) {
            tanggalLahirInput.addEventListener('blur', function() {
                // Hanya validasi jika ada value dan lengkap
                if (!this.value || this.value.length < 10) {
                    return;
                }

                const birthDate = new Date(this.value);
                const today = new Date();

                // Cek tanggal valid
                if (isNaN(birthDate.getTime())) {
                    return;
                }

                // Cek tidak boleh masa depan
                if (birthDate > today) {
                    alert('Tanggal lahir tidak boleh lebih dari hari ini');
                    this.value = '';
                    this.focus();
                    return;
                }

                // Hitung umur
                const ageInMonths = Math.floor((today - birthDate) / (1000 * 60 * 60 * 24 * 30.44));
                
                // Cek maksimal 5 tahun (60 bulan)
                if (ageInMonths > 60) {
                    const years = Math.floor(ageInMonths / 12);
                    const months = ageInMonths % 12;
                    alert(`Balita maksimal berusia 5 tahun.\nUsia: ${years} tahun ${months} bulan`);
                    this.value = '';
                    this.focus();
                }
            });
        }

        @if(auth()->user()->isAdmin())
        // Dropdown dependencies untuk admin
        const areaSelect = document.getElementById('area');
        const posyanduSelect = document.getElementById('master_posyandu_id');
        const desaSelect = document.getElementById('master_desa_id');

        if (areaSelect) {
            areaSelect.addEventListener('change', function() {
                const selectedArea = this.value;
                posyanduSelect.innerHTML = '<option value="">-- Pilih Posyandu --</option>';
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

                if (selectedArea) {
                    fetch(`/api/posyandu-by-area?area=${selectedArea}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(posyandu => {
                                const option = document.createElement('option');
                                option.value = posyandu.id;
                                option.textContent = posyandu.nama_posyandu;
                                posyanduSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            posyanduSelect.addEventListener('change', function() {
                const selectedPosyandu = this.value;
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

                if (selectedPosyandu) {
                    fetch(`/api/desa-by-posyandu?posyandu_id=${selectedPosyandu}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(desa => {
                                const option = document.createElement('option');
                                option.value = desa.id;
                                option.textContent = desa.nama_desa;
                                desaSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        }
        @endif

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nik = document.getElementById('nik_balita').value;
                const rt = document.getElementById('rt').value;
                const rw = document.getElementById('rw').value;

                if (nik.length !== 16) {
                    e.preventDefault();
                    alert('NIK harus 16 digit');
                    document.getElementById('nik_balita').focus();
                    return false;
                }

                if (rt === '000') {
                    e.preventDefault();
                    alert('RT tidak boleh 000');
                    document.getElementById('rt').focus();
                    return false;
                }

                if (rw === '000') {
                    e.preventDefault();
                    alert('RW tidak boleh 000');
                    document.getElementById('rw').focus();
                    return false;
                }

                return true;
            });
        }
    });
</script>
@endsection
{{-- resources/views/balita/create.blade.php
@extends('layouts.app')

@section('title', 'Tambah Balita')
@section('page-title', 'Tambah Data Balita')

@section('content')
<div class="max-w-4xl mx-auto" x-data="balitaCreateForm()">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Daftarkan Balita Baru
            </h3>
            <p class="text-sm text-gray-600 mt-1">Pilih dari data keluarga yang sudah terdaftar untuk memastikan akurasi data</p>
        </div>

        <form method="POST" action="{{ route('balita.store') }}" class="space-y-6">
@csrf

<!-- Step 1: Pilih Keluarga -->
<div class="space-y-4">
    <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">1</span>
        Pilih Data Keluarga
    </h4>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h5 class="text-sm font-medium text-blue-800">Cara menggunakan:</h5>
                <ul class="text-xs text-blue-700 mt-1 space-y-1">
                    <li>• Masukkan No KK atau NIK Kepala Keluarga</li>
                    <li>• Sistem akan menampilkan anggota keluarga yang eligible sebagai balita</li>
                    <li>• Pilih anak yang akan didaftarkan sebagai balita</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="family_identifier" class="block text-sm font-medium text-gray-700 mb-2">
                No KK / NIK Kepala Keluarga *
            </label>
            <input type="text"
                id="family_identifier"
                x-model="familyIdentifier"
                @input="searchFamily()"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan 16 digit No KK atau NIK">
            <p class="mt-1 text-xs text-gray-500">Masukkan minimal 16 karakter untuk pencarian</p>
        </div>

        <div x-show="searching" class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-2 text-sm text-gray-600">Mencari keluarga...</span>
        </div>
    </div>

    <!-- Family Info -->
    <div x-show="familyFound" x-transition class="bg-green-50 border border-green-200 rounded-lg p-4">
        <h5 class="text-sm font-medium text-green-800 mb-2">Keluarga Ditemukan:</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-green-700">No KK:</span>
                <p class="text-green-600" x-text="familyData.no_kk"></p>
            </div>
            <div>
                <span class="font-medium text-green-700">Kepala Keluarga:</span>
                <p class="text-green-600" x-text="familyData.nama_kepala_keluarga"></p>
            </div>
            <div class="md:col-span-2">
                <span class="font-medium text-green-700">Alamat:</span>
                <p class="text-green-600" x-text="familyData.alamat_lengkap"></p>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="searchError" x-transition class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h5 class="text-sm font-medium text-red-800">Keluarga tidak ditemukan</h5>
                <p class="text-xs text-red-700 mt-1">
                    Pastikan No KK atau NIK sudah benar.
                    <a href="{{ route('keluarga.create') }}" class="underline">Daftarkan keluarga baru</a> jika belum terdaftar.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Pilih Anggota Keluarga -->
<div x-show="familyFound" x-transition class="space-y-4">
    <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">2</span>
        Pilih Anggota Keluarga (Balita)
    </h4>

    <div x-show="balitaEligible.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h5 class="text-sm font-medium text-yellow-800">Tidak ada anak yang eligible</h5>
                <p class="text-xs text-yellow-700 mt-1">
                    Keluarga ini belum memiliki anak usia 0-5 tahun yang dapat didaftarkan sebagai balita,
                    atau semua anak sudah terdaftar.
                </p>
            </div>
        </div>
    </div>

    <div x-show="balitaEligible.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <template x-for="anggota in balitaEligible" :key="anggota.id">
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer"
                :class="selectedAnggota === anggota.id ? 'border-blue-500 bg-blue-50' : 'hover:bg-gray-50'"
                @click="selectAnggota(anggota.id)">
                <label class="cursor-pointer">
                    <input type="radio"
                        name="anggota_keluarga_id"
                        :value="anggota.id"
                        x-model="selectedAnggota"
                        class="sr-only">
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium"
                            :class="anggota.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600'">
                            <span x-text="anggota.nama_lengkap.charAt(0)"></span>
                        </div>
                        <div class="flex-1">
                            <h5 class="text-sm font-medium text-gray-900" x-text="anggota.nama_lengkap"></h5>
                            <p class="text-xs text-gray-500" x-text="anggota.nik"></p>
                            <div class="mt-1 text-xs text-gray-600">
                                <span x-text="anggota.jenis_kelamin_label"></span> •
                                <span x-text="anggota.umur_bulan + ' bulan'"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Lahir: <span x-text="new Date(anggota.tanggal_lahir).toLocaleDateString('id-ID')"></span>
                            </p>
                        </div>
                        <div x-show="selectedAnggota === anggota.id" class="text-blue-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </label>
            </div>
        </template>
    </div>

    @error('anggota_keluarga_id')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Step 3: Data Orang Tua (Auto-filled) -->
<div x-show="selectedAnggota" x-transition class="space-y-4">
    <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">3</span>
        Data Orang Tua (Otomatis)
    </h4>

    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <p class="text-sm text-gray-600 mb-3">Data orang tua akan diambil otomatis dari data keluarga:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div x-show="orangTua.ayah">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ayah:</h5>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>NIK:</strong> <span x-text="orangTua.ayah?.nik"></span></p>
                    <p><strong>Nama:</strong> <span x-text="orangTua.ayah?.nama"></span></p>
                    <p><strong>Pekerjaan:</strong> <span x-text="orangTua.ayah?.pekerjaan || '-'"></span></p>
                </div>
            </div>

            <div x-show="orangTua.ibu">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Data Ibu:</h5>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>NIK:</strong> <span x-text="orangTua.ibu?.nik"></span></p>
                    <p><strong>Nama:</strong> <span x-text="orangTua.ibu?.nama"></span></p>
                    <p><strong>Pekerjaan:</strong> <span x-text="orangTua.ibu?.pekerjaan || '-'"></span></p>
                </div>
            </div>
        </div>

        <div x-show="!orangTua.ayah && !orangTua.ibu" class="text-sm text-yellow-600">
            ⚠️ Data orang tua belum lengkap dalam sistem keluarga
        </div>
    </div>
</div>

<!-- Step 4: Konfirmasi Alamat -->
<div x-show="selectedAnggota" x-transition class="space-y-4">
    <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2 flex items-center">
        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-2">4</span>
        Konfirmasi Alamat & Posyandu
    </h4>

    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <p class="text-sm text-gray-600 mb-3">Data alamat dan posyandu diambil dari data keluarga:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700">Alamat Lengkap:</span>
                <p class="text-gray-600" x-text="familyData.alamat_lengkap"></p>
            </div>
            <div>
                <span class="font-medium text-gray-700">Area:</span>
                <p class="text-gray-600" x-text="familyData.area ? familyData.area.charAt(0).toUpperCase() + familyData.area.slice(1) : '-'"></p>
            </div>
        </div>
    </div>

    <!-- Option untuk alamat berbeda -->
    <div class="flex items-center space-x-2">
        <input type="checkbox"
            id="alamat_berbeda"
            x-model="alamatBerbeda"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="alamat_berbeda" class="text-sm text-gray-700">
            Alamat balita berbeda dengan alamat keluarga
        </label>
    </div>

    <!-- Form alamat custom jika berbeda -->
    <div x-show="alamatBerbeda" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="dusun_kampung_blok" class="block text-sm font-medium text-gray-700 mb-1">
                Dusun/Kampung/Blok
            </label>
            <input type="text" name="dusun_kampung_blok" id="dusun_kampung_blok"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Contoh: Dusun Mawar">
        </div>

        <div>
            <label for="rt" class="block text-sm font-medium text-gray-700 mb-1">RT</label>
            <input type="text" name="rt" id="rt" maxlength="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="001">
        </div>

        <div>
            <label for="rw" class="block text-sm font-medium text-gray-700 mb-1">RW</label>
            <input type="text" name="rw" id="rw" maxlength="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="002">
        </div>

        <div>
            <label for="kelurahan_desa_balita" class="block text-sm font-medium text-gray-700 mb-1">
                Kelurahan/Desa
            </label>
            <input type="text" name="kelurahan_desa_balita" id="kelurahan_desa_balita"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Nama Kelurahan/Desa">
        </div>
    </div>
</div>

<!-- Submit Buttons -->
<div class="flex items-center justify-between pt-6 border-t border-gray-200">
    <div class="flex items-center space-x-2 text-sm text-gray-600">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
        </svg>
        <span>QR Code akan di-generate otomatis setelah pendaftaran</span>
    </div>

    <div class="flex items-center space-x-4">
        <a href="{{ route('balita.index') }}"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
            Batal
        </a>
        <button type="submit"
            x-bind:disabled="!selectedAnggota"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>Daftarkan Balita</span>
        </button>
    </div>
</div>
</form>
</div>
</div>

<script>
    function balitaCreateForm() {
        return {
            familyIdentifier: '',
            familyFound: false,
            familyData: {},
            balitaEligible: [],
            selectedAnggota: null,
            orangTua: {
                ayah: null,
                ibu: null
            },
            searching: false,
            searchError: false,
            alamatBerbeda: false,

            init() {
                // Initialize
            },

            async searchFamily() {
                if (this.familyIdentifier.length < 16) {
                    this.familyFound = false;
                    this.searchError = false;
                    return;
                }

                this.searching = true;
                this.searchError = false;
                this.familyFound = false;

                try {
                    const response = await fetch(`/api/keluarga/by-identifier?identifier=${this.familyIdentifier}`);
                    const data = await response.json();

                    if (data.success) {
                        this.familyData = data.keluarga;
                        this.balitaEligible = data.anggota_balita;
                        this.familyFound = true;
                        this.searchError = false;
                    } else {
                        this.familyFound = false;
                        this.searchError = true;
                    }
                } catch (error) {
                    console.error('Error searching family:', error);
                    this.familyFound = false;
                    this.searchError = true;
                } finally {
                    this.searching = false;
                }
            },

            async selectAnggota(anggotaId) {
                this.selectedAnggota = anggotaId;

                // Fetch data orang tua
                try {
                    const response = await fetch(`/api/keluarga/orang-tua?keluarga_id=${this.familyData.id}`);
                    const data = await response.json();

                    if (data.success) {
                        this.orangTua = data.orang_tua;
                    }
                } catch (error) {
                    console.error('Error fetching orang tua:', error);
                }
            }
        }
    }
</script>
@endsection --}}