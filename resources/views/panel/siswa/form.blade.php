@extends('layouts.panel')
@section('title', $siswa ? 'Ubah Siswa' : 'Tambah Siswa')
@section('content')
@php $gaya = 'w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand'; @endphp
<a href="{{ route('panel.siswa.index') }}" class="text-sm text-brand font-semibold">← Kembali ke daftar siswa</a>
<h1 class="text-2xl font-bold mt-3 mb-6">{{ $siswa ? 'Ubah data siswa' : 'Tambah siswa' }}</h1>

<form method="POST" enctype="multipart/form-data"
      action="{{ $siswa ? route('panel.siswa.update', $siswa) : route('panel.siswa.store') }}"
      class="bg-white rounded-2xl border border-slate-200 p-6 max-w-xl space-y-4">
    @csrf
    @if ($siswa)
        @method('PUT')
    @endif

    <div>
        <label class="block text-sm font-medium mb-1">Nama lengkap</label>
        <input type="text" name="name" required maxlength="100" value="{{ old('name', $siswa?->name) }}" class="{{ $gaya }}">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">NISN</label>
            <input type="text" name="nisn" required maxlength="20" inputmode="numeric" value="{{ old('nisn', $siswa?->nisn) }}" class="{{ $gaya }}">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Username</label>
            <input type="text" name="username" required maxlength="50" value="{{ old('username', $siswa?->username) }}" class="{{ $gaya }}">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Kelas</label>
            <select name="kelas_id" required class="{{ $gaya }}">
                @foreach ($daftarKelas as $k)
                    <option value="{{ $k->id }}" @selected((int) old('kelas_id', $siswa?->kelas_id) === $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            @if ($siswa)
                <p class="text-xs text-slate-500 mt-1">Mengubah kelas memindahkan siswa. Riwayat presensi lama tetap tersimpan.</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tahun ajaran</label>
            <input type="text" name="tahun_ajaran" required maxlength="50"
                   value="{{ old('tahun_ajaran', $siswa?->tahun_ajaran ?? 'Ganjil 2026/2027') }}" class="{{ $gaya }}">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Password{{ $siswa ? ' baru' : '' }}</label>
        <input type="password" name="password" {{ $siswa ? '' : 'required' }} minlength="6" autocomplete="new-password" class="{{ $gaya }}">
        <p class="text-xs text-slate-500 mt-1">{{ $siswa ? 'Kosongkan bila tidak ingin mengubah password.' : 'Minimal 6 karakter.' }}</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Foto wajah pendaftaran</label>
        @if ($siswa?->foto_wajah)
            <img src="{{ route('panel.siswa.foto', $siswa) }}" alt="Foto wajah {{ $siswa->name }}"
                 class="w-32 h-32 object-cover rounded-xl border border-slate-200 mb-2">
        @endif
        <input type="file" name="foto_wajah" accept="image/*" class="block text-sm">
        <p class="text-xs text-slate-500 mt-1">
            Foto wajah menghadap depan, jelas, dan tidak tertutup. Dipakai sebagai pembanding oleh layanan pengenalan wajah. Maks. 4 MB.
        </p>
    </div>

    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>
@endsection
