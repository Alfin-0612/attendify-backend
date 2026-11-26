<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\SesiPresensi;
use Illuminate\Console\Command;

/**
 * Pengganti panel guru untuk demo: membuka sesi presensi SEKARANG.
 *   php artisan presensi:buka          (dibuka 30 menit)
 *   php artisan presensi:buka 60       (dibuka 60 menit)
 *   php artisan presensi:buka --reset  (hapus presensi hari ini supaya bisa dites ulang)
 */
class BukaSesiPresensi extends Command
{
    protected $signature = 'presensi:buka {menit=30} {--reset}';
    protected $description = 'Buka sesi presensi sekarang untuk demo';

    public function handle(): int
    {
        $kelas = Kelas::first();
        if (! $kelas) {
            $this->error('Data kelas belum ada. Jalankan: php artisan migrate:fresh --seed');
            return self::FAILURE;
        }

        $dasar = Jadwal::with('mapel')->where('kelas_id', $kelas->id)->where('jenis', 'pelajaran');

        $jadwal = (clone $dasar)->where('hari', now()->isoWeekday())->orderBy('jam_mulai')->first()
            ?? (clone $dasar)->orderBy('hari')->orderBy('jam_mulai')->first();

        if (! $jadwal) {
            $this->error('Belum ada jadwal pelajaran.');
            return self::FAILURE;
        }

        $sesi = SesiPresensi::updateOrCreate(
            ['jadwal_id' => $jadwal->id, 'tanggal' => now()->toDateString()],
            [
                'waktu_buka' => now(),
                'batas_presensi' => now()->addMinutes((int) $this->argument('menit')),
            ]
        );

        if ($this->option('reset')) {
            $sesi->presensi()->delete();
            $this->line('Presensi untuk sesi ini sudah dihapus.');
        }

        $this->info("Sesi dibuka: {$jadwal->nama} ({$kelas->nama}) sampai {$sesi->batas_presensi->format('H:i')} WIB. ID sesi: {$sesi->id}");

        return self::SUCCESS;
    }
}
