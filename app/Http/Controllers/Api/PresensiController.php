<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Services\FaceVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /** Sesi presensi yang sedang dibuka untuk kelas siswa. */
    public function aktif(Request $request): JsonResponse
    {
        $user = $request->user();

        $sesi = SesiPresensi::with('jadwal.mapel', 'jadwal.kelas')
            ->whereHas('jadwal', fn ($q) => $q->where('kelas_id', $user->kelas_id))
            ->where('waktu_buka', '<=', now())
            ->where('batas_presensi', '>=', now())
            ->orderByDesc('waktu_buka')
            ->first();

        if (! $sesi) {
            return response()->json([
                'aktif' => false,
                'pesan' => 'Tidak ada sesi presensi saat ini.',
            ]);
        }

        return response()->json([
            'aktif' => true,
            'sudah_presensi' => Presensi::where('user_id', $user->id)
                ->where('sesi_presensi_id', $sesi->id)->exists(),
            'sesi' => $this->formatSesi($sesi),
        ]);
    }

    /** Terima foto wajah lalu catat presensi. */
    public function store(Request $request, FaceVerifier $face): JsonResponse
    {
        $data = $request->validate([
            'sesi_presensi_id' => 'required|integer|exists:sesi_presensi,id',
            'foto' => 'required|image|max:5120',
        ]);

        $user = $request->user();
        $sesi = SesiPresensi::with('jadwal.mapel', 'jadwal.kelas')->findOrFail($data['sesi_presensi_id']);

        if ((int) $sesi->jadwal->kelas_id !== (int) $user->kelas_id) {
            return response()->json(['status' => 'gagal', 'pesan' => 'Sesi ini bukan untuk kelas Anda.'], 403);
        }

        if (now()->lt($sesi->waktu_buka) || now()->gt($sesi->batas_presensi)) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Presensi sedang tidak dibuka atau sudah melewati batas waktu.',
            ], 422);
        }

        if (Presensi::where('user_id', $user->id)->where('sesi_presensi_id', $sesi->id)->exists()) {
            return response()->json(['status' => 'gagal', 'pesan' => 'Anda sudah presensi untuk sesi ini.'], 409);
        }

        // simulasi_gagal=1 hanya berpengaruh pada mode 'stub' (untuk menguji layar gagal di Flutter)
        $hasil = $face->verify($user, $request->file('foto'), $request->boolean('simulasi_gagal'));

        if (! $hasil['cocok']) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => $this->pesanGagal($hasil['alasan']),
                'penyebab' => [
                    'Wajah tertutup masker, topi, atau tidak berada di dalam oval.',
                    'Cahaya kurang atau terlalu silau. Coba pindah ke tempat yang lebih terang.',
                ],
            ], 422);
        }

        $path = $request->file('foto')->store('presensi');

        $presensi = Presensi::create([
            'user_id' => $user->id,
            'sesi_presensi_id' => $sesi->id,
            'waktu_tercatat' => now(),       // selalu waktu server
            'status' => 'hadir',
            'skor_kecocokan' => $hasil['skor'],
            'foto_path' => $path,
        ]);

        $mapel = $sesi->jadwal->nama;
        $jam = $presensi->waktu_tercatat->format('H:i');

        return response()->json([
            'status' => 'berhasil',
            'pesan' => "Tercatat pukul {$jam} WIB untuk {$mapel}. Terima kasih!",
            'waktu_tercatat' => $jam,
            'batas_presensi' => $sesi->batas_presensi->format('H:i'),
            'mapel' => $mapel,
            'kelas' => $sesi->jadwal->kelas->nama,
        ], 201);
    }

    private function pesanGagal(?string $alasan): string
    {
        return match ($alasan) {
            'layanan_tidak_tersedia' => 'Layanan pengenalan wajah sedang tidak tersedia. Coba lagi sebentar.',
            'wajah_tidak_terdeteksi' => 'Wajah tidak terdeteksi. Posisikan wajah di dalam oval.',
            'pencahayaan_buruk' => 'Cahaya kurang atau terlalu silau. Pindah ke tempat yang lebih terang.',
            'wajah_tertutup' => 'Wajah tertutup. Lepas masker atau topi lalu coba lagi.',
            'liveness_gagal' => 'Verifikasi keaslian wajah gagal. Gunakan wajah asli, bukan foto atau layar.',
            'foto_referensi_belum_ada' => 'Wajah Anda belum didaftarkan. Hubungi admin sekolah.',
            default => 'Wajah tidak dikenali. Pastikan wajah terlihat jelas, lalu coba lagi.',
        };
    }

    private function formatSesi(SesiPresensi $s): array
    {
        return [
            'id' => $s->id,
            'tanggal' => $s->tanggal->toDateString(),
            'kelas' => $s->jadwal->kelas->nama,
            'mapel' => $s->jadwal->nama,
            'waktu_presensi' => $s->waktu_buka->format('H:i'),
            'batas_presensi' => $s->batas_presensi->format('H:i'),
        ];
    }
}
