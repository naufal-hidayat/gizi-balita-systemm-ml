@extends('layouts.app')

@section('title', 'Hasil Prediksi Gizi Balita')
@section('page-title', 'Hasil Prediksi Gizi Balita')

@section('content')
<div class="container mx-auto px-4 py-6">

    <h2 class="text-xl font-semibold mb-4">Ringkasan per Area</h2>

    <table class="table-auto w-full border border-gray-200 mb-8">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Area</th>
                <th class="border px-4 py-2">Total Data</th>
                <th class="border px-4 py-2 text-red-600">Stunting</th>
                <th class="border px-4 py-2 text-yellow-600">Beresiko Stunting</th>
                <th class="border px-4 py-2 text-green-600">Normal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grouped as $area => $data)
            <tr>
                <td class="border px-4 py-2">{{ $area }}</td>
                <td class="border px-4 py-2">{{ $data['total'] }}</td>
                <td class="border px-4 py-2 text-red-600">{{ $data['stunting'] }} anak</td>
                <td class="border px-4 py-2 text-yellow-600">{{ $data['beresiko'] }} anak</td>
                <td class="border px-4 py-2 text-green-600">{{ $data['normal'] }} anak</td>
                
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="text-xl font-semibold mt-10 mb-4">Dominasi Status Gizi Buruk per Area</h2>
    <canvas id="chartPieArea" width="400" height="400" class="mx-auto mb-10"></canvas>

    <h2 class="text-xl font-semibold mb-4">Detail Data</h2>

    <table class="table-auto w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Area</th>
                <th class="border px-4 py-2">Posyandu</th>
                <th class="border px-4 py-2">Status Gizi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $row)
            @php
                $status = strtolower($row['status_gizi']);
                $colorClass = $status === 'stunting' ? 'text-red-600' : ($status === 'beresiko stunting' ? 'text-yellow-600' : 'text-green-600');
            @endphp
            <tr>
                <td class="border px-4 py-2">{{ $row['area'] }}</td>
                <td class="border px-4 py-2">{{ $row['posyandu'] }}</td>
                <td class="border px-4 py-2 {{ $colorClass }}">{{ $row['status_gizi'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        <a href="{{ route('prediksi.bulk.form') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
            ← Kembali
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tambahkan delay agar canvas benar-benar tersedia
        setTimeout(() => {
            const grouped = @json($grouped);

            // Log untuk debug
            console.log('Grouped data:', grouped);

            const labels = Object.keys(grouped).map(area => {
                const st = grouped[area].persentase_stunting;
                const br = grouped[area].persentase_beresiko;
                const nr = grouped[area].persentase_normal;

                let areaDominan = 'Normal';
                if (st >= br && st >= nr) areaDominan = 'Stunting';
                else if (br >= st && br >= nr) areaDominan = 'Beresiko Stunting';

                return `${area}: ${areaDominan}`;
            });

            let data = Object.keys(grouped).map(area => grouped[area].persentase_buruk);

            // Cegah semua nol (Chart.js tidak bisa render pie chart dari semua nol)
            if (data.every(val => val === 0)) {
                data = data.map(() => 1); // isikan nilai dummy
            }

            const canvas = document.getElementById('chartPieArea');
            if (!canvas) {
                console.error('⛔ Canvas dengan id chartPieArea tidak ditemukan!');
                return;
            }

            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Persentase Gizi Buruk (Stunting + Beresiko)',
                        data: data,
                        backgroundColor: ['#f87171', '#facc15', '#60a5fa', '#4ade80', '#a78bfa'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: context => `${context.label} - ${context.parsed}%`
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }, 300);
    });
</script>
@endpush
