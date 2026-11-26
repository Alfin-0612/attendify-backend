<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengumumanAdminController extends Controller
{
    public function index(): View
    {
        $daftar = Pengumuman::orderByDesc('tanggal')->orderByDesc('id')->get();

        return view('panel.pengumuman', compact('daftar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'isi' => 'required|string|max:1000',
            'tanggal' => 'required|date',
        ]);

        Pengumuman::create($data);

        return back()->with('status', 'Pengumuman diterbitkan.');
    }

    public function destroy(Pengumuman $pengumuman): RedirectResponse
    {
        $pengumuman->delete();

        return back()->with('status', 'Pengumuman dihapus.');
    }
}
