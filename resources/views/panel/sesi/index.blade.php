@extends('layouts.panel')
@section('title', 'Sesi Presensi')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Sesi Presensi</h1>
        <p class="text-sm text-slate-500">{{ $tanggal->copy()->locale('id')->translatedFormat('l, j F Y') }}</p>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}"
               class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
        <button class="bg-white border border-slate-300 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-slate-100">Tampilkan</button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
@if ($jadwal->isEmpty())
    <p class="p-6 text-sm text-slate-500">Tidak ada jadwal pelajaran pada tanggal ini.</p>
@else
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3">Jam</th>
                <th class="px-4 py-3">Mata pelajaran</th>
                <th class="px-4 py-3">Kelas</th>
                <th class="px-4 py-3">Guru</th>
                <th class="px-4 py-3">Sesi</th>
                <th class="px-4 py-3">Hadir / Izin / Alfa</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($jadwal as $j)
            @php
                $s = $sesi->get($j->id);
                $status = $s ? $s->status() : 'belum';
                $r = $s ? ($ringkas[$s->id] ?? []) : [];
                $boleh = $user->bolehMembuka($j);
                $hariIni = $tanggal->isToday();
                $bisaBuka = $boleh && $hariIni && ($user->isAdmin() || (int) $j->hari === now()->isoWeekday());
            @endphp
            <tr class="border-t border-slate-100 align-top">
                <td class="px-4 py-3 whitespace-nowrap">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                <td class="px-4 py-3 font-medium">{{ $j->nama }}</td>
                <td class="px-4 py-3">{{ $j->kelas->nama }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $j->guru?->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    @include('panel._badge', ['status' => $status])
                    @if ($s)
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $s->waktu_buka->format('H:i') }} – {{ $s->batas_presensi->format('H:i') }}
                        </p>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    @if ($s)
                        <span class="text-green-600 font-semibold">{{ $r['hadir'] ?? 0 }}</span> /
                        <span class="text-amber-600 font-semibold">{{ $r['izin'] ?? 0 }}</span> /
                        <span class="text-red-600 font-semibold">{{ $r['alfa'] ?? 0 }}</span>
                        <span class="text-xs text-slate-400">dari {{ $jumlahSiswa[$j->kelas_id] ?? 0 }}</span>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-col gap-2 items-start">
                        @if ($bisaBuka && $status !== 'aktif')
                            <form method="POST" action="{{ route('panel.sesi.buka', $j) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="number" name="menit" value="15" min="1" max="240"
                                       class="w-16 border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                                <span class="text-xs text-slate-500">menit</span>
                                <button class="bg-brand text-white rounded-lg px-3 py-1.5 text-xs font-semibold hover:opacity-90">
                                    {{ $s ? 'Buka ulang' : 'Buka sesi' }}
                                </button>
                            </form>
                        @endif
                        @if ($s && $boleh && $status === 'aktif')
                            <form method="POST" action="{{ route('panel.sesi.tutup', $s) }}">
                                @csrf
                                <button class="border border-red-300 text-red-600 rounded-lg px-3 py-1.5 text-xs font-semibold hover:bg-red-50">Tutup sesi</button>
                            </form>
                        @endif
                        @if ($s)
                            <a href="{{ route('panel.sesi.show', $s) }}" class="text-brand text-xs font-semibold">Lihat &amp; koreksi →</a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</div>
@endsection
