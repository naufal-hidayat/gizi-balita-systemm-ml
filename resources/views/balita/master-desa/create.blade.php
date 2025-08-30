{{-- resources/views/master-desa/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Desa')
@section('page-title', 'Tambah Master Desa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Master Desa Baru
            </h3>
            <p class="text-sm text-gray-600 mt-1">Masukkan data lengkap desa yang dilayani posyandu</p>
        </div>

        <form method="POST" action="{{ route('balita.master-desa.store') }}" class="space-y-6">
            @csrf

            <!-- Informasi Lokasi -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Lokasi</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select name="area" id="area"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('area') border-red-300 @enderror" required>
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
                        <label for="master_posyandu_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Posyandu <span class="text-red-500">*</span>
                        </label>
                        <select name="master_posyandu_id" id="master_posyandu_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('master_posyandu_id') border-red-300 @enderror" required>
                            <option value="">-- Pilih Posyandu --</option>
                            @foreach($posyandus as $posyandu)
                            <option value="{{ $posyandu->id }}"
                                data-area="{{ $posyandu->area }}"
                                {{ old('master_posyandu_id') == $posyandu->id ? 'selected' : '' }}>
                                {{ $posyandu->nama_posyandu }} ({{ ucfirst($posyandu->area) }})
                            </option>
                            @endforeach
                        </select>
                        @error('master_posyandu_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="nama_desa" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Desa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_desa" id="nama_desa"
                        value="{{ old('nama_desa') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('nama_desa') border-red-300 @enderror"
                        placeholder="Contoh: Desa Sukamaju" required>
                    @error('nama_desa')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informasi Demografis -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Demografis</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="jumlah_penduduk" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Penduduk
                        </label>
                        <input type="number" name="jumlah_penduduk" id="jumlah_penduduk"
                            value="{{ old('jumlah_penduduk') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('jumlah_penduduk') border-red-300 @enderror"
                            placeholder="Contoh: 1500" min="0">
                        @error('jumlah_penduduk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Opsional - untuk analisis statistik</p>
                    </div>

                    <div>
                        <!-- Empty space for grid alignment -->
                    </div>
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan Tambahan
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('keterangan') border-red-300 @enderror"
                        placeholder="Keterangan khusus tentang desa (opsional)...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Posyandu Terpilih -->
            <div id="posyandu-info" class="bg-green-50 border border-green-200 rounded-lg p-4" style="display: none;">
                <h6 class="text-sm font-medium text-green-800 mb-2">Informasi Posyandu Terpilih</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-green-700">Nama:</span>
                        <p class="text-green-600" id="info-posyandu-nama">-</p>
                    </div>
                    <div>
                        <span class="font-medium text-green-700">Area:</span>
                        <p class="text-green-600" id="info-posyandu-area">-</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="font-medium text-green-700">Alamat:</span>
                        <p class="text-green-600" id="info-posyandu-alamat">-</p>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Informasi</p>
                        <p class="text-xs text-blue-700 mt-1">
                            Desa akan dikaitkan dengan posyandu yang melayaninya. Pastikan area desa sesuai dengan area posyandu.
                            Data ini akan digunakan untuk registrasi balita dan analisis pemetaan stunting.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('balita.master-desa.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Desa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto capitalize names
        const namaInput = document.getElementById('nama_desa');
        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        // Handle area and posyandu dependencies
        const areaSelect = document.getElementById('area');
        const posyanduSelect = document.getElementById('master_posyandu_id');
        const posyanduInfo = document.getElementById('posyandu-info');

        if (areaSelect && posyanduSelect) {
            areaSelect.addEventListener('change', function() {
                const selectedArea = this.value;

                // Reset posyandu selection
                posyanduSelect.value = '';
                posyanduInfo.style.display = 'none';

                // Show/hide posyandu options based on area
                Array.from(posyanduSelect.options).forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                    } else {
                        const optionArea = option.getAttribute('data-area');
                        option.style.display = (selectedArea === '' || optionArea === selectedArea) ? 'block' : 'none';
                    }
                });
            });

            posyanduSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const posyanduName = selectedOption.textContent.split(' (')[0];
                    const posyanduArea = selectedOption.getAttribute('data-area');

                    // Show posyandu info
                    document.getElementById('info-posyandu-nama').textContent = posyanduName;
                    document.getElementById('info-posyandu-area').textContent = posyanduArea ? posyanduArea.charAt(0).toUpperCase() + posyanduArea.slice(1) : '-';
                    document.getElementById('info-posyandu-alamat').textContent = 'Loading...';

                    posyanduInfo.style.display = 'block';

                    // Auto-select area if not selected
                    if (!areaSelect.value && posyanduArea) {
                        areaSelect.value = posyanduArea;
                    }
                } else {
                    posyanduInfo.style.display = 'none';
                }
            });
        }

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nama = document.getElementById('nama_desa').value.trim();
                const area = document.getElementById('area').value;
                const posyandu = document.getElementById('master_posyandu_id').value;

                if (!nama) {
                    e.preventDefault();
                    alert('Nama desa wajib diisi');
                    document.getElementById('nama_desa').focus();
                    return false;
                }

                if (!area) {
                    e.preventDefault();
                    alert('Area wajib dipilih');
                    document.getElementById('area').focus();
                    return false;
                }

                if (!posyandu) {
                    e.preventDefault();
                    alert('Posyandu wajib dipilih');
                    document.getElementById('master_posyandu_id').focus();
                    return false;
                }

                return true;
            });
        }

        // Initialize on load if editing
        if (posyanduSelect.value) {
            posyanduSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection