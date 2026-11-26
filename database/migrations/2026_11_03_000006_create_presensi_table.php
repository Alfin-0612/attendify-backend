<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sesi_presensi_id')->constrained('sesi_presensi')->cascadeOnDelete();
            $table->dateTime('waktu_tercatat')->nullable();
            $table->string('status')->default('hadir');           // hadir | izin | alfa
            $table->float('skor_kecocokan')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'sesi_presensi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
