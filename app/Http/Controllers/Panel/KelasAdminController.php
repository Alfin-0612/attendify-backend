<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasAdminController extends Controller
{
    public function index(): View
    {
        $daftar = Kelas::with('wali')->withCount(['siswa', 'jadwal'])->orderBy('nama')->get();
        $daftarWali = User::where('role', 'wali')->orderBy('name')->get();

        return view('panel.kelas.index', compact('daftar', 'daftarWali'));
    }

    public function store(Request $request): RedirectResponse
    {
        Kelas::create($this->validasi($request, null));

        return back()->with('status', 'Kelas ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        $daftarWali = User::where('role', 'wali')->orderBy('name')->get();

        return view('panel.kelas.edit', compact('kelas', 'daftarWali'));
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($this->validasi($request, $kelas));

        return redirect()->route('panel.kelas.index')->with('status', 'Kelas diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->siswa()->exists()) {
            return back()->with('error', 'Kelas masih memiliki siswa. Pindahkan atau hapus siswanya terlebih dahulu.');
        }

        $kelas->delete(); // jadwal kelas ini ikut terhapus

        return back()->with('status', 'Kelas dihapus.');
    }

    private function validasi(Request $request, ?Kelas $kelas): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:50', Rule::unique('kelas', 'nama')->ignore($kelas?->id)],
            'wali_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'wali')],
        ]);
    }
}
