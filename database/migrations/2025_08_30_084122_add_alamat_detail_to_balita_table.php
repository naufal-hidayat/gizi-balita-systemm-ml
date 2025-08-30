<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('balita', function (Blueprint $table) {
            // Tambah kolom alamat detail
            $table->string('rt', 3)->nullable()->comment('RT tempat tinggal');
            $table->string('rw', 3)->nullable()->comment('RW tempat tinggal'); 
            $table->string('dusun')->nullable()->comment('Nama dusun/lingkungan');
            $table->string('desa_kelurahan')->nullable()->comment('Nama desa/kelurahan');
            $table->string('kecamatan')->nullable()->comment('Nama kecamatan');
            $table->string('kabupaten')->nullable()->comment('Nama kabupaten/kota');
            
            // Rename kolom alamat lama untuk backward compatibility
            $table->renameColumn('alamat', 'alamat_lengkap');
            
            // Index untuk performa
            $table->index(['desa_kelurahan', 'kecamatan']);
            $table->index('kecamatan');
            $table->index('kabupaten');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balita', function (Blueprint $table) {
            $table->dropColumn([
                'rt', 'rw', 'dusun', 'desa_kelurahan', 
                'kecamatan', 'kabupaten'
            ]);
            
            $table->renameColumn('alamat_lengkap', 'alamat');
            
            $table->dropIndex(['desa_kelurahan', 'kecamatan']);
            $table->dropIndex(['kecamatan']);
            $table->dropIndex(['kabupaten']);
        });
    }
};
