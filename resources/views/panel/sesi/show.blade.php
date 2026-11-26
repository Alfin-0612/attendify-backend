@extends('layouts.panel')
@section('title', 'Detail Sesi')
@section('content')
@php
    $status = $sesi->status();
    $boleh = $user->bolehMembuka($sesi->jadwal);
@endphp
<a href="{{ route('panel.sesi.index', ['tanggal' => $sesi->tanggal->toDateString()]) }}" class="text-sm text-brand font-semibold">← Kembali ke daftar sesi</a>

<div class="flex flex-wrap items-start justify-between gap-4 mt-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">{{ $sesi->jadwal->nama }} · {{ $sesi->jadwal->kelas->nama }}</h1>
        <p class="text-sm text-slate-500">
            {{ $sesi->tanggal->copy()->locale('id')->translatedFormat('l, j F Y') }} ·
            {{ $sesi->waktu_buka->format('H:i') }} – {{ $sesi->batas_presensi->format('H:i') }} ·
            Guru: {{ $sesi->jadwal->guru?->name ?? '-' }}
        </p>
        <div class="mt-2">@include('panel._badge', ['status' => $status])</div>
    </div>
    <div class="flex gap-2">
        @if ($boleh && $status === 'aktif')
            <form method="POST" action="{{ route('panel.sesi.tutup', $sesi) }}">
                @csrf
                <button class="border border-red-300 text-red-600 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-50">Tutup sesi</button>
            </form>
        @endif
        @if ($status !== 'aktif' && ($ringkas['belum'] ?? 0) > 0)
            <form method="POST" action="{{ route('panel.sesi.alfa', $sesi) }}"
                  onsubmit="return confirm('Tandai semua siswa yang belum presensi sebagai alfa?')">
                @csrf
                <button class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:opacity-90">
                    Tandai {{ $ringkas['belum'] }} siswa belum presensi sebagai alfa
                </button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-4"><p class="text-xs text-slate-500">Hadir</p><p class="text-3xl font-bold text-green-600">{{ $ringkas['hadir'] }}</p></div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4"><p class="text-xs text-slate-500">Izin</p><p class="text-3xl font-bold text-amber-600">{{ $ringkas['izin'] }}</p></div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4"><p class="text-xs text-slate-500">Alfa</p><p class="text-3xl font-bold text-red-600">{{ $ringkas['alfa'] }}</p></div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4"><p class="text-xs text-slate-500">Belum presensi</p><p class="text-3xl font-bold text-slate-600">{{ $ringkas['belum'] }}</p></div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3 w-10">#</th>
                <th class="px-4 py-3">Siswa</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Waktu</th>
                <th class="px-4 py-3">Koreksi status</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($siswa as $no => $sw)
            @php $p = $presensi->get($sw->id); @endphp
            <tr class="border-t border-slate-100 align-top">
                <td class="px-4 py-3 text-slate-400">{{ $no + 1 }}</td>
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $sw->name }}</p>
                    <p class="text-xs text-slate-500">NISN {{ $sw->nisn }}</p>
                </td>
                <td class="px-4 py-3">
                    @include('panel._badge', ['status' => $p?->status ?? 'kosong'])
                    @if ($p?->dikoreksiOleh)
                        <p class="text-xs text-slate-500 mt-1">Dikoreksi {{ $p->dikoreksiOleh->name }}</p>
                    @endif
                    @if ($p?->catatan)
                        <p class="text-xs text-slate-500 italic">“{{ $p->catatan }}”</p>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap">{{ $p?->waktu_tercatat?->format('H:i') ?? '-' }}</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('panel.sesi.koreksi', ['sesi' => $sesi->id, 'siswa' => $sw->id]) }}"
                          class="flex flex-wrap items-center gap-2">
                        @csrf
                        <select name="status" class="border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                            @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'] as $nilai => $label)
                                <option value="{{ $nilai }}" @selected(($p?->status) === $nilai)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="catatan" placeholder="Catatan (opsional)" maxlength="255"
                               class="border border-slate-300 rounded-lg px-2 py-1.5 text-sm w-44">
                        <button class="bg-slate-800 text-white rounded-lg px-3 py-1.5 text-xs font-semibold hover:opacity-90">Simpan</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
