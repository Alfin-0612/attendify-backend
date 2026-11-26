@extends('layouts.panel')
@section('title', 'Ubah Mata Pelajaran')
@section('content')
<a href="{{ route('panel.mapel.index') }}" class="text-sm text-brand font-semibold">← Kembali ke daftar</a>
<h1 class="text-2xl font-bold mt-3 mb-6">Ubah mata pelajaran</h1>

<form method="POST" action="{{ route('panel.mapel.update', $mapel) }}"
      class="bg-white rounded-2xl border border-slate-200 p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Nama</label>
        <input type="text" name="nama" required maxlength="100" value="{{ old('nama', $mapel->nama) }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>
@endsection
