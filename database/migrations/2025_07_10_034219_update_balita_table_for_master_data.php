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
            // Tambah foreign key ke master data
            $table->foreignId('master_posyandu_id')->nullable()->after('area')->constrained('master_posyandu');
            $table->foreignId('master_desa_id')->nullable()->after('master_posyandu_id')->constrained('master_desa');
            
            // Index untuk performa
            $table->index('master_posyandu_id');
            $table->index('master_desa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balita', function (Blueprint $table) {
            $table->dropForeign(['master_posyandu_id']);
            $table->dropForeign(['master_desa_id']);
            $table->dropColumn(['master_posyandu_id', 'master_desa_id']);
        });
    }
};
