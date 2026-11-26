<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SesiController extends Controller
{
    /** Daftar jadwal pelajaran pada suatu tanggal beserta status sesinya. */
    public function index(Request $request): View
    {
        $request->validate(['tanggal' => 'nullable|date']);

        $user = $request->user();
        $tanggal = Carbon::parse($request->query('tanggal', now()->toDateString()))->startOfDay();
        $hari = $tanggal->isoWeekday();

        $jadwal = $hari > 5 ? collect() : $user->jadwalQuery()
            ->with('mapel', 'kelas', 'guru')
            ->where('jenis', 'pelajaran')
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->orderBy('kelas_id')
            ->get();

        $sesi = SesiPresensi::whereIn('jadwal_id', $jadwal->pluck('id'))
            ->where('tanggal', $tanggal->toDateString())
            ->get()
            ->keyBy('jadwal_id');

        $ringkas = [];
        $baris = Presensi::whereIn('sesi_presensi_id', $sesi->pluck('id'))
            ->selectRaw('sesi_presensi_id, status, count(*) as jumlah')
            ->groupBy('sesi_presensi_id', 'status')
            ->get();
        foreach ($baris as $b) {
            $ringkas[$b->sesi_presensi_id][$b->status] = (int) $b->jumlah;
        }

        $jumlahSiswa = User::where('role', 'siswa')
            ->selectRaw('kelas_id, count(*) as jumlah')
            ->groupBy('kelas_id')
            ->pluck('jumlah', 'kelas_id');

        return view('panel.sesi.index', compact('user', 'jadwal', 'sesi', 'ringkas', 'jumlahSiswa', 'tanggal'));
    }

    /** Buka sesi presensi sekarang untuk satu jadwal. */
    public function buka(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->bolehMembuka($jadwal), 403);

        $data = $request->validate(['menit' => 'required|integer|min:1|max:240']);

        if (! $user->isAdmin() && (int) $jadwal->hari !== now()->isoWeekday()) {
            return back()->with('error', 'Sesi hanya dapat dibuka pada hari jadwalnya.');
        }

        SesiPresensi::updateOrCreate(
            ['jadwal_id' => $jadwal->id, 'tanggal' => now()->toDateString()],
            ['waktu_buka' => now(), 'batas_presensi' => now()->addMinutes((int) $data['menit'])],
        );

        return back()->with('status', "Sesi presensi {$jadwal->nama} dibuka selama {$data['menit']} menit.");
    }

    /** Tutup sesi yang sedang aktif. */
    public function tutup(Request $request, SesiPresensi $sesi): RedirectResponse
    {
        $sesi->load('jadwal');
        abort_unless($request->user()->bolehMembuka($sesi->jadwal), 403);

        if (! $sesi->aktif()) {
            return back()->with('error', 'Sesi ini tidak sedang aktif.');
        }

        $sesi->update(['batas_presensi' => now()]);

        return back()->with('status', 'Sesi presensi ditutup.');
    }

    /** Detail satu sesi: daftar siswa dan koreksi status. */
    public function show(Request $request, SesiPresensi $sesi): View
    {
        $user = $request->user();
        abort_unless($user->jadwalQuery()->whereKey($sesi->jadwal_id)->exists(), 403);

        $sesi->load('jadwal.mapel', 'jadwal.kelas', 'jadwal.guru');

        $siswa = User::where('role', 'siswa')
            ->where('kelas_id', $sesi->jadwal->kelas_id)
            ->orderBy('name')
            ->get();

        $presensi = Presensi::with('dikoreksiOleh')
            ->where('sesi_presensi_id', $sesi->id)
            ->get()
            ->keyBy('user_id');

        $ringkas = ['hadir' => 0, 'izin' => 0, 'alfa' => 0, 'belum' => 0];
        foreach ($siswa as $sw) {
            $status = $presensi->get($sw->id)?->status ?? 'belum';
            $ringkas[$status] = ($ringkas[$status] ?? 0) + 1;
        }

        return view('panel.sesi.show', compact('user', 'sesi', 'siswa', 'presensi', 'ringkas'));
    }

    /** Koreksi status presensi seorang siswa pada satu sesi. */
    public function koreksi(Request $request, SesiPresensi $sesi, User $siswa): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->jadwalQuery()->whereKey($sesi->jadwal_id)->exists(), 403);

        $sesi->load('jadwal');
        abort_unless(
            $siswa->role === 'siswa' && (int) $siswa->kelas_id === (int) $sesi->jadwal->kelas_id,
            404
        );

        $data = $request->validate([
            'status' => 'required|in:hadir,izin,alfa',
            'catatan' => 'nullable|string|max:255',
        ]);

        $ada = Presensi::where('user_id', $siswa->id)->where('sesi_presensi_id', $sesi->id)->first();

        Presensi::updateOrCreate(
            ['user_id' => $siswa->id, 'sesi_presensi_id' => $sesi->id],
            [
                'status' => $data['status'],
                'waktu_tercatat' => $data['status'] === 'hadir' ? ($ada?->waktu_tercatat ?? now()) : null,
                'dikoreksi_oleh' => $user->id,
                'catatan' => $data['catatan'] ?? null,
            ],
        );

        return back()->with('status', "Status {$siswa->name} diubah menjadi {$data['status']}.");
    }

    /** Tandai semua siswa yang belum presensi sebagai alfa (setelah sesi ditutup). */
    public function tandaiAlfa(Request $request, SesiPresensi $sesi): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->jadwalQuery()->whereKey($sesi->jadwal_id)->exists(), 403);

        if ($sesi->aktif()) {
            return back()->with('error', 'Tutup sesi terlebih dahulu sebelum menandai alfa.');
        }

        $sesi->load('jadwal');

        $sudah = Presensi::where('sesi_presensi_id', $sesi->id)->pluck('user_id')->all();
        $belum = User::where('role', 'siswa')
            ->where('kelas_id', $sesi->jadwal->kelas_id)
            ->whereNotIn('id', $sudah)
            ->get();

        foreach ($belum as $sw) {
            Presensi::create([
                'user_id' => $sw->id,
                'sesi_presensi_id' => $sesi->id,
                'status' => 'alfa',
                'dikoreksi_oleh' => $user->id,
                'catatan' => 'Tidak melakukan presensi',
            ]);
        }

        return back()->with('status', $belum->count().' siswa ditandai alfa.');
    }
}
