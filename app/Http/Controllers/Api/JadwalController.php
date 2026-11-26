<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Support\Semester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /** GET /api/jadwal?tanggal=2026-10-26 */
    public function index(Request $request): JsonResponse
    {
        $request->validate(['tanggal' => 'nullable|date']);

        $user = $request->user();
        $tanggal = Carbon::parse($request->query('tanggal', now()->toDateString()))->startOfDay();
        $hari = $tanggal->isoWeekday();

        $items = $hari > 5 ? collect() : Jadwal::with('mapel')
            ->where('kelas_id', $user->kelas_id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $pekan = Semester::pekanKe($tanggal);

        return response()->json([
            'semester' => config('attendify.semester_label'),
            'tanggal' => $tanggal->toDateString(),
            'tanggal_label' => $tanggal->copy()->locale('id')->translatedFormat('l, j F Y'),
            'hari_ini' => $tanggal->isToday(),
            'pertemuan_ke' => $pekan >= 1 ? $pekan : null,
            'jadwal' => $items->map(fn ($j) => [
                'jenis' => $j->jenis,
                'nama' => $j->nama,
                'mulai' => substr($j->jam_mulai, 0, 5),
                'selesai' => substr($j->jam_selesai, 0, 5),
                'ruang' => $j->ruang,
            ])->values(),
        ]);
    }

    /** GET /api/kalender?bulan=2026-10 */
    public function kalender(Request $request): JsonResponse
    {
        $request->validate(['bulan' => 'nullable|date_format:Y-m']);

        $user = $request->user();
        $bulan = Carbon::createFromFormat('!Y-m', $request->query('bulan', now()->format('Y-m')));

        $hariAdaPelajaran = Jadwal::where('kelas_id', $user->kelas_id)
            ->where('jenis', 'pelajaran')
            ->pluck('hari')->unique()->all();

        $hari = [];
        foreach (CarbonPeriod::create($bulan->copy()->startOfMonth(), $bulan->copy()->endOfMonth()) as $d) {
            $pekan = Semester::pekanKe($d);
            $iso = $d->isoWeekday();

            $hari[] = [
                'tanggal' => $d->toDateString(),
                'pekan_ke' => $pekan >= 1 ? $pekan : null,
                'ada_pertemuan' => $pekan >= 1 && in_array($iso, $hariAdaPelajaran, true),
                'akhir_pekan' => $iso > 5,
                'hari_ini' => $d->isToday(),
            ];
        }

        return response()->json([
            'semester' => config('attendify.semester_label'),
            'bulan' => $bulan->copy()->locale('id')->translatedFormat('F Y'),
            'hari' => $hari,
        ]);
    }
}
