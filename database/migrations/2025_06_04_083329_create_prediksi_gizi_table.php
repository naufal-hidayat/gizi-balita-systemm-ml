<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prediksi_gizi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengukuran_id')->constrained('pengukuran')->onDelete('cascade');
            
            // Z-Scores
            $table->decimal('zscore_bb_u', 5, 2)->nullable()->comment('Z-Score Berat Badan per Umur');
            $table->decimal('zscore_tb_u', 5, 2)->nullable()->comment('Z-Score Tinggi Badan per Umur');
            $table->decimal('zscore_bb_tb', 5, 2)->nullable()->comment('Z-Score Berat Badan per Tinggi Badan');
            
            // Status Interpretations
            $table->enum('status_bb_u', ['gizi_buruk', 'gizi_kurang', 'gizi_baik', 'gizi_lebih'])->nullable();
            $table->enum('status_tb_u', ['sangat_pendek', 'pendek', 'normal', 'tinggi'])->nullable();
            $table->enum('status_bb_tb', ['sangat_kurus', 'kurus', 'normal', 'gemuk'])->nullable();
            
            // Fuzzy-AHP Results
            $table->json('fuzzy_weights')->nullable()->comment('Bobot AHP untuk setiap kriteria');
            $table->json('fuzzy_scores')->nullable()->comment('Skor fuzzy untuk setiap faktor');
            $table->decimal('final_score', 6, 3)->nullable()->comment('Skor akhir hasil agregasi Fuzzy-AHP');
            
            // Prediction Results
            $table->enum('prediksi_status', ['stunting', 'berisiko_stunting', 'normal', 'gizi_lebih'])->comment('Status gizi hasil prediksi');
            $table->decimal('confidence_level', 5, 1)->default(50)->comment('Tingkat kepercayaan prediksi (%)');
            $table->text('rekomendasi')->nullable()->comment('Rekomendasi tindakan');
            $table->enum('prioritas', ['tinggi', 'sedang', 'rendah'])->default('rendah')->comment('Prioritas penanganan');
            
            $table->timestamps();
            
            // Indexes
            $table->index('prediksi_status');
            $table->index('prioritas');
            $table->index(['prediksi_status', 'prioritas']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prediksi_gizi');
    }
};