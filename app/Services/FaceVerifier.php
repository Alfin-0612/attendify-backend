<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Pembungkus verifikasi wajah. Kontrak lengkap: docs/KONTRAK-FACE-SERVICE.md
 *
 * FACE_VERIFY_MODE=stub    -> selalu cocok (kecuali simulasi gagal), hanya untuk pengembangan.
 * FACE_VERIFY_MODE=service -> kirim ke layanan buatan tim Sains Data (FACE_SERVICE_URL).
 */
class FaceVerifier
{
    public function verify(User $user, UploadedFile $foto, bool $simulasiGagal = false): array
    {
        if (config('attendify.face_mode') === 'service') {
            return $this->viaService($user, $foto);
        }

        if ($simulasiGagal) {
            return ['cocok' => false, 'skor' => 0.2, 'alasan' => 'wajah_tidak_cocok'];
        }

        return ['cocok' => true, 'skor' => 0.99, 'alasan' => null];
    }

    private function viaService(User $user, UploadedFile $foto): array
    {
        $url = config('attendify.face_service_url');
        if (! $url) {
            return ['cocok' => false, 'skor' => null, 'alasan' => 'layanan_tidak_tersedia'];
        }

        try {
            $req = Http::timeout(20)
                ->attach('foto', file_get_contents($foto->getRealPath()), $foto->getClientOriginalName());

            // foto pendaftaran (referensi) bila sudah diunggah admin
            if ($user->foto_wajah && Storage::exists($user->foto_wajah)) {
                $req = $req->attach('foto_referensi', Storage::get($user->foto_wajah), basename($user->foto_wajah));
            }

            $res = $req->post($url, ['nisn' => $user->nisn, 'nama' => $user->name]);

            if ($res->successful()) {
                $cocok = (bool) $res->json('cocok');

                return [
                    'cocok' => $cocok,
                    'skor' => $res->json('skor'),
                    'alasan' => $cocok ? null : ($res->json('alasan') ?? 'wajah_tidak_cocok'),
                ];
            }
        } catch (\Throwable $e) {
            // jatuh ke bawah
        }

        return ['cocok' => false, 'skor' => null, 'alasan' => 'layanan_tidak_tersedia'];
    }
}
