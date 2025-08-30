{{-- resources/views/keluarga/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Keluarga - ' . $keluarga->nama_kepala_keluarga)
@section('page-title', 'Detail Keluarga')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Header Profile -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-blue-600">
                            {{ substr($keluarga->nama_kepala_keluarga, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $keluarga->nama_kepala_keluarga }}</h1>
                        <p class="text-gray-600">Kepala Keluarga</p>
                        <p class="text-sm text-gray-500">KK: {{ $keluarga->no_kk }} • NIK: {{ $keluarga->nik_kepala_keluarga }}</p>
                        <p class="text-sm text-gray-500">{{ $keluarga->alamat_lengkap }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    @if($keluarga->hasBalitaEligible())
                    <a href="{{ route('balita.create') }}?keluarga_id={{ $keluarga->id }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Daftarkan Balita
                    </a>
                    @endif
                    <a href="{{ route('keluarga.edit', $keluarga) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Data
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $keluarga->jumlah_anggota }}</div>
                    <div class="text-sm text-gray-600">Anggota Keluarga</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $keluarga->jumlah_balita }}</div>
                    <div class="text-sm text-gray-600">Balita Terdaftar</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $keluarga->balita_eligible_count }}</div>
                    <div class="text-sm text-gray-600">Bisa Jadi Balita</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $keluarga->total_pengukuran }}</div>
                    <div class="text-sm text-gray-600">Total Pengukuran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column - Informasi Keluarga -->
        <div class="space-y-6">
            
            <!-- Informasi Keluarga -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Keluarga</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">No KK:</span>
                        <span class="font-medium">{{ $keluarga->no_kk }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">NIK Kepala Keluarga:</span>
                        <span class="font-medium">{{ $keluarga->nik_kepala_keluarga }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Area:</span>
                        <span class="font-medium">{{ $keluarga->area_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Posyandu:</span>
                        <span class="font-medium">{{ $keluarga->masterPosyandu->nama_posyandu ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Desa:</span>
                        <span class="font-medium">{{ $keluarga->masterDesa->nama_desa ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium">
                            @if($keluarga->is_active)
                                <span class="text-green-600">Aktif</span>
                            @else
                                <span class="text-red-600">Nonaktif</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Terdaftar:</span>
                        <span class="font-medium">{{ $keluarga->created_at->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Alamat Lengkap</h3>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dusun/Kampung:</span>
                        <span class="font-medium">{{ $keluarga->dusun_kampung_blok }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">RT/RW:</span>
                        <span class="font-medium">{{ $keluarga->rt }}/{{ $keluarga->rw }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kelurahan/Desa:</span>
                        <span class="font-medium">{{ $keluarga->kelurahan_desa }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kecamatan:</span>
                        <span class="font-medium">{{ $keluarga->kecamatan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kabupaten/Kota:</span>
                        <span class="font-medium">{{ $keluarga->kabupaten_kota }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Provinsi:</span>
                        <span class="font-medium">{{ $keluarga->provinsi }}</span>
                    </div>
                    @if($keluarga->kode_pos)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kode Pos:</span>
                        <span class="font-medium">{{ $keluarga->kode_pos }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                
                <div class="space-y-3">
                    <button onclick="showAddAnggotaModal()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Anggota
                    </button>
                    
                    @if($keluarga->balita->count() > 0)
                    <a href="{{ route('balita.index') }}?keluarga_id={{ $keluarga->id }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lihat Balita
                    </a>
                    @endif
                    
                    <a href="{{ route('keluarga.edit', $keluarga) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Keluarga
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column - Anggota Keluarga -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Anggota Keluarga</h3>
                        <button onclick="showAddAnggotaModal()" 
                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($keluarga->anggotaKeluarga->where('is_active', true) as $anggota)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-medium
                                            {{ $anggota->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600' }}">
                                    {{ substr($anggota->nama_lengkap, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $anggota->nama_lengkap }}</h4>
                                    <p class="text-sm text-gray-500">{{ $anggota->hubungan_keluarga_label }}</p>
                                    <div class="flex items-center space-x-4 text-xs text-gray-400">
                                        <span>{{ $anggota->jenis_kelamin_label }}</span>
                                        <span>{{ $anggota->umur }} tahun</span>
                                        <span>NIK: {{ $anggota->nik }}</span>
                                    </div>
                                    @if($anggota->pekerjaan)
                                        <p class="text-xs text-gray-500 mt-1">{{ $anggota->pekerjaan }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <!-- Status Labels -->
                                @if($anggota->hubungan_keluarga === 'kepala_keluarga')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Kepala Keluarga
                                    </span>
                                @endif
                                
                                @if($anggota->is_balita)
                                    @if($anggota->balita)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Balita Terdaftar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Bisa Jadi Balita
                                        </span>
                                    @endif
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-1">
                                    @if($anggota->is_balita && !$anggota->balita)
                                    <a href="{{ route('balita.create') }}?anggota_id={{ $anggota->id }}" 
                                       class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                        Daftarkan Balita
                                    </a>
                                    @endif
                                    
                                    @if($anggota->balita)
                                    <a href="{{ route('balita.show', $anggota->balita) }}" 
                                       class="inline-flex items-center px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Lihat Balita
                                    </a>
                                    @endif
                                    
                                    <button onclick="editAnggota({{ $anggota->id }})" 
                                            class="inline-flex items-center px-2 py-1 text-xs border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                                        Edit
                                    </button>
                                    
                                    @if($anggota->hubungan_keluarga !== 'kepala_keluarga' && !$anggota->balita)
                                    <button onclick="removeAnggota({{ $anggota->id }})" 
                                            class="inline-flex items-center px-2 py-1 text-xs border border-red-300 text-red-700 rounded hover:bg-red-50">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada anggota keluarga</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan anggota keluarga.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Anggota -->
<div id="addAnggotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Anggota Keluarga</h3>
            </div>
            
            <form id="addAnggotaForm" method="POST" action="{{ route('keluarga.add-anggota', $keluarga) }}">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                            <input type="text" name="nik" maxlength="16" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="16 digit NIK">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="nama_lengkap" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nama lengkap">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" required max="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin *</label>
                            <select name="jenis_kelamin" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hubungan Keluarga *</label>
                            <select name="hubungan_keluarga" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="istri">Istri</option>
                                <option value="anak">Anak</option>
                                <option value="menantu">Menantu</option>
                                <option value="cucu">Cucu</option>
                                <option value="orangtua">Orang Tua</option>
                                <option value="mertua">Mertua</option>
                                <option value="famili_lain">Famili Lain</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Pekerjaan (opsional)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih --</option>
                                <option value="tidak_sekolah">Tidak Sekolah</option>
                                <option value="tidak_tamat_sd">Tidak Tamat SD</option>
                                <option value="sd">SD/Sederajat</option>
                                <option value="smp">SMP/Sederajat</option>
                                <option value="sma">SMA/Sederajat</option>
                                <option value="diploma">Diploma</option>
                                <option value="sarjana">Sarjana</option>
                                <option value="magister">Magister</option>
                                <option value="doktor">Doktor</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="hideAddAnggotaModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Tambah Anggota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddAnggotaModal() {
    document.getElementById('addAnggotaModal').classList.remove('hidden');
}

function hideAddAnggotaModal() {
    document.getElementById('addAnggotaModal').classList.add('hidden');
    document.getElementById('addAnggotaForm').reset();
}

function editAnggota(id) {
    // Implementasi edit anggota
    alert('Edit anggota ID: ' + id + ' - Fitur akan segera tersedia');
}

function removeAnggota(id) {
    if (confirm('Yakin ingin menghapus anggota keluarga ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/keluarga/{{ $keluarga->id }}/anggota/${id}/remove`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto format NIK
document.querySelector('input[name="nik"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 16) {
        this.value = this.value.slice(0, 16);
    }
});

// Auto capitalize names
document.querySelector('input[name="nama_lengkap"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());
});
</script>
@endsection