<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $hari = now()->isoWeekday();

        $jadwal = $hari > 5 ? collect() : $user->jadwalQuery()
            ->with('mapel', 'kelas')
            ->where('jenis', 'pelajaran')
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $sesi = SesiPresensi::whereIn('jadwal_id', $jadwal->pluck('id'))
            ->where('tanggal', now()->toDateString())
            ->get()
            ->keyBy('jadwal_id');

        $hitung = Presensi::whereIn('sesi_presensi_id', $sesi->pluck('id'))
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $stat = [
            'siswa' => User::where('role', 'siswa')
                ->whereIn('kelas_id', $user->kelasQuery()->select('id'))
                ->count(),
            'jadwal' => $jadwal->count(),
            'aktif' => $sesi->filter(fn ($s) => $s->aktif())->count(),
            'hadir' => (int) ($hitung['hadir'] ?? 0),
            'izin' => (int) ($hitung['izin'] ?? 0),
            'alfa' => (int) ($hitung['alfa'] ?? 0),
        ];

        return view('panel.dashboard', compact('user', 'jadwal', 'sesi', 'stat'));
    }
}
