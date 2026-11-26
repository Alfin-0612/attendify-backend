<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->string('nama_kegiatan')->nullable();          // untuk istirahat, apel, dll.
            $table->string('jenis')->default('pelajaran');        // pelajaran | break
            $table->unsignedTinyInteger('hari');                  // 1=Senin ... 5=Jumat
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
