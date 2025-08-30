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
    Schema::table('data_latih', function (Blueprint $table) {
        $table->string('nama')->nullable();
        $table->float('lingkar_kepala')->nullable();
        $table->float('lingkar_lengan')->nullable();
        $table->boolean('asi_eksklusif')->nullable();
        $table->boolean('imunisasi_lengkap')->nullable();
        $table->boolean('riwayat_penyakit')->nullable();
        $table->boolean('akses_air_bersih')->nullable();
        $table->boolean('sanitasi_layak')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_latih', function (Blueprint $table) {
            //
        });
    }
};
