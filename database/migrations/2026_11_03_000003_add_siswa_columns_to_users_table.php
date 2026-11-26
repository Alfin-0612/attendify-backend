<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('nisn')->nullable()->unique()->after('username');
            $table->foreignId('kelas_id')->nullable()->after('nisn')->constrained('kelas')->nullOnDelete();
            $table->string('role')->default('siswa')->after('kelas_id');
            $table->string('tahun_ajaran')->default('Ganjil 2026/2027')->after('role');
            $table->string('foto_wajah')->nullable()->after('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kelas_id');
            $table->dropColumn(['username', 'nisn', 'role', 'tahun_ajaran', 'foto_wajah']);
        });
    }
};
