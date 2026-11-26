<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Input izin/sakit (atau alfa) satu siswa untuk satu hari penuh. */
class IzinController extends Controller
{
    private function daftarSiswa(User $user)
    {
        return User::where('role', 'siswa')
            ->whereIn('kelas_id', $user->kelasQuery()->select('id'))
            ->with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('name')
            ->get();
    }

    public function form(Request $request): View
    {
        $user = $request->user();
        $siswa = $this->daftarSiswa($user);

        return view('panel.izin', compact('user', 'siswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'siswa_id' => 'required|integer',
            'tanggal' => 'required|date',
            'status' => 'required|in:izin,alfa',
            'catatan' => 'nullable|string|max:255',
        ]);

        $siswa = $this->daftarSiswa($user)->firstWhere('id', (int) $data['siswa_id']);
        if (! $siswa) {
            return back()->withInput()->with('error', 'Siswa tidak ditemukan pada kelas Anda.');
        }

        $tanggal = Carbon::parse($data['tanggal'])->startOfDay();
        if ($tanggal->isoWeekday() > 5) {
            return back()->withInput()->with('error', 'Tanggal tersebut jatuh pada akhir pekan.');
        }

        $jadwal = Jadwal::where('kelas_id', $siswa->kelas_id)
            ->where('jenis', 'pelajaran')
            ->where('hari', $tanggal->isoWeekday())
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwal->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada jadwal pelajaran pada hari itu.');
        }

        foreach ($jadwal as $j) {
            $buka = $tanggal->copy()->setTimeFromTimeString($j->jam_mulai);

            $sesi = SesiPresensi::firstOrCreate(
                ['jadwal_id' => $j->id, 'tanggal' => $tanggal->toDateString()],
                ['waktu_buka' => $buka, 'batas_presensi' => $buka->copy()->addMinutes(15)],
            );

            Presensi::updateOrCreate(
                ['user_id' => $siswa->id, 'sesi_presensi_id' => $sesi->id],
                [
                    'status' => $data['status'],
                    'waktu_tercatat' => null,
                    'dikoreksi_oleh' => $user->id,
                    'catatan' => $data['catatan'] ?? null,
                ],
            );
        }

        return redirect()
            ->route('panel.izin')
            ->with('status', "{$siswa->name} dicatat {$data['status']} pada {$jadwal->count()} sesi tanggal {$tanggal->toDateString()}.");
    }
}
