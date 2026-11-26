@extends('layouts.panel')
@section('title', 'Kelas')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Kelas</h1>
    <p class="text-sm text-slate-500">Atur kelas dan wali kelasnya.</p>
</div>

<div class="grid gap-6 lg:grid-cols-5">
    <form method="POST" action="{{ route('panel.kelas.store') }}"
          class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-5 space-y-4 self-start">
        @csrf
        <h2 class="font-bold">Kelas baru</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Nama kelas</label>
            <input type="text" name="nama" required maxlength="50" value="{{ old('nama') }}" placeholder="Mis. XII MIPA 2"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Wali kelas</label>
            <select name="wali_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option value="">— belum ditentukan —</option>
                @foreach ($daftarWali as $w)
                    <option value="{{ $w->id }}" @selected((int) old('wali_id') === $w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1">Akun wali kelas dibuat di menu Guru &amp; Wali.</p>
        </div>
        <button class="bg-brand text-white rounded-lg px-5 py-2.5 text-sm font-semibold hover:opacity-90">Tambah</button>
    </form>

    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 overflow-x-auto self-start">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Wali kelas</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Jadwal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($daftar as $k)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3 font-medium">{{ $k->nama }}</td>
                    <td class="px-4 py-3">{{ $k->wali?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $k->siswa_count }}</td>
                    <td class="px-4 py-3">{{ $k->jadwal_count }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('panel.kelas.edit', $k) }}" class="text-brand font-semibold text-xs mr-3">Ubah</a>
                        <form method="POST" action="{{ route('panel.kelas.destroy', $k) }}" class="inline"
                              onsubmit="return confirm('Hapus kelas {{ $k->nama }}? Jadwalnya juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 font-semibold text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-slate-500">Belum ada kelas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
