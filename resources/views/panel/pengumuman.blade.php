@extends('layouts.panel')
@section('title', 'Pengumuman')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Pengumuman</h1>
    <p class="text-sm text-slate-500">Pengumuman terbaru tampil di beranda aplikasi siswa.</p>
</div>

<div class="grid gap-6 lg:grid-cols-5">
    <form method="POST" action="{{ route('panel.pengumuman.store') }}"
          class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 space-y-4 self-start">
        @csrf
        <h2 class="font-bold">Pengumuman baru</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Judul</label>
            <input type="text" name="judul" required maxlength="150" value="{{ old('judul') }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Isi</label>
            <textarea name="isi" required maxlength="1000" rows="4"
                      class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">{{ old('isi') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="tanggal" required value="{{ old('tanggal', now()->toDateString()) }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Terbitkan</button>
    </form>

    <div class="lg:col-span-3 space-y-3">
        @forelse ($daftar as $p)
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold">{{ $p->judul }}</p>
                        <p class="text-xs text-slate-500 mb-2">{{ $p->tanggal->copy()->locale('id')->translatedFormat('j F Y') }}</p>
                        <p class="text-sm text-slate-700">{{ $p->isi }}</p>
                    </div>
                    <form method="POST" action="{{ route('panel.pengumuman.destroy', $p) }}"
                          onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 text-xs font-semibold">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection
