@extends('layouts.app')

@section('title', 'Hasil Prediksi Random Forest')
@section('page-title', 'Hasil Prediksi Random Forest')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header dengan Info Perbandingan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Data Training (Fuzzy-AHP) -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">📊 Data Training (Fuzzy-AHP)</h3>
                @if(isset($distribution) && !empty($distribution))
                    @php
                        $totalTraining = array_sum($distribution);
                        $normalTraining = $distribution[0] ?? 0;
                        $beresikoTraining = $distribution[1] ?? 0;
                        $stuntingTraining = $distribution[2] ?? 0;
                    @endphp
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <div class="text-lg font-bold text-green-600">{{ $normalTraining }}</div>
                            <div class="text-gray-600">Normal</div>
                            <div class="text-gray-500">({{ $totalTraining > 0 ? round(($normalTraining/$totalTraining)*100,1) : 0 }}%)</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-yellow-600">{{ $beresikoTraining }}</div>
                            <div class="text-gray-600">Beresiko</div>
                            <div class="text-gray-500">({{ $totalTraining > 0 ? round(($beresikoTraining/$totalTraining)*100,1) : 0 }}%)</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-red-600">{{ $stuntingTraining }}</div>
                            <div class="text-gray-600">Stunting</div>
                            <div class="text-gray-500">({{ $totalTraining > 0 ? round(($stuntingTraining/$totalTraining)*100,1) : 0 }}%)</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Hasil Prediksi Random Forest -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">🤖 Hasil Prediksi Random Forest</h3>
                @if(isset($globalSummary))
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div>
                            <div class="text-lg font-bold text-green-600">{{ $globalSummary['total_normal'] }}</div>
                            <div class="text-gray-600">Normal</div>
                            <div class="text-gray-500">({{ $globalSummary['total_anak'] > 0 ? round(($globalSummary['total_normal']/$globalSummary['total_anak'])*100,1) : 0 }}%)</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-yellow-600">{{ $globalSummary['total_beresiko'] }}</div>
                            <div class="text-gray-600">Beresiko</div>
                            <div class="text-gray-500">({{ $globalSummary['total_anak'] > 0 ? round(($globalSummary['total_beresiko']/$globalSummary['total_anak'])*100,1) : 0 }}%)</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-red-600">{{ $globalSummary['total_stunting'] }}</div>
                            <div class="text-gray-600">Stunting</div>
                            <div class="text-gray-500">({{ $globalSummary['total_anak'] > 0 ? round(($globalSummary['total_stunting']/$globalSummary['total_anak'])*100,1) : 0 }}%)</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Alert jika ada perbedaan signifikan -->
        @if(isset($distribution) && isset($globalSummary))
            @php
                $diffStunting = abs(($distribution[2] ?? 0) - $globalSummary['total_stunting']);
                $diffBeresiko = abs(($distribution[1] ?? 0) - $globalSummary['total_beresiko']);
                $diffNormal = abs(($distribution[0] ?? 0) - $globalSummary['total_normal']);
                $significantDiff = $diffStunting > 2 || $diffBeresiko > 2 || $diffNormal > 2;
            @endphp
            @if($significantDiff)
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm">
                            <p class="font-medium text-yellow-800">Perbedaan Hasil Terdeteksi</p>
                            <p class="text-yellow-700 mt-1">
                                Model Random Forest memberikan hasil yang berbeda dari data training Fuzzy-AHP. 
                                Ini mungkin karena model perlu dilatih ulang atau ada masalah dalam proses training.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Filter dan Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-end">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Gizi</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="Stunting">Stunting</option>
                        <option value="Beresiko Stunting">Beresiko Stunting</option>
                        <option value="Normal">Normal</option>
                    </select>
                </div>

                <!-- Filter Area -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                    <select id="filterArea" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Area</option>
                        @foreach($grouped->keys() as $area)
                            <option value="{{ $area }}">{{ ucfirst($area) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Posyandu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Posyandu</label>
                    <select id="filterPosyandu" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Posyandu</option>
                        @foreach(collect($result)->pluck('posyandu')->unique()->sort() as $posyandu)
                            <option value="{{ $posyandu }}">{{ $posyandu }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama</label>
                    <input type="text" id="searchNama" placeholder="Masukkan nama balita..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex gap-2">
                <button id="resetFilter" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Reset Filter
                </button>
                <button id="exportData" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Summary per Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Ringkasan per Area</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Area</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Total</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-red-600">Stunting</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-yellow-600">Beresiko</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-green-600">Normal</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">% Gizi Buruk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($grouped as $area => $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($area) }}</td>
                        <td class="px-4 py-3 text-center">{{ $data['total'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $data['stunting'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $data['beresiko'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $data['normal'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $data['persentase_buruk'] > 50 ? 'text-red-600' : ($data['persentase_buruk'] > 30 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $data['persentase_buruk'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Data dengan Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Detail Data Balita</h2>
            <div class="text-sm text-gray-600">
                Total: <span id="totalCount">{{ count($result) }}</span> data
                | Ditampilkan: <span id="filteredCount">{{ count($result) }}</span> data
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto" id="dataTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nama Balita</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Area</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Posyandu</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Desa</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Status Gizi</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Confidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="dataTableBody">
                    @foreach ($result as $row)
                    @php
                        $statusClass = match(strtolower($row['status_gizi'])) {
                            'stunting' => 'bg-red-100 text-red-800',
                            'beresiko stunting' => 'bg-yellow-100 text-yellow-800',
                            'normal' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $confidenceClass = $row['confidence'] >= 80 ? 'text-green-600' : ($row['confidence'] >= 60 ? 'text-yellow-600' : 'text-red-600');
                    @endphp
                    <tr class="hover:bg-gray-50 data-row" 
                        data-status="{{ $row['status_gizi'] }}" 
                        data-area="{{ $row['area'] }}" 
                        data-posyandu="{{ $row['posyandu'] }}"
                        data-nama="{{ strtolower($row['nama']) }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $row['nama'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="capitalize">{{ $row['area'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $row['posyandu'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $row['desa'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $row['status_gizi'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold {{ $confidenceClass }}">{{ $row['confidence'] }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination placeholder for future enhancement -->
        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                Data diurutkan berdasarkan nama balita
            </div>
            <div class="flex gap-2">
                <a href="{{ route('prediksi.bulk.form') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    ← Kembali ke Form
                </a>
                <button onclick="window.print()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    🖨️ Print Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterStatus = document.getElementById('filterStatus');
    const filterArea = document.getElementById('filterArea');
    const filterPosyandu = document.getElementById('filterPosyandu');
    const searchNama = document.getElementById('searchNama');
    const resetFilter = document.getElementById('resetFilter');
    const dataRows = document.querySelectorAll('.data-row');
    const filteredCount = document.getElementById('filteredCount');
    
    function filterTable() {
        const statusValue = filterStatus.value.toLowerCase();
        const areaValue = filterArea.value.toLowerCase();
        const posyanduValue = filterPosyandu.value.toLowerCase();
        const namaValue = searchNama.value.toLowerCase();
        
        let visibleCount = 0;
        
        dataRows.forEach(row => {
            const rowStatus = row.dataset.status.toLowerCase();
            const rowArea = row.dataset.area.toLowerCase();
            const rowPosyandu = row.dataset.posyandu.toLowerCase();
            const rowNama = row.dataset.nama.toLowerCase();
            
            const statusMatch = !statusValue || rowStatus.includes(statusValue);
            const areaMatch = !areaValue || rowArea.includes(areaValue);
            const posyanduMatch = !posyanduValue || rowPosyandu.includes(posyanduValue);
            const namaMatch = !namaValue || rowNama.includes(namaValue);
            
            if (statusMatch && areaMatch && posyanduMatch && namaMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        filteredCount.textContent = visibleCount;
    }
    
    // Event listeners
    filterStatus.addEventListener('change', filterTable);
    filterArea.addEventListener('change', filterTable);
    filterPosyandu.addEventListener('change', filterTable);
    searchNama.addEventListener('input', filterTable);
    
    // Reset filter
    resetFilter.addEventListener('click', function() {
        filterStatus.value = '';
        filterArea.value = '';
        filterPosyandu.value = '';
        searchNama.value = '';
        filterTable();
    });
    
    // Export functionality (placeholder)
    document.getElementById('exportData').addEventListener('click', function() {
        // Create CSV data from visible rows
        let csvContent = 'Nama Balita,Area,Posyandu,Desa,Status Gizi,Confidence\n';
        
        document.querySelectorAll('.data-row:not([style*="none"])').forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = [
                cells[0].textContent.trim(),
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim()
            ];
            csvContent += rowData.map(field => `"${field}"`).join(',') + '\n';
        });
        
        // Download CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'hasil_prediksi_random_forest.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});

// Print styles
const printStyles = `
<style media="print">
    @page { margin: 1cm; }
    .no-print { display: none !important; }
    .print-break { page-break-before: always; }
    body { font-size: 12px; }
    table { font-size: 11px; }
    .bg-red-100 { background: #fef2f2 !important; -webkit-print-color-adjust: exact; }
    .bg-yellow-100 { background: #fffbeb !important; -webkit-print-color-adjust: exact; }
    .bg-green-100 { background: #f0fdf4 !important; -webkit-print-color-adjust: exact; }
    .text-red-600 { color: #dc2626 !important; -webkit-print-color-adjust: exact; }
    .text-yellow-600 { color: #d97706 !important; -webkit-print-color-adjust: exact; }
    .text-green-600 { color: #16a34a !important; -webkit-print-color-adjust: exact; }
</style>
`;

document.head.insertAdjacentHTML('beforeend', printStyles);
</script>
@endsection