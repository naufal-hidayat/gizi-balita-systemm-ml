<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengukuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->comment('Petugas yang melakukan pengukuran');
            
            // Basic measurement data
            $table->date('tanggal_pengukuran')->comment('Tanggal dilakukan pengukuran');
            $table->integer('umur_bulan')->comment('Umur balita dalam bulan saat pengukuran');
            
            // Anthropometric measurements
            $table->decimal('berat_badan', 5, 1)->comment('Berat badan dalam kg');
            $table->decimal('tinggi_badan', 5, 1)->comment('Tinggi badan dalam cm');
            $table->decimal('lingkar_kepala', 5, 1)->nullable()->comment('Lingkar kepala dalam cm');
            $table->decimal('lingkar_lengan', 5, 1)->nullable()->comment('Lingkar lengan atas dalam cm');
            
            // Health factors
            $table->enum('asi_eksklusif', ['ya', 'tidak'])->comment('Apakah mendapat ASI eksklusif');
            $table->enum('imunisasi_lengkap', ['ya', 'tidak', 'tidak_lengkap'])->comment('Status kelengkapan imunisasi');
            $table->text('riwayat_penyakit')->nullable()->comment('Riwayat penyakit balita');
            
            // Socioeconomic factors
            $table->bigInteger('pendapatan_keluarga')->comment('Pendapatan keluarga per bulan dalam rupiah');
            $table->enum('pendidikan_ibu', ['sd', 'smp', 'sma', 'diploma', 'sarjana'])->comment('Tingkat pendidikan ibu');
            $table->integer('jumlah_anggota_keluarga')->comment('Jumlah anggota keluarga');
            
            // Environmental factors
            $table->enum('akses_air_bersih', ['ya', 'tidak'])->comment('Akses ke air bersih');
            $table->enum('sanitasi_layak', ['ya', 'tidak'])->comment('Akses ke sanitasi layak');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('tanggal_pengukuran');
            $table->index('umur_bulan');
            $table->index(['balita_id', 'tanggal_pengukuran']);
            $table->index('asi_eksklusif');
            $table->index('imunisasi_lengkap');
            $table->index('pendidikan_ibu');
            $table->index('akses_air_bersih');
            $table->index('sanitasi_layak');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengukuran');
    }
};