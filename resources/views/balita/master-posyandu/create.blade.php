{{-- resources/views/master-posyandu/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Posyandu')
@section('page-title', 'Tambah Master Posyandu')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Master Posyandu Baru
            </h3>
            <p class="text-sm text-gray-600 mt-1">Masukkan data lengkap posyandu untuk sistem</p>
        </div>

        <form method="POST" action="{{ route('balita.master-posyandu.store') }}" class="space-y-6">
            @csrf

            <!-- Informasi Dasar -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Dasar</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_posyandu" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Posyandu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_posyandu" id="nama_posyandu"
                            value="{{ old('nama_posyandu') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_posyandu') border-red-300 @enderror"
                            placeholder="Contoh: Posyandu Melati Timur" required>
                        @error('nama_posyandu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                            Area <span class="text-red-500">*</span>
                        </label>
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
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Posyandu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat') border-red-300 @enderror"
                        placeholder="Alamat lengkap posyandu..." required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Informasi Kontak -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900 border-b border-gray-200 pb-2">Informasi Kontak</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="ketua_posyandu" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Ketua Posyandu
                        </label>
                        <input type="text" name="ketua_posyandu" id="ketua_posyandu"
                            value="{{ old('ketua_posyandu') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ketua_posyandu') border-red-300 @enderror"
                            placeholder="Nama lengkap ketua posyandu">
                        @error('ketua_posyandu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kontak" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Kontak
                        </label>
                        <input type="text" name="kontak" id="kontak"
                            value="{{ old('kontak') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kontak') border-red-300 @enderror"
                            placeholder="Contoh: 08123456789" maxlength="15">
                        @error('kontak')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            Setelah posyandu dibuat, Anda dapat menambahkan desa yang dilayani oleh posyandu ini.
                            Data posyandu akan digunakan untuk registrasi balita dan analisis stunting per area.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('balita.master-posyandu.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Posyandu</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto capitalize names
        const namaInput = document.getElementById('nama_posyandu');
        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        const ketuaInput = document.getElementById('ketua_posyandu');
        if (ketuaInput) {
            ketuaInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
            });
        }

        // Format phone number
        const kontakInput = document.getElementById('kontak');
        if (kontakInput) {
            kontakInput.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const nama = document.getElementById('nama_posyandu').value.trim();
                const area = document.getElementById('area').value;
                const alamat = document.getElementById('alamat').value.trim();

                if (!nama) {
                    e.preventDefault();
                    alert('Nama posyandu wajib diisi');
                    document.getElementById('nama_posyandu').focus();
                    return false;
                }

                if (!area) {
                    e.preventDefault();
                    alert('Area wajib dipilih');
                    document.getElementById('area').focus();
                    return false;
                }

                if (!alamat) {
                    e.preventDefault();
                    alert('Alamat posyandu wajib diisi');
                    document.getElementById('alamat').focus();
                    return false;
                }

                return true;
            });
        }
    });
</script>
@endsection