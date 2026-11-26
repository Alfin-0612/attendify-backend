<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MapelAdminController extends Controller
{
    public function index(): View
    {
        $daftar = Mapel::withCount('jadwal')->orderBy('nama')->get();

        return view('panel.mapel.index', compact('daftar'));
    }

    public function store(Request $request): RedirectResponse
    {
        Mapel::create($this->validasi($request, null));

        return back()->with('status', 'Mata pelajaran ditambahkan.');
    }

    public function edit(Mapel $mapel): View
    {
        return view('panel.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, Mapel $mapel): RedirectResponse
    {
        $mapel->update($this->validasi($request, $mapel));

        return redirect()->route('panel.mapel.index')->with('status', 'Mata pelajaran diperbarui.');
    }

    public function destroy(Mapel $mapel): RedirectResponse
    {
        if ($mapel->jadwal()->exists()) {
            return back()->with('error', 'Mata pelajaran masih dipakai di jadwal. Hapus atau ganti jadwalnya terlebih dahulu.');
        }

        $mapel->delete();

        return back()->with('status', 'Mata pelajaran dihapus.');
    }

    private function validasi(Request $request, ?Mapel $mapel): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('mapel', 'nama')->ignore($mapel?->id)],
        ]);
    }
}
