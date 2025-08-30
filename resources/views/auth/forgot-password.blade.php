{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Lupa Password</h2>
    
    <div class="mb-4 text-sm text-gray-600">
        Lupa password Anda? Tidak masalah. Berikan alamat email Anda dan kami akan mengirimkan link reset password.
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
    
    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf
        
        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-300 @enderror"
                   placeholder="Masukkan email Anda"
                   required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
            Kirim Link Reset Password
        </button>
    </form>

    <!-- Back to Login -->
    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-500">
            ← Kembali ke halaman login
        </a>
    </div>
</div>
@endsection