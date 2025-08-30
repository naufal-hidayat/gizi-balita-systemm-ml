@extends('layouts.app')

@section('title', 'QR Code - ' . $balita->nama_balita)
@section('page-title', 'QR Code Balita')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-6">
                    {{-- Avatar --}}
                    @php
                        $genderClass = $balita->jenis_kelamin == 'L'
                            ? 'bg-blue-100 text-blue-600'
                            : 'bg-pink-100 text-pink-600';
                    @endphp
                    <div class="w-16 h-16 rounded-full flex-shrink-0 flex items-center justify-center {{ $genderClass }}">
                        <span class="text-2xl font-bold">{{ substr($balita->nama_balita, 0, 1) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $balita->nama_balita }}</h1>
                        <p class="text-gray-600">NIK: {{ $balita->nik_balita ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $balita->jenis_kelamin_label }} &bull; {{ $balita->umur }} &bull; {{ $balita->posyandu }}</p>
                    </div>
                </div>

                {{-- Tombol Kembali --}}
                <a href="{{ route('balita.show', $balita) }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Main Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kolom Kiri: QR Code Display --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center justify-center text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code untuk Pengukuran Mobile</h3>

            @if($balita->qr_code)
                {{-- State 1: QR Code ada dan file gambar ditemukan --}}
                @if(Storage::disk('public')->exists("qr-codes/{$balita->qr_code}.png"))
                    <div class="flex flex-col items-center space-y-4">
                        <div class="p-4 bg-white border-2 border-dashed border-gray-200 rounded-lg">
                            <img src="{{ Storage::url("qr-codes/{$balita->qr_code}.png") }}" alt="QR Code {{ $balita->nama_balita }}" class="w-56 h-56 md:w-64 md:h-64 object-contain">
                        </div>
                        <p class="text-sm font-medium text-gray-700">Kode: <span class="font-mono">{{ $balita->qr_code }}</span></p>

                        {{-- Action Buttons --}}
                        <div class="flex items-center space-x-3 pt-2">
                            <a href="{{ route('balita.download-qr', $balita) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download
                            </a>
                            <form method="POST" action="{{ route('balita.regenerate-qr', $balita) }}">
                                @csrf
                                <button type="submit" onclick="return confirm('Anda yakin ingin generate ulang QR Code? Kode yang lama tidak akan berlaku lagi.')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Generate Ulang
                                </button>
                            </form>
                        </div>
                    </div>
                {{-- State 2: QR Code ada, tapi file gambar sedang diproses (belum ada) --}}
                @else
                    <div class="w-64 h-64 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center p-4">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-blue-500 mx-auto mb-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <h4 class="text-base font-medium text-gray-800">Memproses QR Code...</h4>
                            <p class="text-sm text-gray-500 mt-1">Halaman akan dimuat ulang secara otomatis.</p>
                        </div>
                    </div>
                @endif
            {{-- State 3: QR Code belum pernah dibuat sama sekali --}}
            @else
                <div class="w-full max-w-sm h-64 bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg flex items-center justify-center p-4">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">QR Code Belum Tersedia</h4>
                        <p class="text-sm text-gray-500 mb-4">Generate QR Code untuk memulai pengukuran via aplikasi mobile.</p>
                        <form method="POST" action="{{ route('balita.regenerate-qr', $balita) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Generate QR Code
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Informasi & Data --}}
        <div class="space-y-6">
            {{-- Petunjuk Penggunaan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Cara Penggunaan
                </h3>
                <ol class="space-y-4 text-sm">
                    @php
                        $steps = [
                            ['title' => 'Buka Aplikasi Mobile', 'description' => 'Pastikan aplikasi Posyandu Mobile sudah terinstall.'],
                            ['title' => 'Scan QR Code', 'description' => 'Arahkan kamera ke QR Code di samping.'],
                            ['title' => 'Input Pengukuran', 'description' => 'Data balita akan otomatis muncul, tinggal isi data pengukuran.'],
                            ['title' => 'Lihat Hasil Prediksi', 'description' => 'Sistem akan otomatis memprediksi status gizi balita.'],
                        ];
                    @endphp
                    @foreach ($steps as $index => $step)
                        <li class="flex items-start space-x-3">
                            <span class="w-6 h-6 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $loop->iteration }}</span>
                            <div>
                                <p class="font-medium text-gray-800">{{ $step['title'] }}</p>
                                <p class="text-gray-500">{{ $step['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Pengukuran Terakhir --}}
            @if($balita->latestPengukuran)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengukuran Terakhir</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Tanggal:</span> <span class="font-medium text-gray-800">{{ $balita->latestPengukuran->tanggal_pengukuran->format('d F Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Berat Badan:</span> <span class="font-medium text-gray-800">{{ $balita->latestPengukuran->berat_badan }} kg</span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Tinggi Badan:</span> <span class="font-medium text-gray-800">{{ $balita->latestPengukuran->tinggi_badan }} cm</span></div>
                        @if($balita->latestPengukuran->prediksiGizi)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Status Gizi:</span>
                                @php
                                    $status = $balita->latestPengukuran->prediksiGizi->prediksi_status;
                                    $badgeClass = [
                                        'stunting' => 'bg-red-100 text-red-800',
                                        'berisiko_stunting' => 'bg-yellow-100 text-yellow-800',
                                        'normal' => 'bg-green-100 text-green-800',
                                        'gizi_lebih' => 'bg-blue-100 text-blue-800',
                                    ][$status] ?? 'bg-gray-100 text-gray-800';
                                    $statusText = ucwords(str_replace('_', ' ', $status));
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('balita.show', $balita) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors">
                            Lihat semua riwayat &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Important Instructions --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <div>
                <h3 class="text-sm font-medium text-yellow-800">Penting untuk diperhatikan:</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>QR Code ini unik untuk <strong>{{ $balita->nama_balita }}</strong>.</li>
                        <li>Jangan bagikan QR Code ini ke pihak yang tidak berwenang.</li>
                        <li>Jika QR Code hilang atau bocor, segera lakukan "Generate Ulang".</li>
                        <li>Pastikan aplikasi mobile terkoneksi internet saat melakukan scan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(button, text) {
        navigator.clipboard.writeText(text).then(function() {
            const originalText = button.innerHTML;
            button.innerHTML = 'Disalin!';
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }).catch(function(err) {
            console.error('Gagal menyalin teks: ', err);
            alert('Gagal menyalin link');
        });
    }

    // Auto-refresh jika gambar QR code sedang diproses
    @if($balita->qr_code && !Storage::disk('public')->exists("qr-codes/{$balita->qr_code}.png"))
        setTimeout(() => {
            window.location.reload();
        }, 5000); // refresh setelah 5 detik
    @endif
</script>
@endpush