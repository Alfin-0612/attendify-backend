@extends('layouts.panel')
@section('title', 'Izin Harian')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Izin Harian</h1>
    <p class="text-sm text-slate-500">Catat izin/sakit seorang siswa untuk <strong>seluruh sesi pelajaran</strong> pada satu tanggal.</p>
</div>

<form method="POST" action="{{ route('panel.izin.simpan') }}"
      class="bg-white rounded-2xl border border-slate-200 p-6 max-w-xl space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Siswa</label>
        <select name="siswa_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— pilih siswa —</option>
            @foreach ($siswa as $sw)
                <option value="{{ $sw->id }}" @selected((int) old('siswa_id') === $sw->id)>{{ $sw->name }} · {{ $sw->kelas?->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="tanggal" required value="{{ old('tanggal', now()->toDateString()) }}"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option value="izin" @selected(old('status') === 'izin')>Izin / sakit</option>
                <option value="alfa" @selected(old('status') === 'alfa')>Alfa</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Keterangan</label>
        <input type="text" name="catatan" maxlength="255" value="{{ old('catatan') }}" placeholder="Mis. sakit demam, surat dokter"
               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>
@endsection
