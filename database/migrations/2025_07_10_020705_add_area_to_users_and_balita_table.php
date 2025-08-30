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
        // Tambah kolom area ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('area', ['timur', 'barat', 'utara', 'selatan'])->nullable()->after('village');
        });

        // Tambah kolom area dan desa ke tabel balita
        Schema::table('balita', function (Blueprint $table) {
            $table->enum('area', ['timur', 'barat', 'utara', 'selatan'])->nullable()->after('posyandu');
            $table->string('desa')->nullable()->after('area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        Schema::table('balita', function (Blueprint $table) {
            $table->dropColumn(['area', 'desa']);
        });
    }
};
