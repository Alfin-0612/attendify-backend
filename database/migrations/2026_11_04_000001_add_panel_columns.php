<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // wali kelas pada tiap kelas
        Schema::table('kelas', function (Blueprint $table) {
            $table->foreignId('wali_id')->nullable()->after('nama')->constrained('users')->nullOnDelete();
        });

        // guru pengampu pada tiap jadwal
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('mapel_id')->constrained('users')->nullOnDelete();
        });

        // jejak koreksi manual pada presensi
        Schema::table('presensi', function (Blueprint $table) {
            $table->foreignId('dikoreksi_oleh')->nullable()->after('foto_path')->constrained('users')->nullOnDelete();
            $table->string('catatan')->nullable()->after('dikoreksi_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dikoreksi_oleh');
            $table->dropColumn('catatan');
        });
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guru_id');
        });
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('wali_id');
        });
    }
};
