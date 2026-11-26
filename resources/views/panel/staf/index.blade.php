@extends('layouts.panel')
@section('title', 'Guru & Wali')
@section('content')
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold">Guru, Wali Kelas &amp; Admin</h1>
        <p class="text-sm text-slate-500">Akun yang dapat masuk ke panel ini.</p>
    </div>
    <a href="{{ route('panel.staf.create') }}" class="bg-brand text-white rounded-lg px-4 py-2 text-sm font-semibold hover:opacity-90">+ Tambah akun</a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">Peran</th>
                <th class="px-4 py-3">Keterangan</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($daftar as $s)
            <tr class="border-t border-slate-100">
                <td class="px-4 py-3 font-medium">{{ $s->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $s->username }}</td>
                <td class="px-4 py-3">{{ $s->labelPeran() }}</td>
                <td class="px-4 py-3 text-slate-600">
                    @if ($s->role === 'guru')
                        {{ $s->jadwal_diampu_count }} jadwal diampu
                    @elseif ($s->role === 'wali')
                        {{ $s->kelas_wali_count }} kelas diwalikan
                    @else
                        -
                    @endif
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="{{ route('panel.staf.edit', $s) }}" class="text-brand font-semibold text-xs mr-3">Ubah</a>
                    @if ($s->id !== auth()->id())
                        <form method="POST" action="{{ route('panel.staf.destroy', $s) }}" class="inline"
                              onsubmit="return confirm('Hapus akun {{ $s->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 font-semibold text-xs">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
