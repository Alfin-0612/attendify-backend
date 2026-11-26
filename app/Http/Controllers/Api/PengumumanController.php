<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;

class PengumumanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Pengumuman::orderByDesc('tanggal')->limit(10)->get()->map(fn ($p) => [
                'id' => $p->id,
                'judul' => $p->judul,
                'isi' => $p->isi,
                'tanggal' => $p->tanggal->toDateString(),
            ])
        );
    }
}
