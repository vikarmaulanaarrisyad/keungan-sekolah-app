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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('kelas_id');
            $table->string('nama_siswa');
            $table->integer('nisn_siswa')->default(0);
            $table->integer('nis_siswa')->default(0);
            $table->bigInteger('nik_siswa')->default(0);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('agama', ['1', '2', '3', '4', '5'])->default(1); // 1 : Islam
            $table->text('alamat_siswa')->nullable();
            $table->integer('rt')->default(0);
            $table->integer('rw')->default(0);
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->integer('kode_pos')->default(0);
            $table->string('jenis_tinggal')->nullable();
            $table->string('transportasi')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->integer('anakke')->default(1);
            $table->integer('jumlah_saudara')->default(0);
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
            $table->integer('berat_badan')->default(0);
            $table->integer('tinggi_badan')->default(0);
            $table->integer('lingkar_kepala')->default(0);
            $table->bigInteger('saldo')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
