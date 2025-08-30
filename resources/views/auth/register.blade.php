{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Daftar Petugas Baru</h2>
    
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-300 @enderror"
                   placeholder="Masukkan nama lengkap"
                   required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('email') border-red-300 @enderror"
                   placeholder="email@example.com"
                   required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Posyandu Name -->
        <div>
            <label for="posyandu_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Posyandu</label>
            <input type="text" 
                   name="posyandu_name" 
                   id="posyandu_name" 
                   value="{{ old('posyandu_name') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('posyandu_name') border-red-300 @enderror"
                   placeholder="Contoh: Posyandu Melati"
                   required>
            @error('posyandu_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Village -->
        <div>
            <label for="village" class="block text-sm font-medium text-gray-700 mb-1">Nama Desa</label>
            <input type="text" 
                   name="village" 
                   id="village" 
                   value="{{ old('village') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('village') border-red-300 @enderror"
                   placeholder="Contoh: Desa Sukamaju"
                   required>
            @error('village')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
            <input type="text" 
                   name="phone" 
                   id="phone" 
                   value="{{ old('phone') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('phone') border-red-300 @enderror"
                   placeholder="08123456789">
            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('password') border-red-300 @enderror"
                   placeholder="Minimal 8 karakter"
                   required>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                   placeholder="Ulangi password"
                   required>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
            Daftar
        </button>
    </form>

    <!-- Login Link -->
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="font-medium text-purple-600 hover:text-purple-500">
                Masuk di sini
            </a>
        </p>
    </div>
</div>
@endsection