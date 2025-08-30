@extends('layouts.app')

@section('title', 'Dashboard - Sistem Prediksi Gizi Balita')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="inline-flex items-center">
    <span class="text-gray-500">Dashboard</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Balita -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Balita</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBalita) }}</p>
                        <p class="text-sm text-gray-500">Terdaftar</p>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 px-6 py-3">
                <div class="text-sm">
                    <a href="{{ route('balita.index') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua balita
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Pengukuran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pengukuran</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPengukuran) }}</p>
                        <p class="text-sm text-gray-500">Dilakukan</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 px-6 py-3">
                <div class="text-sm">
                    <a href="{{ route('pengukuran.index') }}" class="font-medium text-green-600 hover:text-green-500">
                        Lihat pengukuran
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Stunting -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Stunting</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($stuntingCount) }}</p>
                        <p class="text-sm text-gray-500">Kasus</p>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 px-6 py-3">
                <div class="text-sm">
                    <span class="font-medium text-red-600">
                        Perlu perhatian khusus
                        <span aria-hidden="true"> ⚠️</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Berisiko -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Berisiko Stunting</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($berisiko) }}</p>
                        <p class="text-sm text-gray-500">Kasus</p>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 px-6 py-3">
                <div class="text-sm">
                    <span class="font-medium text-yellow-600">
                        Perlu pemantauan
                        <span aria-hidden="true"> 👁️</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Tren Status Gizi (6 Bulan Terakhir)</h3>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Stunting
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Berisiko
                    </span>
                </div>
            </div>
            <div class="relative h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Distribusi Status Gizi</h3>
            <div class="relative h-64">
                <canvas id="distributionChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $normalCount }}</div>
                    <div class="text-sm text-gray-600">Normal</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-400">{{ $normalPercent }}%</div>
                    <div class="text-sm text-gray-600">Persentase</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Measurements -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Pengukuran Terbaru</h3>
                <a href="{{ route('pengukuran.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Lihat semua
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balita</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BB/TB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Prediksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentMeasurements as $measurement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ substr($measurement->balita?->nama_balita ?? '', 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $measurement->balita?->nama_balita ?? 'Balita tidak ditemukan' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if(($measurement->balita?->jenis_kelamin) === 'L') Laki-laki
                                        @elseif(($measurement->balita?->jenis_kelamin) === 'P') Perempuan
                                        @else —
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ optional($measurement->tanggal_pengukuran)->format('d/m/Y') ?? \Carbon\Carbon::parse($measurement->tanggal_pengukuran)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $measurement->umur_bulan }} bulan
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $measurement->berat_badan }} kg / {{ $measurement->tinggi_badan }} cm
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($measurement->prediksiGizi)
                            @php
                            $status = $measurement->prediksiGizi->prediksi_status;
                            $confidence = $measurement->prediksiGizi->confidence_level;
                            @endphp

                            @if($status == 'stunting')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Stunting ({{ number_format($confidence, 1) }}%)
                            </span>
                            @elseif($status == 'berisiko_stunting')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Berisiko ({{ number_format($confidence, 1) }}%)
                            </span>
                            @elseif($status == 'normal')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Normal ({{ number_format($confidence, 1) }}%)
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Gizi Lebih ({{ number_format($confidence, 1) }}%)
                            </span>
                            @endif
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Belum Diprediksi
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($measurement->balita)
                            <a href="{{ route('pengukuran.show', $measurement) }}"
                                class="text-blue-600 hover:text-blue-900">Detail</a>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengukuran</h3>
                                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data pengukuran balita.</p>
                                <div class="mt-6">
                                    <a href="{{ route('pengukuran.create') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tambah Pengukuran
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('balita.create') }}"
                class="relative group bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-lg overflow-hidden text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                <div class="relative z-10">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium">Tambah Balita</h4>
                    <p class="text-xs text-blue-100 mt-1">Daftarkan balita baru</p>
                </div>
            </a>

            <a href="{{ route('pengukuran.create') }}"
                class="relative group bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg overflow-hidden text-white hover:from-green-600 hover:to-green-700 transition-all duration-200">
                <div class="relative z-10">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium">Input Pengukuran</h4>
                    <p class="text-xs text-green-100 mt-1">Ukur & prediksi status gizi</p>
                </div>
            </a>

            <a href="{{ route('reports.index') }}"
                class="relative group bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg overflow-hidden text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                <div class="relative z-10">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium">Buat Laporan</h4>
                    <p class="text-xs text-purple-100 mt-1">Generate laporan bulanan</p>
                </div>
            </a>

            <a href="{{ route('api.export-eppgbm') }}"
                class="relative group bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-lg overflow-hidden text-white hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                <div class="relative z-10">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium">Export E-PPGBM</h4>
                    <p class="text-xs text-orange-100 mt-1">Kirim data ke nutrisionist</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- data dari server (aman) ---
        const months = @json($chartData['months'] ?? []);
        const stunting = @json($chartData['stunting'] ?? []);
        const risiko = @json($chartData['berisiko'] ?? []);
        const distData = @json([$normalCount, $berisiko, $stuntingCount]);

        // --- Trend Chart ---
        const trendEl = document.getElementById('trendChart');
        if (trendEl && window.Chart) {
            new Chart(trendEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                            label: 'Stunting',
                            data: stunting,
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Berisiko Stunting',
                            data: risiko,
                            borderColor: 'rgb(245, 158, 11)',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // --- Distribution Chart ---
        const distEl = document.getElementById('distributionChart');
        if (distEl && window.Chart) {
            new Chart(distEl.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Normal', 'Berisiko Stunting', 'Stunting'],
                    datasets: [{
                        data: distData,
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush