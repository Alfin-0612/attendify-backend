@extends('layouts.panel')
@section('title', 'Rekap Kelas')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Rekap Kehadiran Kelas</h1>
        <p class="text-sm text-slate-500">Akumulasi seluruh sesi presensi yang tercatat.</p>
    </div>
    @if ($daftarKelas->count() > 1)
        <form method="GET">
            <select name="kelas" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
                @foreach ($daftarKelas as $k)
                    <option value="{{ $k->id }}" @selected($kelas && $kelas->id === $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </form>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
@if (! $kelas)
    <p class="p-6 text-sm text-slate-500">Belum ada kelas yang dapat ditampilkan.</p>
@else
    <div class="px-4 py-3 border-b border-slate-100 font-semibold">{{ $kelas->nama }} · {{ $baris->count() }} siswa</div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3">Siswa</th>
                <th class="px-4 py-3">Hadir</th>
                <th class="px-4 py-3">Izin</th>
                <th class="px-4 py-3">Alfa</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3">Kehadiran</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($baris as $b)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $b['siswa']->name }}</p>
                    <p class="text-xs text-slate-500">NISN {{ $b['siswa']->nisn }}</p>
                </td>
                <td class="px-4 py-3 text-green-600 font-semibold">{{ $b['hadir'] }}</td>
                <td class="px-4 py-3 text-amber-600 font-semibold">{{ $b['izin'] }}</td>
                <td class="px-4 py-3 text-red-600 font-semibold">{{ $b['alfa'] }}</td>
                <td class="px-4 py-3">{{ $b['total'] }}</td>
                <td class="px-4 py-3 w-56">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full {{ $b['persen'] >= 75 ? 'bg-green-500' : 'bg-amber-500' }}" style="width: {{ $b['persen'] }}%"></div>
                        </div>
                        <span class="text-xs font-semibold w-10 text-right">{{ $b['persen'] }}%</span>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</div>
@endsection
