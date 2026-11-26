<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Kelola akun admin, guru, dan wali kelas. */
class StafAdminController extends Controller
{
    private const PERAN = ['admin', 'guru', 'wali'];

    public function index(): View
    {
        $daftar = User::whereIn('role', self::PERAN)
            ->withCount(['jadwalDiampu', 'kelasWali'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('panel.staf.index', compact('daftar'));
    }

    public function create(): View
    {
        return view('panel.staf.form', ['staf' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan(null));
        $data['email'] = $data['username'].'@attendify.test';

        User::create($data);

        return redirect()->route('panel.staf.index')->with('status', 'Akun staf ditambahkan.');
    }

    public function edit(User $staf): View
    {
        $this->pastikanStaf($staf);

        return view('panel.staf.form', compact('staf'));
    }

    public function update(Request $request, User $staf): RedirectResponse
    {
        $this->pastikanStaf($staf);
        $data = $request->validate($this->aturan($staf));

        if ($staf->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withInput()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri dari admin.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['email'] = $data['username'].'@attendify.test';

        // lepas keterkaitan lama bila peran berubah
        if ($staf->role !== $data['role']) {
            if ($staf->role === 'guru') {
                Jadwal::where('guru_id', $staf->id)->update(['guru_id' => null]);
            }
            if ($staf->role === 'wali') {
                Kelas::where('wali_id', $staf->id)->update(['wali_id' => null]);
            }
        }

        $staf->update($data);

        return redirect()->route('panel.staf.index')->with('status', 'Akun staf diperbarui.');
    }

    public function destroy(Request $request, User $staf): RedirectResponse
    {
        $this->pastikanStaf($staf);

        if ($staf->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staf->delete(); // guru_id / wali_id terkait menjadi kosong

        return back()->with('status', 'Akun staf dihapus.');
    }

    private function pastikanStaf(User $user): void
    {
        abort_unless(in_array($user->role, self::PERAN, true), 404);
    }

    private function aturan(?User $staf): array
    {
        return [
            'name' => 'required|string|max:100',
            'username' => [
                'required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($staf?->id),
            ],
            'role' => ['required', Rule::in(self::PERAN)],
            'password' => [$staf ? 'nullable' : 'required', 'string', 'min:6', 'max:100'],
        ];
    }
}
