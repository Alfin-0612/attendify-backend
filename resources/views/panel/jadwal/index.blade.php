@extends('layouts.panel')
@section('title', 'Jadwal')
@section('content')
@php $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat']; @endphp
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Jadwal Pelajaran</h1>
        <p class="text-sm text-slate-500">Jadwal ini dipakai aplikasi siswa dan sesi presensi.</p>
    </div>
    <div class="flex gap-2">
        <form method="GET">
            <select name="kelas" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
                @foreach ($daftarKelas as $k)
                    <option value="{{ $k->id }}" @selected($kelas && $kelas->id === $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('panel.jadwal.create', ['kelas' => $kelas?->id]) }}"
           class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:opacity-90">+ Tambah jadwal</a>
    </div>
</div>

@if (! $kelas)
    <p class="text-sm text-slate-500">Belum ada kelas.</p>
@else
    <div class="grid gap-5 lg:grid-cols-2">
    @foreach ($namaHari as $no => $hari)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 font-semibold">{{ $hari }}</div>
            @forelse ($perHari->get($no, collect()) as $j)
                <div class="flex items-start justify-between gap-3 px-4 py-3 border-t border-slate-100 text-sm">
                    <div>
                        <p class="font-medium {{ $j->jenis === 'break' ? 'text-slate-500' : '' }}">{{ $j->nama }}</p>
                        <p class="text-xs text-slate-500">
                            {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}
                            @if ($j->ruang) · {{ $j->ruang }} @endif
                            @if ($j->guru) · {{ $j->guru->name }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('panel.jadwal.edit', $j) }}" class="text-brand font-semibold text-xs">Ubah</a>
                        <form method="POST" action="{{ route('panel.jadwal.destroy', $j) }}"
                              onsubmit="return confirm('Hapus jadwal ini? Sesi dan presensi terkait juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 font-semibold text-xs">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-4 py-4 text-sm text-slate-400 border-t border-slate-100">Belum ada jadwal.</p>
            @endforelse
        </div>
    @endforeach
    </div>
@endif
@endsection
