<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalAdminController extends Controller
{
    public function index(Request $request): View
    {
        $daftarKelas = Kelas::orderBy('nama')->get();
        $kelas = $daftarKelas->firstWhere('id', (int) $request->query('kelas')) ?? $daftarKelas->first();

        $perHari = $kelas
            ? Jadwal::with('mapel', 'guru')
                ->where('kelas_id', $kelas->id)
                ->orderBy('hari')
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari')
            : collect();

        return view('panel.jadwal.index', compact('daftarKelas', 'kelas', 'perHari'));
    }

    public function create(Request $request): View
    {
        return view('panel.jadwal.form', $this->dataForm(null, (int) $request->query('kelas')));
    }

    public function store(Request $request): RedirectResponse
    {
        $jadwal = Jadwal::create($this->validasi($request));

        return redirect()
            ->route('panel.jadwal.index', ['kelas' => $jadwal->kelas_id])
            ->with('status', 'Jadwal ditambahkan.');
    }

    public function edit(Jadwal $jadwal): View
    {
        return view('panel.jadwal.form', $this->dataForm($jadwal, $jadwal->kelas_id));
    }

    public function update(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $jadwal->update($this->validasi($request));

        return redirect()
            ->route('panel.jadwal.index', ['kelas' => $jadwal->kelas_id])
            ->with('status', 'Jadwal diperbarui.');
    }

    public function destroy(Jadwal $jadwal): RedirectResponse
    {
        $kelasId = $jadwal->kelas_id;
        $jadwal->delete();

        return redirect()
            ->route('panel.jadwal.index', ['kelas' => $kelasId])
            ->with('status', 'Jadwal dihapus.');
    }

    private function dataForm(?Jadwal $jadwal, int $kelasId): array
    {
        return [
            'jadwal' => $jadwal,
            'kelasId' => $kelasId,
            'daftarKelas' => Kelas::orderBy('nama')->get(),
            'daftarMapel' => Mapel::orderBy('nama')->get(),
            'daftarGuru' => User::where('role', 'guru')->orderBy('name')->get(),
        ];
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'jenis' => 'required|in:pelajaran,break',
            'mapel_id' => 'required_if:jenis,pelajaran|nullable|exists:mapel,id',
            'guru_id' => 'nullable|exists:users,id',
            'nama_kegiatan' => 'required_if:jenis,break|nullable|string|max:100',
            'hari' => 'required|integer|between:1,5',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:50',
        ]);

        if ($data['jenis'] === 'break') {
            $data['mapel_id'] = null;
            $data['guru_id'] = null;
        } else {
            $data['nama_kegiatan'] = null;
        }

        return $data;
    }
}
