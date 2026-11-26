@extends('layouts.panel')
@section('title', 'Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Halo, {{ $user->name }}</h1>
    <p class="text-sm text-slate-500">{{ $user->labelPeran() }} · {{ now()->locale('id')->translatedFormat('l, j F Y') }}</p>
</div>

@php
    $kartu = [
        ['Siswa', $stat['siswa'], 'text-slate-800'],
        ['Sesi hari ini', $stat['jadwal'], 'text-slate-800'],
        ['Sesi aktif', $stat['aktif'], 'text-brand'],
        ['Hadir hari ini', $stat['hadir'], 'text-green-600'],
        ['Izin hari ini', $stat['izin'], 'text-amber-600'],
        ['Alfa hari ini', $stat['alfa'], 'text-red-600'],
    ];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    @foreach ($kartu as $k)
        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs text-slate-500">{{ $k[0] }}</p>
            <p class="text-3xl font-bold mt-1 {{ $k[2] }}">{{ $k[1] }}</p>
        </div>
    @endforeach
</div>

<h2 class="font-bold text-lg mb-3">Jadwal pelajaran hari ini</h2>
<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    @if ($jadwal->isEmpty())
        <p class="p-6 text-sm text-slate-500">Tidak ada jadwal pelajaran hari ini.</p>
    @else
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Jam</th>
                    <th class="px-4 py-3">Mata pelajaran</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Status sesi</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($jadwal as $j)
                @php $s = $sesi->get($j->id); @endphp
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3 whitespace-nowrap">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                    <td class="px-4 py-3 font-medium">{{ $j->nama }}</td>
                    <td class="px-4 py-3">{{ $j->kelas->nama }}</td>
                    <td class="px-4 py-3">@include('panel._badge', ['status' => $s ? $s->status() : 'belum'])</td>
                    <td class="px-4 py-3 text-right">
                        @if ($s)
                            <a href="{{ route('panel.sesi.show', $s) }}" class="text-brand font-semibold">Buka</a>
                        @else
                            <a href="{{ route('panel.sesi.index') }}" class="text-brand font-semibold">Kelola</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
