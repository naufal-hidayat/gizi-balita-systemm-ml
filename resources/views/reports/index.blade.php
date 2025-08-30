{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Generate Laporan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Report Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Buat Laporan Status Gizi</h3>

        <form method="POST" action="{{ route('reports.generate') }}" class="space-y-6">
            @csrf

            <!-- Report Type -->
            <div x-data="{ reportType: 'monthly' }">
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Laporan</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative">
                        <input type="radio" name="report_type" value="monthly" x-model="reportType" class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Bulanan</div>
                                    <div class="text-sm text-gray-500">Laporan per bulan</div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" name="report_type" value="yearly" x-model="reportType" class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Tahunan</div>
                                    <div class="text-sm text-gray-500">Laporan per tahun</div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" name="report_type" value="custom" x-model="reportType" class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Custom</div>
                                    <div class="text-sm text-gray-500">Rentang waktu bebas</div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Period Inputs -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Monthly -->
                    <div x-show="reportType === 'monthly'" class="md:col-span-2">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Pilih Bulan</label>
                        <input type="month" name="month" id="month" value="{{ date('Y-m') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Yearly -->
                    <div x-show="reportType === 'yearly'" class="md:col-span-2">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Pilih Tahun</label>
                        <select name="year" id="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Custom -->
                    <div x-show="reportType === 'custom'">
                        <label for="period_start" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="period_start" id="period_start"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div x-show="reportType === 'custom'">
                        <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="period_end" id="period_end"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-2">Format Output</label>
                    <select name="format" id="format" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="view">Tampilkan di Browser</option>
                        <option value="pdf">Download PDF</option>
                        {{-- <option value="excel">Download Excel</option> --}}
                        {{-- <option value="json">Export JSON (E-PPGBM)</option> --}}
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="include_charts" id="include_charts" value="1" checked
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="include_charts" class="ml-2 block text-sm text-gray-700">
                        Sertakan grafik dan visualisasi
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Generate Laporan
                </button>
            </div>
        </form>
    </div> 

    <!-- Quick Reports -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Laporan Cepat</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('reports.generate') }}?report_type=monthly&month={{ date('Y-m') }}&format=view"
                class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Bulan Ini</div>
                        <div class="text-sm text-gray-500">{{ date('F Y') }}</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('reports.generate') }}?report_type=yearly&year={{ date('Y') }}&format=view"
                class="p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Tahun Ini</div>
                        <div class="text-sm text-gray-500">{{ date('Y') }}</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('api.export-eppgbm') }}"
                class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Export E-PPGBM</div>
                        <div class="text-sm text-gray-500">CSV Format</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection