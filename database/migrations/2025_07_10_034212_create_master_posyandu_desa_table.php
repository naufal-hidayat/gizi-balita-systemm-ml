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
        // Tabel master posyandu
        Schema::create('master_posyandu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posyandu');
            $table->enum('area', ['timur', 'barat', 'utara', 'selatan']);
            $table->text('alamat')->nullable();
            $table->string('ketua_posyandu')->nullable();
            $table->string('kontak')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['area', 'is_active']);
            $table->index('nama_posyandu');
        });

        // Tabel master desa
        Schema::create('master_desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->enum('area', ['timur', 'barat', 'utara', 'selatan']);
            $table->foreignId('master_posyandu_id')->constrained('master_posyandu')->onDelete('cascade');
            $table->integer('jumlah_penduduk')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['area', 'is_active']);
            $table->index('nama_desa');
            $table->index('master_posyandu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_desa');
        Schema::dropIfExists('master_posyandu');
    }
};
