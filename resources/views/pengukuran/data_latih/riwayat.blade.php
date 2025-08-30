@extends('layouts.app')

@section('title', 'Riwayat Prediksi')
@section('page-title', 'Riwayat Prediksi Status Gizi (Random Forest)')

@section('content')
<div class="bg-white shadow rounded p-6">
    <h2 class="text-xl font-bold mb-4">Daftar Riwayat Prediksi</h2>

    @if($riwayat->isEmpty())
        <p class="text-gray-600">Belum ada data prediksi.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">Nama</th>
                    <th class="border px-4 py-2">Berat</th>
                    <th class="border px-4 py-2">Tinggi</th>
                    <th class="border px-4 py-2">Usia</th>
                    <th class="border px-4 py-2">Status Gizi</th>
                    <th class="border px-4 py-2">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->nama }}</td>
                    <td class="border px-4 py-2">{{ $item->berat }}</td>
                    <td class="border px-4 py-2">{{ $item->tinggi }}</td>
                    <td class="border px-4 py-2">{{ $item->usia }} bln</td>
                    <td class="border px-4 py-2">
                        <span class="px-2 py-1 rounded font-semibold
                            {{ strtolower($item->status_gizi) === 'stunting' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $item->status_gizi }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">{{ $item->created_at->translatedFormat('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
