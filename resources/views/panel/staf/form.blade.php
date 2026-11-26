@extends('layouts.panel')
@section('title', $staf ? 'Ubah Akun' : 'Tambah Akun')
@section('content')
@php $gaya = 'w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand'; @endphp
<a href="{{ route('panel.staf.index') }}" class="text-sm text-brand font-semibold">← Kembali ke daftar</a>
<h1 class="text-2xl font-bold mt-3 mb-6">{{ $staf ? 'Ubah akun' : 'Tambah akun' }}</h1>

<form method="POST" action="{{ $staf ? route('panel.staf.update', $staf) : route('panel.staf.store') }}"
      class="bg-white rounded-2xl border border-slate-200 p-6 max-w-md space-y-4">
    @csrf
    @if ($staf)
        @method('PUT')
    @endif
    <div>
        <label class="block text-sm font-medium mb-1">Nama lengkap</label>
        <input type="text" name="name" required maxlength="100" value="{{ old('name', $staf?->name) }}" class="{{ $gaya }}">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Username</label>
        <input type="text" name="username" required maxlength="50" value="{{ old('username', $staf?->username) }}" class="{{ $gaya }}">
        <p class="text-xs text-slate-500 mt-1">Huruf, angka, titik, minus, atau garis bawah.</p>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Peran</label>
        <select name="role" class="{{ $gaya }}">
            @foreach (['guru' => 'Guru mata pelajaran', 'wali' => 'Wali kelas', 'admin' => 'Admin sekolah'] as $nilai => $label)
                <option value="{{ $nilai }}" @selected(old('role', $staf?->role ?? 'guru') === $nilai)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Password{{ $staf ? ' baru' : '' }}</label>
        <input type="password" name="password" {{ $staf ? '' : 'required' }} minlength="6" autocomplete="new-password" class="{{ $gaya }}">
        @if ($staf)
            <p class="text-xs text-slate-500 mt-1">Kosongkan bila tidak ingin mengubah password.</p>
        @else
            <p class="text-xs text-slate-500 mt-1">Minimal 6 karakter.</p>
        @endif
    </div>
    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>
@endsection
