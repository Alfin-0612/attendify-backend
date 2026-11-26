<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Kelola data siswa, termasuk pindah kelas dan foto wajah pendaftaran. */
class SiswaAdminController extends Controller
{
    public function index(Request $request): View
    {
        $daftarKelas = Kelas::orderBy('nama')->get();
        $q = trim((string) $request->query('q', ''));
        $kelasId = (int) $request->query('kelas');

        $siswa = User::with('kelas')
            ->where('role', 'siswa')
            ->when($kelasId, fn ($b) => $b->where('kelas_id', $kelasId))
            ->when($q !== '', fn ($b) => $b->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('nisn', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            }))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('panel.siswa.index', compact('daftarKelas', 'siswa', 'q', 'kelasId'));
    }

    public function create(): View
    {
        return view('panel.siswa.form', [
            'siswa' => null,
            'daftarKelas' => Kelas::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan(null));

        unset($data['foto_wajah']);
        if ($request->hasFile('foto_wajah')) {
            $data['foto_wajah'] = $request->file('foto_wajah')->store('wajah');
        }

        $data['role'] = 'siswa';
        $data['email'] = $data['username'].'@attendify.test';

        User::create($data);

        return redirect()->route('panel.siswa.index')->with('status', 'Siswa ditambahkan.');
    }

    public function edit(User $siswa): View
    {
        $this->pastikanSiswa($siswa);

        return view('panel.siswa.form', [
            'siswa' => $siswa,
            'daftarKelas' => Kelas::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, User $siswa): RedirectResponse
    {
        $this->pastikanSiswa($siswa);
        $data = $request->validate($this->aturan($siswa));

        if (empty($data['password'])) {
            unset($data['password']);
        }

        unset($data['foto_wajah']);
        if ($request->hasFile('foto_wajah')) {
            if ($siswa->foto_wajah) {
                Storage::delete($siswa->foto_wajah);
            }
            $data['foto_wajah'] = $request->file('foto_wajah')->store('wajah');
        }

        $data['email'] = $data['username'].'@attendify.test';

        $siswa->update($data);

        return redirect()->route('panel.siswa.index')->with('status', 'Data siswa diperbarui.');
    }

    public function destroy(User $siswa): RedirectResponse
    {
        $this->pastikanSiswa($siswa);

        if ($siswa->foto_wajah) {
            Storage::delete($siswa->foto_wajah);
        }
        $siswa->delete(); // riwayat presensinya ikut terhapus

        return back()->with('status', 'Siswa dihapus.');
    }

    /** Tampilkan foto wajah pendaftaran (hanya untuk admin yang login). */
    public function foto(User $siswa)
    {
        $this->pastikanSiswa($siswa);
        abort_unless($siswa->foto_wajah && Storage::exists($siswa->foto_wajah), 404);

        return Storage::response($siswa->foto_wajah);
    }

    private function pastikanSiswa(User $user): void
    {
        abort_unless($user->role === 'siswa', 404);
    }

    private function aturan(?User $siswa): array
    {
        $id = $siswa?->id;

        return [
            'name' => 'required|string|max:100',
            'username' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($id),
            ],
            'nisn' => [
                'required', 'string', 'max:20', 'regex:/^[0-9]+$/',
                Rule::unique('users', 'nisn')->ignore($id),
            ],
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran' => 'required|string|max:50',
            'password' => [$siswa ? 'nullable' : 'required', 'string', 'min:6', 'max:100'],
            'foto_wajah' => 'nullable|image|max:4096',
        ];
    }
}
