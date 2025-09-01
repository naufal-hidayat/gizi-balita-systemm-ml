@extends('layouts.app')

@section('title', 'Prediksi Random Forest')
@section('page-title', 'Prediksi Status Gizi Random Forest')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Prediksi Random Forest</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Sistem akan menggunakan data pengukuran nyata dengan hasil prediksi Fuzzy-AHP sebagai training data Random Forest.
                </p>
            </div>
        </div>
    </div>

    <!-- Real Data Status dari Database -->
    @if(isset($syncStatus))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h4 class="text-md font-semibold text-gray-900 mb-4">📊 Status Data Real Pengukuran</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Data Pengukuran Real dengan Prediksi Fuzzy -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium text-gray-700">📋 Data Pengukuran Real</h5>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Database Asli</span>
                    </div>
                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $syncStatus['fuzzy_total'] }}</div>
                    <div class="text-xs text-gray-600">Records dengan Prediksi Fuzzy-AHP</div>
                    
                    @if(!empty($syncStatus['fuzzy_distribution']))
                        <div class="mt-3 space-y-1">
                            @foreach($syncStatus['fuzzy_distribution'] as $status => $count)
                                @php
                                    $percentage = $syncStatus['fuzzy_total'] > 0 ? round(($count / $syncStatus['fuzzy_total']) * 100, 1) : 0;
                                    $statusName = match($status) {
                                        'normal' => 'Normal',
                                        'berisiko_stunting' => 'Beresiko',
                                        'stunting' => 'Stunting',
                                        'gizi_lebih' => 'Gizi Lebih',
                                        default => ucfirst($status)
                                    };
                                    $colorClass = match($status) {
                                        'normal' => 'text-green-600',
                                        'berisiko_stunting' => 'text-yellow-600',
                                        'stunting' => 'text-red-600',
                                        'gizi_lebih' => 'text-blue-600',
                                        default => 'text-gray-600'
                                    };
                                @endphp
                                <div class="flex justify-between items-center text-xs">
                                    <span class="{{ $colorClass }}">{{ $statusName }}</span>
                                    <span class="text-gray-600">{{ $count }} ({{ $percentage }}%)</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- CSV Training File Status -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-sm font-medium text-gray-700">📁 CSV Training File</h5>
                        <span class="px-2 py-1 {{ $csvExists ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }} text-xs rounded-full">
                            {{ $csvExists ? 'Siap Digunakan' : 'Perlu Sinkronisasi' }}
                        </span>
                    </div>
                    <div class="text-2xl font-bold {{ $csvExists ? 'text-blue-600' : 'text-red-600' }} mb-1">
                        {{ $csvExists ? $dataCount : 0 }}
                    </div>
                    <div class="text-xs text-gray-600">Records Training Data</div>
                    
                    @if($lastUpdated)
                        <div class="text-xs text-gray-500 mt-2">
                            Terakhir diperbarui: {{ date('d/m/Y H:i', $lastUpdated) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Auto Sync Action -->
            {{-- @if($syncStatus['fuzzy_total'] > 0)
                <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-green-800">✨ Data Real Tersedia untuk Training</p>
                            <p class="text-xs text-green-700 mt-1">
                                Sistem akan mengambil {{ $syncStatus['fuzzy_total'] }} data pengukuran nyata dengan hasil prediksi Fuzzy-AHP 
                                dan mengkonversinya menjadi format CSV untuk training Random Forest. Data ini adalah data real dari pengukuran anak di lapangan.
                            </p>
                            <form action="{{ route('prediksi.bulk.sync-fuzzy') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Sinkronkan Data Real ke CSV Training
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Tidak Ada Data Pengukuran</p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Belum ada data pengukuran dengan hasil prediksi Fuzzy-AHP yang tersedia. 
                                Lakukan pengukuran terlebih dahulu di sistem Fuzzy-AHP.
                            </p>
                        </div>
                    </div>
                </div>
            @endif --}}
        </div>
    @endif

    <!-- Status Data Training -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">🎯 Status Data Training</h4>
        
        @if($csvExists)
            @php
                $total = array_sum($statusDistribution);
                $normalCount = $statusDistribution[0] ?? 0;
                $beresikoCount = $statusDistribution[1] ?? 0;
                $stuntingCount = $statusDistribution[2] ?? 0;
                
                $normalPct = $total > 0 ? round(($normalCount / $total) * 100, 1) : 0;
                $beresikoPct = $total > 0 ? round(($beresikoCount / $total) * 100, 1) : 0;
                $stuntingPct = $total > 0 ? round(($stuntingCount / $total) * 100, 1) : 0;
            @endphp
            
            <!-- Data Quality Assessment -->
            @if($dataQuality === 'good')
                <div class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">🎉 Data training berkualitas baik dari data real!</p>
                        <p class="text-xs text-green-600">
                            {{ $dataCount }} records dari pengukuran nyata dengan distribusi seimbang
                        </p>
                    </div>
                </div>
            @elseif(in_array($dataQuality, ['imbalanced', 'no_stunting', 'very_low_stunting']))
                <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">⚠️ Data tidak seimbang - Perlu lebih banyak data</p>
                        <p class="text-xs text-yellow-600">
                            {{ $dataCount }} records dari data real • Stunting: {{ $stuntingCount }} ({{ $stuntingPct }}%) • Beresiko: {{ $beresikoCount }} ({{ $beresikoPct }}%) • Normal: {{ $normalCount }} ({{ $normalPct }}%)
                        </p>
                        <p class="text-xs text-yellow-600 mt-1">
                            💡 Lakukan lebih banyak pengukuran untuk mendapatkan data yang lebih beragam
                        </p>
                    </div>
                </div>
            @else
                <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-lg mb-4">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">❌ Data training bermasalah</p>
                        <p class="text-xs text-red-600">
                            {{ $dataCount }} records • Kualitas: {{ $dataQuality }}
                        </p>
                    </div>
                </div>
            @endif
            
            <!-- Data Distribution Chart -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h5 class="text-sm font-medium text-gray-700 mb-3">📊 Distribusi Data Training (Data Real)</h5>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $normalCount }}</div>
                        <div class="text-xs text-gray-600">Normal ({{ $normalPct }}%)</div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $normalPct }}%"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $beresikoCount }}</div>
                        <div class="text-xs text-gray-600">Beresiko ({{ $beresikoPct }}%)</div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $beresikoPct }}%"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $stuntingCount }}</div>
                        <div class="text-xs text-gray-600">Stunting ({{ $stuntingPct }}%)</div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $stuntingPct }}%"></div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    ⏰ Terakhir disinkronkan: 
                    <?php 
                        date_default_timezone_set('Asia/Jakarta');
                        echo date('d/m/Y H:i', $lastUpdated); 
                    ?>
                    <span class="text-green-600 font-semibold">• Sumber: Data Real Pengukuran</span>
                </p>
            </div>
            <!-- FINAL RANDOM FOREST SOLUTION -->
            <div class="bg-blue-100 border-2 border-blue-500 rounded-lg p-4 mb-4">
                <form action="{{ route('prediksi.bulk.submit-final-rf') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-bold text-lg">
                        PREDIKSI RANDOM FOREST SEKARANG!!!
                    </button>
                </form>
                <p class="text-xs text-blue-700 mt-2">
                    <strong>COMPLETE SOLUTION:</strong> 1) Generate CSV persis sama, 2) Retrain model baru, 
                    3) Reload Flask API, 4) Prediksi dengan Random Forest yang benar. 
                    Dijamin distribusi akan SAMA: 17 Normal, 3 Beresiko, 48 Stunting.
                </p>
            </div>
            
            <!-- Action Buttons -->
            {{-- <div class="flex items-center justify-between pt-6 border-t border-gray-200"> --}}
                {{-- <div class="flex space-x-3"> --}}
                    <!-- Sync Ulang Button -->
                    {{-- @if(isset($syncStatus) && $syncStatus['fuzzy_total'] > 0)
                        <form action="{{ route('prediksi.bulk.sync-fuzzy') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg text-green-700 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Sync Data Terbaru
                            </button>
                        </form>
                    @endif --}}

                    <!-- Retrain Model Button -->
                    {{-- <form action="{{ route('prediksi.bulk.retrain') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-purple-300 rounded-lg text-purple-700 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            Latih Ulang Model
                        </button>
                    </form> --}}
                {{-- </div> --}}
                {{-- <div class="border-t pt-4 mt-4">
                    <h5 class="text-sm font-medium text-gray-700 mb-3">🔧 Debug & Troubleshoot</h5>
                    <div class="flex gap-2">
                        <a href="{{ route('prediksi.bulk.debug') }}" target="_blank"
                           class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-lg text-blue-700 hover:bg-blue-50 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Debug Data
                        </a>
                        
                        <form action="{{ route('prediksi.bulk.force-retrain') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-orange-300 rounded-lg text-orange-700 hover:bg-orange-50 text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Force Retrain
                            </button>
                        </form>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Gunakan debug jika hasil prediksi tidak sesuai dengan data training. 
                        Force retrain akan melatih ulang model dengan logging detail.
                    </p>
                </div> --}}
            {{-- </div> --}}

        @else
            <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-800">📁 CSV Training belum tersedia</p>
                    <p class="text-xs text-yellow-600">
                        Sinkronkan data pengukuran real untuk membuat file training Random Forest.
                    </p>
                </div>
            </div>

            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Siap untuk sinkronisasi data real</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Sistem akan menggunakan data pengukuran nyata dari database untuk training Random Forest.
                </p>
                <div class="mt-6">
                    @if(isset($syncStatus) && $syncStatus['fuzzy_total'] > 0)
                        <form action="{{ route('prediksi.bulk.sync-fuzzy') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Sinkronkan {{ $syncStatus['fuzzy_total'] }} Data Real
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Belum ada data pengukuran tersedia</p>
                            <a href="{{ route('pengukuran.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Lakukan Pengukuran Dulu
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Tutorial Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">🚀 Keunggulan Menggunakan Data Real</h4>
        <div class="space-y-3">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-semibold text-green-600">✓</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Data Pengukuran Asli</p>
                    <p class="text-xs text-gray-600">Menggunakan data pengukuran nyata dari anak-anak di lapangan, bukan data sintetis</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-semibold text-blue-600">✓</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Label Akurat dari Fuzzy-AHP</p>
                    <p class="text-xs text-gray-600">Setiap data sudah memiliki label status gizi yang diprediksi oleh sistem Fuzzy-AHP</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-xs font-semibold text-purple-600">✓</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Sinkronisasi Otomatis</p>
                    <p class="text-xs text-gray-600">Data otomatis dikonversi ke format CSV yang sesuai untuk training Random Forest</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto hide alerts after 8 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 8000);

// Add loading states for buttons
document.querySelectorAll('form button[type="submit"]').forEach(button => {
    button.addEventListener('click', function(e) {
        const form = this.closest('form');
        if (form) {
            setTimeout(() => {
                this.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                this.disabled = true;
            }, 100);
        }
    });
});
</script>
@endsection