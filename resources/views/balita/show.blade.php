{{-- resources/views/balita/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Balita')
@section('page-title', 'Detail Balita')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header dengan aksi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 {{ $balita->jenis_kelamin == 'L' ? 'bg-blue-100' : 'bg-pink-100' }} rounded-full flex items-center justify-center">
                    <span class="text-xl font-bold {{ $balita->jenis_kelamin == 'L' ? 'text-blue-600' : 'text-pink-600' }}">
                        {{ substr($balita->nama_balita, 0, 1) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $balita->nama_balita }}</h1>
                    <p class="text-sm text-gray-600">NIK: {{ $balita->nik_balita }}</p>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $balita->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                            {{ $balita->jenis_kelamin_label }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $balita->umur }} ({{ $balita->umur_bulan }} bulan)</span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('balita.edit', $balita) }}"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Data
                </a>
                
                <a href="{{ route('balita.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Informasi Utama -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Data Pribadi -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Data Pribadi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $balita->nama_balita }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <p class="text-sm text-gray-900 font-mono">{{ $balita->nik_balita }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <p class="text-sm text-gray-900">{{ $balita->tanggal_lahir->format('d F Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <p class="text-sm text-gray-900">{{ $balita->jenis_kelamin_label }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Umur</label>
                        <p class="text-sm text-gray-900">{{ $balita->umur }} <span class="text-gray-500">({{ $balita->umur_bulan }} bulan)</span></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua/Wali</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $balita->nama_orang_tua }}</p>
                    </div>
                </div>
            </div>

            <!-- Alamat Detail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Alamat Tempat Tinggal
                    @if($balita->isAddressComplete())
                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Lengkap
                    </span>
                    @else
                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Tidak Lengkap
                    </span>
                    @endif
                </h3>

                @if($balita->isAddressComplete())
                <!-- Alamat Format Baru (Detail) -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $balita->rt }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $balita->rw }}</p>
                        </div>
                        @if($balita->dusun)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dusun/Lingkungan</label>
                            <p class="text-sm text-gray-900">{{ $balita->dusun }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desa/Kelurahan</label>
                            <p class="text-sm text-gray-900 font-medium">{{ $balita->desa_kelurahan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                            <p class="text-sm text-gray-900">{{ $balita->kecamatan }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                            <p class="text-sm text-gray-900">{{ $balita->kabupaten }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <p class="text-sm text-gray-900 leading-relaxed">{{ $balita->formatted_address }}</p>
                    </div>
                </div>
                
                @elseif($balita->alamat_lengkap)
                <!-- Alamat Format Lama -->
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h6 class="text-sm font-medium text-yellow-800 mb-1">Format Alamat Lama</h6>
                                <p class="text-sm text-yellow-700">{{ $balita->alamat_lengkap }}</p>
                                <p class="text-xs text-yellow-600 mt-2">
                                    <em>Silahkan update alamat untuk mendapatkan format yang lebih detail dan terstruktur</em>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @else
                <!-- Alamat Belum Ada -->
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Alamat belum lengkap</h3>
                    <p class="mt-1 text-sm text-gray-500">Update data balita untuk melengkapi informasi alamat.</p>
                    <div class="mt-6">
                        <a href="{{ route('balita.edit', $balita) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Update Alamat
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Lokasi Posyandu -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Lokasi Posyandu
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                        <p class="text-sm text-gray-900 capitalize">{{ $balita->area_label ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Posyandu</label>
                        <p class="text-sm text-gray-900 font-medium">{{ $balita->posyandu ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desa Posyandu</label>
                        <p class="text-sm text-gray-900">{{ $balita->desa ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- QR Code -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    QR Code
                </h3>
                
                <div class="text-center">
                    @if ($balita->qr_code && Storage::disk('public')->exists('qr-codes/'.$balita->qr_code.'.png'))
                    <img src="{{ asset('storage/qr-codes/'.$balita->qr_code.'.png') }}" 
                         alt="QR Code {{ $balita->nama_balita }}" 
                         class="mx-auto w-32 h-32 border border-gray-200 rounded-lg">
                    
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('balita.download-qr', $balita) }}"
                           class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
                           <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                           </svg>
                           Download QR
                        </a>
                        
                        <form action="{{ route('balita.regenerate-qr', $balita) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200 text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Generate Ulang
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="py-8">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">QR Code belum tersedia</h3>
                        <p class="mt-1 text-sm text-gray-500">Generate QR Code untuk balita ini.</p>
                        
                        <div class="mt-4">
                            <form action="{{ route('balita.regenerate-qr', $balita) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Generate QR Code
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ringkasan Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Status Terkini
                </h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Pengukuran</span>
                        <span class="text-lg font-bold text-gray-900">{{ $balita->measurement_count }}</span>
                    </div>
                    
                    @php
                    $latestMeasurement = $balita->latestPengukuran;
                    @endphp
                    
                    @if($latestMeasurement)
                    <div class="border-t pt-4">
                        <div class="text-sm text-gray-600 mb-2">Pengukuran Terakhir</div>
                        <div class="text-xs text-gray-500 mb-3">{{ $latestMeasurement->tanggal_pengukuran->format('d F Y') }}</div>
                        
                        @if($latestMeasurement->prediksiGizi)
                        @php $prediksi = $latestMeasurement->prediksiGizi; @endphp
                        <div class="space-y-2">
                            @if($prediksi->prediksi_status == 'stunting')
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="text-sm font-medium text-red-800">Status Stunting</div>
                                <div class="text-xs text-red-600 mt-1">Memerlukan perhatian khusus</div>
                            </div>
                            @elseif($prediksi->prediksi_status == 'normal')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="text-sm font-medium text-green-800">Status Normal</div>
                                <div class="text-xs text-green-600 mt-1">Pertumbuhan sesuai standar</div>
                            </div>
                            @else
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="text-sm font-medium text-blue-800 capitalize">{{ str_replace('_', ' ', $prediksi->prediksi_status) }}</div>
                                <div class="text-xs text-blue-600 mt-1">Pantau perkembangan</div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <div class="text-sm font-medium text-gray-800">Data Pengukuran</div>
                            <div class="text-xs text-gray-600 mt-1">BB: {{ $latestMeasurement->berat_badan }} kg | TB: {{ $latestMeasurement->tinggi_badan }} cm</div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="border-t pt-4 text-center">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-sm text-gray-600 mt-2">Belum ada pengukuran</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Info Sistem -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Info Sistem
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Didaftarkan oleh</span>
                        <span class="text-gray-900">{{ $balita->user->name ?? 'Sistem' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal daftar</span>
                        <span class="text-gray-900">{{ $balita->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Update terakhir</span>
                        <span class="text-gray-900">{{ $balita->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    @if($balita->isAddressComplete())
                    <div class="pt-2 border-t">
                        <div class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm">Data alamat lengkap</span>
                        </div>
                    </div>
                    @else
                    <div class="pt-2 border-t">
                        <div class="flex items-center text-yellow-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm">Alamat perlu diupdate</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection