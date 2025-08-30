<!-- @extends('layouts.app')

@section('title', 'Data Latih Gizi')
@section('page-title', 'Data Latih')

@section('content')
<div class="bg-white rounded shadow p-4 mb-6">
    <h3 class="text-lg font-semibold mb-2">Statistik Status Gizi</h3>
    <canvas id="statusChart" height="100"></canvas>
</div>

<div class="bg-white rounded shadow p-4">
    <h3 class="text-lg font-semibold mb-4">Daftar Data Latih</h3>
    <table class="w-full table-auto text-sm text-left border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="px-4 py-2 border">Nama</th>
                <th class="px-4 py-2 border">Berat</th>
                <th class="px-4 py-2 border">Tinggi</th>
                <th class="px-4 py-2 border">Usia</th>
                <th class="px-4 py-2 border">Status Gizi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataLatih as $item)
            <tr class="border-t">
                <td class="px-4 py-2 border">{{ $item->nama }}</td>
                <td class="px-4 py-2 border">{{ $item->berat_badan }}</td>
                <td class="px-4 py-2 border">{{ $item->tinggi_badan }}</td>
                <td class="px-4 py-2 border">{{ $item->usia }}</td>
                <td class="px-4 py-2 border">
                    <span class="px-2 py-1 text-sm rounded 
                            {{ $item->status_gizi === 'Stunting' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $item->status_gizi ?? '-' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-3 text-center text-gray-500">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Stunting'],
            datasets: [{
                label: 'Jumlah',
                data: [{
                    {
                        $jumlahNormal
                    }
                }, {
                    {
                        $jumlahStunting
                    }
                }],
                backgroundColor: ['#22c55e', '#ef4444'],
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush -->