<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gizi-balita.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Petugas Posyandu Area Timur
        User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@posyandu-melati.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Melati Timur',
            'village' => 'Desa Sukamaju',
            'area' => 'timur',
            'phone' => '08123456789',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Nur Aini',
            'email' => 'aini@posyandu-mawar.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Mawar Timur',
            'village' => 'Desa Sejahtera',
            'area' => 'timur',
            'phone' => '08987654321',
            'email_verified_at' => now(),
        ]);

        // Petugas Posyandu Area Barat
        User::create([
            'name' => 'Dewi Sartika',
            'email' => 'dewi@posyandu-anggrek.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Anggrek Barat',
            'village' => 'Desa Bahagia',
            'area' => 'barat',
            'phone' => '08111222333',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Rina Kartika',
            'email' => 'rina@posyandu-dahlia.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Dahlia Barat',
            'village' => 'Desa Harmoni',
            'area' => 'barat',
            'phone' => '08444555666',
            'email_verified_at' => now(),
        ]);

        // Petugas Posyandu Area Utara
        User::create([
            'name' => 'Lestari Wulandari',
            'email' => 'lestari@posyandu-kenanga.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Kenanga Utara',
            'village' => 'Desa Maju',
            'area' => 'utara',
            'phone' => '08777888999',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Indah Permata',
            'email' => 'indah@posyandu-seruni.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Seruni Utara',
            'village' => 'Desa Damai',
            'area' => 'utara',
            'phone' => '08000111222',
            'email_verified_at' => now(),
        ]);

        // Petugas Posyandu Area Selatan
        User::create([
            'name' => 'Fitri Handayani',
            'email' => 'fitri@posyandu-cempaka.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Cempaka Selatan',
            'village' => 'Desa Tentram',
            'area' => 'selatan',
            'phone' => '08333444555',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ani Suryani',
            'email' => 'ani@posyandu-bougenville.id',
            'password' => Hash::make('password'),
            'role' => 'petugas_posyandu',
            'posyandu_name' => 'Posyandu Bougenville Selatan',
            'village' => 'Desa Rukun',
            'area' => 'selatan',
            'phone' => '08666777888',
            'email_verified_at' => now(),
        ]);
    }
}