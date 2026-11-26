@extends('layouts.panel')
@section('title', 'Ubah Kelas')
@section('content')
<a href="{{ route('panel.kelas.index') }}" class="text-sm text-brand font-semibold">← Kembali ke daftar kelas</a>
<h1 class="text-2xl font-bold mt-3 mb-6">Ubah kelas</h1>

<form method="POST" action="{{ route('panel.kelas.update', $kelas) }}"
      class="bg-white rounded-2xl border border-slate-200 p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Nama kelas</label>
        <input type="text" name="nama" required maxlength="50" value="{{ old('nama', $kelas->nama) }}"
               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Wali kelas</label>
        <select name="wali_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— belum ditentukan —</option>
            @foreach ($daftarWali as $w)
                <option value="{{ $w->id }}" @selected((int) old('wali_id', $kelas->wali_id) === $w->id)>{{ $w->name }}</option>
            @endforeach
        </select>
    </div>
    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>
@endsection
