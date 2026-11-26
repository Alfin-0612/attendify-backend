@extends('layouts.panel')
@section('title', $jadwal ? 'Ubah Jadwal' : 'Tambah Jadwal')
@section('content')
@php
    $aksi = $jadwal ? route('panel.jadwal.update', $jadwal) : route('panel.jadwal.store');
    $jenis = old('jenis', $jadwal?->jenis ?? 'pelajaran');
    $jamMulai = old('jam_mulai', $jadwal ? substr($jadwal->jam_mulai, 0, 5) : '07:30');
    $jamSelesai = old('jam_selesai', $jadwal ? substr($jadwal->jam_selesai, 0, 5) : '09:00');
    $namaHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat'];
    $gaya = 'w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand';
@endphp
<a href="{{ route('panel.jadwal.index', ['kelas' => $kelasId]) }}" class="text-sm text-brand font-semibold">← Kembali ke jadwal</a>
<h1 class="text-2xl font-bold mt-3 mb-6">{{ $jadwal ? 'Ubah jadwal' : 'Tambah jadwal' }}</h1>

<form method="POST" action="{{ $aksi }}" class="bg-white rounded-2xl border border-slate-200 p-6 max-w-xl space-y-4">
    @csrf
    @if ($jadwal)
        @method('PUT')
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Kelas</label>
            <select name="kelas_id" class="{{ $gaya }}">
                @foreach ($daftarKelas as $k)
                    <option value="{{ $k->id }}" @selected((int) old('kelas_id', $jadwal?->kelas_id ?? $kelasId) === $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jenis</label>
            <select name="jenis" id="jenis" class="{{ $gaya }}">
                <option value="pelajaran" @selected($jenis === 'pelajaran')>Mata pelajaran</option>
                <option value="break" @selected($jenis === 'break')>Kegiatan / istirahat</option>
            </select>
        </div>
    </div>

    <div id="blok-pelajaran" class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Mata pelajaran</label>
            <select name="mapel_id" class="{{ $gaya }}">
                <option value="">— pilih —</option>
                @foreach ($daftarMapel as $m)
                    <option value="{{ $m->id }}" @selected((int) old('mapel_id', $jadwal?->mapel_id) === $m->id)>{{ $m->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Guru pengampu</label>
            <select name="guru_id" class="{{ $gaya }}">
                <option value="">— belum ditentukan —</option>
                @foreach ($daftarGuru as $g)
                    <option value="{{ $g->id }}" @selected((int) old('guru_id', $jadwal?->guru_id) === $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="blok-kegiatan">
        <label class="block text-sm font-medium mb-1">Nama kegiatan</label>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $jadwal?->nama_kegiatan) }}"
               placeholder="Mis. Istirahat, Apel pagi" class="{{ $gaya }}">
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Hari</label>
            <select name="hari" class="{{ $gaya }}">
                @foreach ($namaHari as $no => $hari)
                    <option value="{{ $no }}" @selected((int) old('hari', $jadwal?->hari ?? 1) === $no)>{{ $hari }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Mulai</label>
            <input type="time" name="jam_mulai" value="{{ $jamMulai }}" required class="{{ $gaya }}">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Selesai</label>
            <input type="time" name="jam_selesai" value="{{ $jamSelesai }}" required class="{{ $gaya }}">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Ruang (opsional)</label>
        <input type="text" name="ruang" value="{{ old('ruang', $jadwal?->ruang) }}" class="{{ $gaya }}">
    </div>

    <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Simpan</button>
</form>

<script>
    const jenis = document.getElementById('jenis');
    const blokPelajaran = document.getElementById('blok-pelajaran');
    const blokKegiatan = document.getElementById('blok-kegiatan');
    function tampilkan() {
        const pelajaran = jenis.value === 'pelajaran';
        blokPelajaran.style.display = pelajaran ? '' : 'none';
        blokKegiatan.style.display = pelajaran ? 'none' : '';
    }
    jenis.addEventListener('change', tampilkan);
    tampilkan();
</script>
@endsection
