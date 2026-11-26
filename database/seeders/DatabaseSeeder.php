<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengumuman;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- staf (password semuanya: password) ----------
        User::create([
            'name' => 'Administrator', 'username' => 'admin', 'email' => 'admin@attendify.test',
            'password' => 'password', 'role' => 'admin',
        ]);
        $wali = User::create([
            'name' => 'Budi Santoso, S.Pd', 'username' => 'wali', 'email' => 'wali@attendify.test',
            'password' => 'password', 'role' => 'wali',
        ]);
        $guru1 = User::create([
            'name' => 'Drs. Hadi Pranoto', 'username' => 'guru1', 'email' => 'guru1@attendify.test',
            'password' => 'password', 'role' => 'guru',
        ]);
        $guru2 = User::create([
            'name' => 'Sri Lestari, S.Pd', 'username' => 'guru2', 'email' => 'guru2@attendify.test',
            'password' => 'password', 'role' => 'guru',
        ]);
        $guru3 = User::create([
            'name' => 'Rina Marlina, S.Pd', 'username' => 'guru3', 'email' => 'guru3@attendify.test',
            'password' => 'password', 'role' => 'guru',
        ]);

        // ---------- kelas & mapel ----------
        $kelas = Kelas::create(['nama' => 'XII MIPA 1', 'wali_id' => $wali->id]);

        $mapel = [];
        foreach (['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris'] as $nama) {
            $mapel[$nama] = Mapel::create(['nama' => $nama]);
        }

        $guruMapel = [
            'Matematika' => $guru1->id,
            'Fisika' => $guru1->id,
            'Kimia' => $guru2->id,
            'Biologi' => $guru2->id,
            'Bahasa Indonesia' => $guru3->id,
            'Bahasa Inggris' => $guru3->id,
        ];

        // ---------- siswa ----------
        $ferris = User::create([
            'name' => 'Ferris Ali', 'username' => 'ferris', 'email' => 'ferris@attendify.test',
            'password' => 'password', 'nisn' => '5240411198', 'kelas_id' => $kelas->id,
            'tahun_ajaran' => 'Ganjil 2026/2027',
        ]);

        $daftarSiswa = [
            ['Aulia Rahma', 'aulia', '5240411201'],
            ['Bagas Pratama', 'bagas', '5240411202'],
            ['Citra Dewi', 'citra', '5240411203'],
            ['Dimas Saputra', 'dimas', '5240411204'],
            ['Eka Putri', 'eka', '5240411205'],
            ['Fajar Nugroho', 'fajar', '5240411206'],
        ];
        $lain = [];
        foreach ($daftarSiswa as [$nama, $username, $nisn]) {
            $lain[] = User::create([
                'name' => $nama, 'username' => $username, 'email' => $username.'@attendify.test',
                'password' => 'password', 'nisn' => $nisn, 'kelas_id' => $kelas->id,
                'tahun_ajaran' => 'Ganjil 2026/2027',
            ]);
        }

        // ---------- jadwal (k = kegiatan/break, p = pelajaran) ----------
        $hariData = [
            1 => [
                ['k', 'Apel pagi', '07:00', '07:30'],
                ['p', 'Matematika', '07:30', '09:00', 'Ruang 01'],
                ['k', 'Istirahat', '09:00', '09:30'],
                ['p', 'Fisika', '09:30', '11:00', 'Ruang 02'],
                ['p', 'Kimia', '11:00', '12:30', 'Laboratorium'],
                ['k', 'Ishoma', '12:30', '13:00'],
                ['p', 'Bahasa Indonesia', '13:00', '14:30', 'Ruang 01'],
                ['k', 'Bimbingan wali kelas', '14:30', '15:00'],
            ],
            2 => [
                ['k', 'Apel pagi', '07:00', '07:30'],
                ['p', 'Biologi', '07:30', '09:00', 'Ruang 03'],
                ['k', 'Istirahat', '09:00', '09:30'],
                ['p', 'Matematika', '09:30', '11:00', 'Ruang 01'],
                ['p', 'Bahasa Inggris', '11:00', '12:30', 'Ruang 02'],
                ['k', 'Ishoma', '12:30', '13:00'],
                ['p', 'Bahasa Indonesia', '13:00', '14:30', 'Ruang 01'],
            ],
            3 => [
                ['k', 'Apel pagi', '07:00', '07:30'],
                ['p', 'Fisika', '07:30', '09:00', 'Ruang 02'],
                ['k', 'Istirahat', '09:00', '09:30'],
                ['p', 'Kimia', '09:30', '11:00', 'Laboratorium'],
                ['p', 'Biologi', '11:00', '12:30', 'Ruang 03'],
                ['k', 'Ishoma', '12:30', '13:00'],
                ['p', 'Bahasa Inggris', '13:00', '14:30', 'Ruang 02'],
            ],
            4 => [
                ['k', 'Apel pagi', '07:00', '07:30'],
                ['p', 'Bahasa Inggris', '07:30', '09:00', 'Ruang 02'],
                ['k', 'Istirahat', '09:00', '09:30'],
                ['p', 'Matematika', '09:30', '11:00', 'Ruang 01'],
                ['p', 'Fisika', '11:00', '12:30', 'Ruang 02'],
                ['k', 'Ishoma', '12:30', '13:00'],
                ['p', 'Kimia', '13:00', '14:30', 'Laboratorium'],
            ],
            5 => [
                ['k', 'Apel pagi', '07:00', '07:30'],
                ['p', 'Biologi', '07:30', '09:00', 'Ruang 03'],
                ['k', 'Istirahat', '09:00', '09:30'],
                ['p', 'Bahasa Indonesia', '09:30', '11:00', 'Ruang 01'],
                ['p', 'Matematika', '11:00', '12:00', 'Ruang 01'],
            ],
        ];

        foreach ($hariData as $hari => $baris) {
            foreach ($baris as $b) {
                $pelajaran = $b[0] === 'p';
                Jadwal::create([
                    'kelas_id' => $kelas->id,
                    'mapel_id' => $pelajaran ? $mapel[$b[1]]->id : null,
                    'guru_id' => $pelajaran ? $guruMapel[$b[1]] : null,
                    'nama_kegiatan' => $pelajaran ? null : $b[1],
                    'jenis' => $pelajaran ? 'pelajaran' : 'break',
                    'hari' => $hari,
                    'jam_mulai' => $b[2],
                    'jam_selesai' => $b[3],
                    'ruang' => $b[4] ?? null,
                ]);
            }
        }

        // ---------- riwayat presensi: 27 hari kerja ----------
        // Ferris: 24 hadir, 2 izin, 1 alfa. Siswa lain: sebagian besar hadir.
        $i = 0;
        foreach (CarbonPeriod::create('2026-09-21', '2026-10-27') as $tgl) {
            if ($tgl->isoWeekday() > 5) {
                continue;
            }

            $pelajaran = Jadwal::where('kelas_id', $kelas->id)
                ->where('jenis', 'pelajaran')
                ->where('hari', $tgl->isoWeekday())
                ->orderBy('jam_mulai')->get();

            $jadwal = $pelajaran[$i % $pelajaran->count()];
            $buka = Carbon::parse($tgl->toDateString().' '.$jadwal->jam_mulai);

            $sesi = SesiPresensi::create([
                'jadwal_id' => $jadwal->id,
                'tanggal' => $tgl->toDateString(),
                'waktu_buka' => $buka,
                'batas_presensi' => $buka->copy()->addMinutes(15),
            ]);

            $statusFerris = in_array($i, [5, 16], true) ? 'izin' : ($i === 11 ? 'alfa' : 'hadir');
            $this->catat($ferris, $sesi, $buka, $statusFerris);

            foreach ($lain as $k => $sw) {
                $n = $k + 1;
                if (($i + $n * 3) % 13 === 0) {
                    $status = 'alfa';
                } elseif (($i + $n * 5) % 17 === 0) {
                    $status = 'izin';
                } else {
                    $status = 'hadir';
                }
                $this->catat($sw, $sesi, $buka, $status);
            }

            $i++;
        }

        // ---------- pengumuman ----------
        Pengumuman::create([
            'judul' => 'Presensi tepat waktu',
            'isi' => 'Jangan lupa melakukan presensi sebelum batas waktu yang tertera pada setiap sesi.',
            'tanggal' => '2026-10-26',
        ]);
        Pengumuman::create([
            'judul' => 'Ujian tengah semester',
            'isi' => 'Jadwal ujian tengah semester akan diumumkan oleh wali kelas.',
            'tanggal' => '2026-10-20',
        ]);
    }

    private function catat(User $siswa, SesiPresensi $sesi, Carbon $buka, string $status): void
    {
        Presensi::create([
            'user_id' => $siswa->id,
            'sesi_presensi_id' => $sesi->id,
            'status' => $status,
            'waktu_tercatat' => $status === 'hadir' ? $buka->copy()->addMinutes(2) : null,
            'skor_kecocokan' => $status === 'hadir' ? 0.95 : null,
        ]);
    }
}
