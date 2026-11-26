<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapKelasController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $daftarKelas = $user->kelasQuery()->orderBy('nama')->get();
        $kelas = $daftarKelas->firstWhere('id', (int) $request->query('kelas')) ?? $daftarKelas->first();

        $baris = collect();

        if ($kelas) {
            $siswa = User::where('role', 'siswa')
                ->where('kelas_id', $kelas->id)
                ->orderBy('name')
                ->get();

            $hitung = [];
            $rows = Presensi::whereIn('user_id', $siswa->pluck('id'))
                ->selectRaw('user_id, status, count(*) as jumlah')
                ->groupBy('user_id', 'status')
                ->get();
            foreach ($rows as $r) {
                $hitung[$r->user_id][$r->status] = (int) $r->jumlah;
            }

            $baris = $siswa->map(function ($sw) use ($hitung) {
                $h = $hitung[$sw->id] ?? [];
                $hadir = $h['hadir'] ?? 0;
                $izin = $h['izin'] ?? 0;
                $alfa = $h['alfa'] ?? 0;
                $total = $hadir + $izin + $alfa;

                return [
                    'siswa' => $sw,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'alfa' => $alfa,
                    'total' => $total,
                    'persen' => $total ? (int) round($hadir / $total * 100) : 0,
                ];
            });
        }

        return view('panel.rekap', compact('user', 'daftarKelas', 'kelas', 'baris'));
    }
}
