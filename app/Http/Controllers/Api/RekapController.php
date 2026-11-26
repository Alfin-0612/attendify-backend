<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Support\Semester;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rows = Presensi::with('sesi.jadwal.mapel')
            ->where('user_id', $request->user()->id)
            ->get();

        $hitung = fn ($koleksi) => [
            'hadir' => $koleksi->where('status', 'hadir')->count(),
            'izin' => $koleksi->where('status', 'izin')->count(),
            'alfa' => $koleksi->where('status', 'alfa')->count(),
        ];

        $total = $rows->count();
        $ringkasan = $hitung($rows);

        // per pekan: M1..M6
        $perPekan = [];
        for ($i = 1; $i <= 6; $i++) {
            $perPekan[$i] = ['pekan' => "M{$i}", 'hadir' => 0, 'izin' => 0, 'alfa' => 0];
        }
        foreach ($rows as $r) {
            $p = Semester::pekanKe($r->sesi->tanggal);
            if (isset($perPekan[$p]) && isset($perPekan[$p][$r->status])) {
                $perPekan[$p][$r->status]++;
            }
        }

        // per mata pelajaran
        $perMapel = $rows->groupBy(fn ($r) => $r->sesi->jadwal->nama)
            ->map(function ($grup, $nama) {
                $hadir = $grup->where('status', 'hadir')->count();

                return [
                    'mapel' => $nama,
                    'hadir' => $hadir,
                    'total' => $grup->count(),
                    'persen' => $grup->count() ? round($hadir / $grup->count() * 100) : 0,
                ];
            })->values();

        return response()->json([
            'semester' => config('attendify.semester_label'),
            'ringkasan' => $ringkasan + [
                'total' => $total,
                'persen_hadir' => $total ? round($ringkasan['hadir'] / $total * 100) : 0,
            ],
            'per_pekan' => array_values($perPekan),
            'per_mapel' => $perMapel,
        ]);
    }
}
