{{-- resources/views/settings/fuzzy.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengaturan Fuzzy-AHP')
@section('page-title', 'Pengaturan Fuzzy-AHP')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    <!-- AHP Criteria Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Bobot Kriteria AHP</h3>
                <p class="text-sm text-gray-600">Atur bobot untuk setiap kriteria dalam analisis AHP</p>
            </div>
            <div class="text-sm text-gray-500">
                Total bobot harus = 1.000
            </div>
        </div>

        <form method="POST" action="{{ route('settings.fuzzy.criteria') }}">
            @csrf
            
            <div class="space-y-4">
                @php $totalWeight = 0; @endphp
                @foreach($criteria as $index => $criterion)
                    @php $totalWeight += $criterion->weight; @endphp
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-blue-600">{{ $criterion->code }}</span>
                        </div>
                        
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $criterion->name }}</div>
                            <div class="text-sm text-gray-600">{{ $criterion->description }}</div>
                        </div>
                        
                        <div class="w-32">
                            <input type="hidden" name="criteria[{{ $index }}][id]" value="{{ $criterion->id }}">
                            <input type="number" 
                                   name="criteria[{{ $index }}][weight]" 
                                   value="{{ $criterion->weight }}"
                                   step="0.001" 
                                   min="0" 
                                   max="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="w-20 text-center">
                            <span class="text-sm text-gray-600">{{ number_format($criterion->weight * 100, 1) }}%</span>
                        </div>
                        
                        <div class="w-16">
                            <input type="checkbox" 
                                   name="criteria[{{ $index }}][is_active]" 
                                   value="1"
                                   {{ $criterion->is_active ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-800">Total Bobot:</span>
                    <span class="text-lg font-bold text-blue-900" id="totalWeight">{{ number_format($totalWeight, 3) }}</span>
                </div>
                <div class="text-xs text-blue-600 mt-1">
                    Pastikan total bobot sama dengan 1.000 untuk hasil yang optimal
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Fuzzy Rules Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Aturan Fuzzy</h3>
                <p class="text-sm text-gray-600">Kelola aturan inferensi fuzzy untuk prediksi status gizi</p>
            </div>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                + Tambah Aturan
            </button>
        </div>

        <form method="POST" action="{{ route('settings.fuzzy.rules') }}">
            @csrf
            
            <div class="space-y-3">
                @foreach($rules as $index => $rule)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <input type="hidden" name="rules[{{ $index }}][id]" value="{{ $rule->id }}">
                                
                                <div class="font-medium text-gray-900 mb-2">{{ $rule->rule_name }}</div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <strong>IF:</strong> 
                                    @foreach($rule->conditions as $key => $value)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800 mr-1">
                                            {{ $key }} = {{ $value }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <div class="text-sm text-gray-600">
                                    <strong>THEN:</strong> 
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                        Status = {{ $rule->conclusion }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="w-24">
                                <label class="block text-xs text-gray-500 mb-1">Bobot</label>
                                <input type="number" 
                                       name="rules[{{ $index }}][weight]" 
                                       value="{{ $rule->weight }}"
                                       step="0.01" 
                                       min="0" 
                                       max="1"
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            
                            <div class="w-16 text-center">
                                <label class="block text-xs text-gray-500 mb-1">Aktif</label>
                                <input type="checkbox" 
                                       name="rules[{{ $index }}][is_active]" 
                                       value="1"
                                       {{ $rule->is_active ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Aturan
                </button>
            </div>
        </form>
    </div>

    <!-- System Info -->
    {{-- <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sistem</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
            <div>
                <div class="font-medium text-gray-700">Total Kriteria Aktif</div>
                <div class="text-2xl font-bold text-blue-600">{{ $criteria->where('is_active', true)->count() }}</div>
            </div>
            
            <div>
                <div class="font-medium text-gray-700">Total Aturan Aktif</div>
                <div class="text-2xl font-bold text-green-600">{{ $rules->where('is_active', true)->count() }}</div>
            </div>
            
            <div>
                <div class="font-medium text-gray-700">Akurasi Model</div>
                <div class="text-2xl font-bold text-purple-600">87.3%</div>
                <div class="text-xs text-gray-500">Berdasarkan validasi terakhir</div>
            </div>
        </div>
    </div> --}}
</div>

<script>
// Auto-calculate total weight
document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[weight]')) {
        updateTotalWeight();
    }
});

function updateTotalWeight() {
    const weightInputs = document.querySelectorAll('input[name*="[weight]"]');
    let total = 0;
    
    weightInputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalWeight').textContent = total.toFixed(3);
    
    // Visual feedback
    const totalElement = document.getElementById('totalWeight');
    if (Math.abs(total - 1.0) < 0.001) {
        totalElement.className = 'text-lg font-bold text-green-900';
    } else {
        totalElement.className = 'text-lg font-bold text-red-900';
    }
}
</script>
@endsection