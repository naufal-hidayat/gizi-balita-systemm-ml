{{-- resources/views/balita/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Balita')
@section('page-title', 'Data Balita')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900">Daftar Balita</h2>
            <p class="text-sm text-gray-600">Kelola data balita di {{ auth()->user()->isAdmin() ? 'seluruh sistem' : auth()->user()->posyandu_name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('balita.migrate-addresses') }}"
                class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 text-sm"
                onclick="return confirm('Migrate alamat lama ke format baru?')">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Migrate Alamat
            </a>
            @endif
            <a href="{{ route('balita.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Balita
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Balita</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $balita->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laki-laki</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $balita->where('jenis_kelamin', 'L')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-pink-100 rounded-lg">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Perempuan</p>
                    <p class="text-2xl font-bold text-pink-600">{{ $balita->where('jenis_kelamin', 'P')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Alamat Lengkap</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $balita->filter(function($child) { return $child->isAddressComplete(); })->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="space-y-4">
            <!-- Search -->
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama balita, NIK, nama orang tua, atau alamat..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @if(auth()->user()->isAdmin())
                <div>
                    <select name="posyandu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Posyandu</option>
                        @php
                        $posyandus = \App\Models\Balita::distinct()->pluck('posyandu');
                        @endphp
                        @foreach($posyandus as $posyandu)
                        <option value="{{ $posyandu }}" {{ request('posyandu') == $posyandu ? 'selected' : '' }}>
                            {{ $posyandu }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <select name="kecamatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kecamatan)
                        <option value="{{ $kecamatan }}" {{ request('kecamatan') == $kecamatan ? 'selected' : '' }}>
                            {{ $kecamatan }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Jenis Kelamin</option>
                        <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Cari
                    </button>
                </div>

                @if(request()->hasAny(['search', 'posyandu', 'kecamatan', 'gender']))
                <div>
                    <a href="{{ route('balita.index') }}" class="w-full inline-block text-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Reset
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balita</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orang Tua & Alamat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posyandu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($balita as $child)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 {{ $child->jenis_kelamin == 'L' ? 'bg-blue-100' : 'bg-pink-100' }} rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium {{ $child->jenis_kelamin == 'L' ? 'text-blue-600' : 'text-pink-600' }}">
                                        {{ substr($child->nama_balita, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $child->nama_balita }}</div>
                                    <div class="text-sm text-gray-500">{{ $child->nik_balita }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ $child->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ $child->tanggal_lahir->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($child->qr_code && Storage::disk('public')->exists('qr-codes/'.$child->qr_code.'.png'))
                            <div class="flex flex-col items-center space-y-2">
                                <img src="{{ asset('storage/qr-codes/'.$child->qr_code.'.png') }}" alt="QR" class="w-12 h-12">
                                <a href="{{ route('balita.download-qr', $child) }}" 
                                   class="text-xs text-blue-600 hover:text-blue-800 underline">Download</a>
                            </div>
                            @else
                            <form action="{{ route('balita.regenerate-qr', $child) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200 transition-colors">
                                    Generate QR
                                </button>
                            </form>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $child->nama_orang_tua }}</div>
                                
                                <!-- Alamat Baru (Detail) -->
                                @if($child->isAddressComplete())
                                <div class="text-sm text-gray-600 mt-1">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mr-1 mt-0.5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $child->short_address }}</div>
                                            @if($child->desa_kelurahan)
                                            <div class="text-xs text-gray-500">{{ $child->desa_kelurahan }}</div>
                                            @endif
                                            @if($child->kecamatan)
                                            <div class="text-xs text-gray-400">Kec. {{ $child->kecamatan }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Alamat Lama (Fallback) -->
                                @elseif($child->alamat_lengkap)
                                <div class="text-sm text-gray-600 mt-1">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mr-1 mt-0.5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="text-xs text-gray-500">{{ Str::limit($child->alamat_lengkap, 35) }}</div>
                                    </div>
                                </div>
                                @else
                                <div class="text-xs text-red-500 mt-1">Alamat belum lengkap</div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $child->umur_bulan }} bulan
                            <div class="text-xs text-gray-500">{{ floor($child->umur_bulan / 12) }} tahun {{ $child->umur_bulan % 12 }} bulan</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $child->posyandu }}</div>
                            @if($child->area)
                            <div class="text-xs text-gray-500 capitalize">{{ $child->area }}</div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $hasPengukuran = $child->pengukuran && $child->pengukuran->count() > 0;
                            $pengukuranTerakhir = $child->pengukuran ? $child->pengukuran->sortByDesc('tanggal_pengukuran')->first() : null;
                            @endphp

                            @if($hasPengukuran && $pengukuranTerakhir)
                            @php
                            $prediksiGizi = $pengukuranTerakhir->prediksiGizi ?? $pengukuranTerakhir->prediksi_gizi;
                            $tanggal = $pengukuranTerakhir->tanggal_pengukuran;
                            @endphp

                            @if($prediksiGizi && isset($prediksiGizi->prediksi_status))
                            @if($prediksiGizi->prediksi_status == 'stunting')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Stunting
                            </span>
                            @elseif($prediksiGizi->prediksi_status == 'gizi_lebih')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Gizi Lebih
                            </span>
                            @elseif($prediksiGizi->prediksi_status == 'normal')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Normal
                            </span>
                            @elseif($prediksiGizi->prediksi_status == 'gizi_kurang')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Gizi Kurang
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $prediksiGizi->prediksi_status)) }}
                            </span>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">{{ $tanggal->format('d/m/Y') }}</div>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Sudah Diukur
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ $tanggal->format('d/m/Y') }}</div>
                            @endif
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Belum Diukur
                            </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('balita.show', $child) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    Detail
                                </a>
                                <a href="{{ route('balita.edit', $child) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 border border-yellow-300 shadow-sm text-xs font-medium rounded text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors duration-200">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('balita.destroy', $child) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100 transition-colors duration-200"
                                        onclick="return confirm('Yakin ingin menghapus data balita ini? Semua data pengukuran juga akan terhapus.')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data balita</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data balita baru.</p>
                                <div class="mt-6">
                                    <a href="{{ route('balita.create') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tambah Balita
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        @if($balita->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <!-- Results Info -->
                <div class="flex items-center text-sm text-gray-700">
                    <span>Menampilkan</span>
                    <span class="font-medium mx-1">{{ $balita->firstItem() }}</span>
                    <span>sampai</span>
                    <span class="font-medium mx-1">{{ $balita->lastItem() }}</span>
                    <span>dari</span>
                    <span class="font-medium mx-1">{{ $balita->total() }}</span>
                    <span>data</span>
                </div>

                <!-- Pagination Links -->
                <div class="flex items-center justify-center sm:justify-end">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Previous Page Link --}}
                        @if ($balita->onFirstPage())
                        <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-1 hidden sm:block">Previous</span>
                        </span>
                        @else
                        <a href="{{ $balita->appends(request()->query())->previousPageUrl() }}"
                            class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-1 hidden sm:block">Previous</span>
                        </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                        $start = max(1, $balita->currentPage() - 2);
                        $end = min($balita->lastPage(), $balita->currentPage() + 2);
                        @endphp

                        {{-- Page Numbers --}}
                        @for($page = $start; $page <= $end; $page++)
                            @if ($page==$balita->currentPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                {{ $page }}
                            </span>
                            @else
                            <a href="{{ $balita->appends(request()->query())->url($page) }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                {{ $page }}
                            </a>
                            @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($balita->hasMorePages())
                            <a href="{{ $balita->appends(request()->query())->nextPageUrl() }}"
                                class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors duration-200">
                                <span class="mr-1 hidden sm:block">Next</span>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            @else
                            <span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <span class="mr-1 hidden sm:block">Next</span>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            @endif
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection