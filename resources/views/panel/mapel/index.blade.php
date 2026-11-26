@extends('layouts.panel')
@section('title', 'Mata Pelajaran')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Mata Pelajaran</h1>
    <p class="text-sm text-slate-500">Daftar mata pelajaran yang dapat dipilih saat menyusun jadwal.</p>
</div>

<div class="grid gap-6 lg:grid-cols-5">
    <form method="POST" action="{{ route('panel.mapel.store') }}"
          class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 space-y-4 self-start">
        @csrf
        <h2 class="font-bold">Mata pelajaran baru</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="nama" required maxlength="100" value="{{ old('nama') }}" placeholder="Mis. Sejarah"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Tambah</button>
    </form>

    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 overflow-x-auto self-start">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Mata pelajaran</th>
                    <th class="px-4 py-3">Dipakai di jadwal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($daftar as $m)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3 font-medium">{{ $m->nama }}</td>
                    <td class="px-4 py-3">{{ $m->jadwal_count }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('panel.mapel.edit', $m) }}" class="text-brand font-semibold text-xs mr-3">Ubah</a>
                        <form method="POST" action="{{ route('panel.mapel.destroy', $m) }}" class="inline"
                              onsubmit="return confirm('Hapus mata pelajaran {{ $m->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 font-semibold text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-slate-500">Belum ada mata pelajaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
