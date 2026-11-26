@extends('layouts.panel')
@section('title', 'Data Siswa')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Data Siswa</h1>
        <p class="text-sm text-slate-500">{{ $siswa->total() }} siswa</p>
    </div>
    <a href="{{ route('panel.siswa.create') }}" class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:opacity-90">+ Tambah siswa</a>
</div>

<form method="GET" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, NISN, atau username"
           class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-64">
    <select name="kelas" class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Semua kelas</option>
        @foreach ($daftarKelas as $k)
            <option value="{{ $k->id }}" @selected($kelasId === $k->id)>{{ $k->nama }}</option>
        @endforeach
    </select>
    <button class="bg-white border border-slate-300 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-slate-100">Cari</button>
</form>

<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3">Siswa</th>
                <th class="px-4 py-3">NISN</th>
                <th class="px-4 py-3">Kelas</th>
                <th class="px-4 py-3">Foto wajah</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($siswa as $sw)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $sw->name }}</p>
                    <p class="text-xs text-slate-500">{{ $sw->username }}</p>
                </td>
                <td class="px-4 py-3">{{ $sw->nisn }}</td>
                <td class="px-4 py-3">{{ $sw->kelas?->nama ?? '-' }}</td>
                <td class="px-4 py-3">
                    @if ($sw->foto_wajah)
                        <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-700">Terdaftar</span>
                    @else
                        <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-500">Belum</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('panel.siswa.edit', $sw) }}" class="text-brand font-semibold text-xs mr-3">Ubah</a>
                    <form method="POST" action="{{ route('panel.siswa.destroy', $sw) }}" class="inline"
                          onsubmit="return confirm('Hapus {{ $sw->name }}? Riwayat presensinya juga akan terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 font-semibold text-xs">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-6 text-slate-500">Tidak ada siswa yang cocok.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $siswa->links() }}</div>
@endsection
