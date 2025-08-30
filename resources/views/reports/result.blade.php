{{-- resources/views/reports/result.blade.php --}}
@extends('layouts.app')

@section('title', 'Hasil Laporan')
@section('page-title', 'Hasil Laporan')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Laporan {{ ucfirst($reportData['report_type']) }}
                </h2>
                <p class="text-sm text-gray-600">
                    Periode: {{ $reportData['period_start']->format('d F Y') }} - {{ $reportData['period_end']->format('d F Y') }}
                </p>
                <p class="text-xs text-gray-500">
                    Dibuat: {{ $reportData['generated_at']->format('d F Y H:i') }} oleh {{ $reportData['generated_by'] }}
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <form method="POST" action="{{ route('reports.export') }}" class="inline">
                    @csrf
                    <input type="hidden" name="report_type" value="{{ $reportData['report_type'] }}">
                    <input type="hidden" name="period_start" value="{{ $reportData['period_start']->format('Y-m-d') }}">
                    <input type="hidden" name="period_end" value="{{ $reportData['period_end']->format('Y-m-d') }}">
                    
                    @if($reportData['report_type'] == 'monthly')
                        <input type="hidden" name="month" value="{{ $reportData['period_start']->format('Y-m') }}">
                    @elseif($reportData['report_type'] == 'yearly')
                        <input type="hidden" name="year" value="{{ $reportData['period_start']->format('Y') }}">
                    @endif
                    
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </button>
                </form>
                
                <a href="{{ route('reports.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
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
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($reportData['stats']['total_balita']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stunting</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($reportData['stats']['stunting']) }}</p>
                    <p class="text-xs text-red-500">{{ $reportData['stats']['stunting_pct'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Berisiko</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($reportData['stats']['berisiko_stunting']) }}</p>
                    <p class="text-xs text-yellow-500">{{ $reportData['stats']['berisiko_pct'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Normal</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($reportData['stats']['normal']) }}</p>
                    <p class="text-xs text-green-500">{{ number_format(($reportData['stats']['normal'] / max($reportData['stats']['total_balita'], 1)) * 100, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren Bulanan</h3>
            <div class="h-64">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Status Gizi</h3>
            <div class="h-64">
                <canvas id="statusDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Data -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Data Detail</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balita</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Umur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posyandu</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reportData['predictions'] as $prediction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $prediction->pengukuran->balita->nama_balita }}</div>
                            <div class="text-sm text-gray-500">{{ $prediction->pengukuran->balita->jenis_kelamin == 'L' ? 'L' : 'P' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $prediction->pengukuran->tanggal_pengukuran->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $prediction->pengukuran->umur_bulan }} bulan
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($prediction->prediksi_status == 'stunting')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Stunting
                                </span>
                            @elseif($prediction->prediksi_status == 'berisiko_stunting')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Berisiko
                                </span>
                            @elseif($prediction->prediksi_status == 'normal')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Normal
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Gizi Lebih
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($prediction->confidence_level, 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $prediction->pengukuran->balita->posyandu }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json(collect($reportData['charts']['monthly_trend'])->pluck('month')),
            datasets: [{
                label: 'Stunting',
                data: @json(collect($reportData['charts']['monthly_trend'])->pluck('stunting')),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }, {
                label: 'Berisiko',
                data: @json(collect($reportData['charts']['monthly_trend'])->pluck('berisiko')),
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                tension: 0.4
            }, {
                label: 'Normal',
                data: @json(collect($reportData['charts']['monthly_trend'])->pluck('normal')),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Distribution Chart
    const distributionCtx = document.getElementById('statusDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: @json(collect($reportData['charts']['status_distribution'])->pluck('status')),
            datasets: [{
                data: @json(collect($reportData['charts']['status_distribution'])->pluck('count')),
                backgroundColor: [
                    'rgb(34, 197, 94)',    // Normal - Green
                    'rgb(245, 158, 11)',   // Berisiko - Yellow
                    'rgb(239, 68, 68)',    // Stunting - Red
                    'rgb(59, 130, 246)'    // Gizi Lebih - Blue
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection