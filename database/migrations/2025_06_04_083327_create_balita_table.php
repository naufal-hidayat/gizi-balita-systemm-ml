<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('balita', function (Blueprint $table) {
            $table->id();
            $table->string('nama_balita')->comment('Nama lengkap balita');
            $table->string('nik_balita', 16)->unique()->comment('Nomor Induk Kependudukan balita');
            $table->date('tanggal_lahir')->comment('Tanggal lahir balita');
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L = Laki-laki, P = Perempuan');
            $table->string('nama_orang_tua')->comment('Nama orang tua/wali');
            $table->text('alamat')->comment('Alamat lengkap');
            $table->string('posyandu')->comment('Nama posyandu tempat pemantauan');
            $table->foreignId('user_id')->constrained('users')->comment('User yang mendaftarkan balita');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('nik_balita');
            $table->index('tanggal_lahir');
            $table->index('jenis_kelamin');
            $table->index('posyandu');
            $table->index('nama_balita');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('balita');
    }
};