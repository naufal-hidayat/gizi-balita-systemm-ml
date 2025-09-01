{{-- resources/views/balita/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Balita')
@section('page-title', 'Edit Data Balita')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Data Balita
            </h3>
            <p class="text-sm text-gray-600 mt-1">Perbarui data balita: {{ $balita->nama_balita }}</p>
        </div>

        <form method="POST" action="{{ route('balita.update', $balita) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Identitas Balita -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Identitas Balita</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nik_balita" class="block text-sm font-medium text-gray-700 mb-2">NIK Balita</label>
                        <input type="text" name="nik_balita" id="nik_balita" maxlength="16" 
                               value="{{ old('nik_balita', $balita->nik_balita) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nik_balita') border-red-300 @enderror"
                               placeholder="16 digit NIK" required>
                        @error('nik_balita')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_balita" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Balita</label>
                        <input type="text" name="nama_balita" id="nama_balita" 
                               value="{{ old('nama_balita', $balita->nama_balita) }}"
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
                               value="{{ old('tanggal_lahir', $balita->tanggal_lahir->format('Y-m-d')) }}"
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
                            <option value="L" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                           value="{{ old('nama_orang_tua', $balita->nama_orang_tua) }}"
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
                            value="{{ old('rt', $balita->rt ?? str_pad('1', 3, '0', STR_PAD_LEFT)) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rt') border-red-300 @enderror"
                            placeholder="001" required>
                        @error('rt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                        <input type="text" name="rw" id="rw" maxlength="3"
                            value="{{ old('rw', $balita->rw ?? str_pad('1', 3, '0', STR_PAD_LEFT)) }}"
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
                        value="{{ old('dusun', $balita->dusun) }}"
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
                            value="{{ old('desa_kelurahan', $balita->desa_kelurahan ?? $balita->desa) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('desa_kelurahan') border-red-300 @enderror"
                            placeholder="Nama desa/kelurahan" required>
                        @error('desa_kelurahan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan"
                            value="{{ old('kecamatan', $balita->kecamatan) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kecamatan') border-red-300 @enderror"
                            placeholder="Nama kecamatan" required>
                        @error('kecamatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota</label>
                        <input type="text" name="kabupaten" id="kabupaten"
                            value="{{ old('kabupaten', $balita->kabupaten) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kabupaten') border-red-300 @enderror"
                            placeholder="Nama kabupaten/kota" required>
                        @error('kabupaten')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview Alamat -->
                <div id="alamat-preview" class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center mb-2">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Preview Alamat:</span>
                    </div>
                    <p class="text-sm text-gray-600" id="alamat-preview-text">{{ $balita->formatted_address ?? '-' }}</p>
                </div>

                <!-- Info: Alamat Lama (jika ada) -->
                @if($balita->alamat_lengkap)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <h6 class="text-sm font-medium text-yellow-800">Alamat Lama:</h6>
                            <p class="text-sm text-yellow-700 mt-1">{{ $balita->alamat_lengkap }}</p>
                            <p class="text-xs text-yellow-600 mt-1">
                                <em>Alamat di atas akan digantikan dengan format baru yang sudah diisi di form</em>
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Lokasi Posyandu -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Lokasi Posyandu</h4>
                
                @if(auth()->user()->isAdmin())
                <!-- Admin: Form lengkap -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                        <select name="area" id="area"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('area') border-red-300 @enderror" required>
                            <option value="">-- Pilih Area --</option>
                            @foreach($areas as $area)
                            <option value="{{ $area }}" {{ old('area', $balita->area) == $area ? 'selected' : '' }}>
                                {{ ucfirst($area) }}
                            </option>
                            @endforeach
                        </select>
                        @error('area')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_posyandu_id" class="block text-sm font-medium text-gray-700 mb-2">Posyandu</label>
                        <select name="master_posyandu_id" id="master_posyandu_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_posyandu_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Posyandu --</option>
                            @foreach($posyandus as $posyandu)
                            <option value="{{ $posyandu->id }}" {{ old('master_posyandu_id', $balita->master_posyandu_id) == $posyandu->id ? 'selected' : '' }}>
                                {{ $posyandu->nama_posyandu }}
                            </option>
                            @endforeach
                        </select>
                        @error('master_posyandu_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="master_desa_id" class="block text-sm font-medium text-gray-700 mb-2">Desa</label>
                        <select name="master_desa_id" id="master_desa_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('master_desa_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Desa --</option>
                            @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ old('master_desa_id', $balita->master_desa_id) == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                            @endforeach
                        </select>
                        @error('master_desa_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @else
                <!-- Non-admin: Info only -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h5 class="text-sm font-medium text-blue-800 mb-2">Informasi Lokasi Posyandu</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-700">Area:</span>
                            <p class="text-blue-600">{{ $balita->area_label ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Posyandu:</span>
                            <p class="text-blue-600">{{ $balita->posyandu ?? 'Belum ditentukan' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Desa:</span>
                            <p class="text-blue-600">{{ $balita->desa ?? 'Belum ditentukan' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('balita.show', $balita) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Update Data Balita</span>
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
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 3) {
                        this.value = this.value.slice(0, 3);
                    }
                    updateAddressPreview();
                });
                
                input.addEventListener('blur', function() {
                    if (this.value && this.value !== '000' && this.value.length > 0) {
                        this.value = this.value.padStart(3, '0');
                        updateAddressPreview();
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
            
            if (rt) addressParts.push(`RT ${rt.padStart(3, '0')}`);
            if (rw) addressParts.push(`RW ${rw.padStart(3, '0')}`);
            if (dusun) addressParts.push(`Dusun ${dusun}`);
            if (desa) addressParts.push(desa);
            if (kecamatan) addressParts.push(`Kec. ${kecamatan}`);
            if (kabupaten) addressParts.push(kabupaten);
            
            const previewText = document.getElementById('alamat-preview-text');
            
            if (addressParts.length > 0) {
                previewText.textContent = addressParts.join(', ');
            } else {
                previewText.textContent = '-';
            }
        }

        // Initialize preview on page load
        updateAddressPreview();

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