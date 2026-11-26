<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') · Attendify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#6254F5', soft: '#F0EEFF' } } } } };
    </script>
</head>
<body class="bg-slate-50 text-slate-800">
@php
    $u = auth()->user();
    $menu = [
        ['panel.dashboard', 'Dashboard', ['admin', 'guru', 'wali'], 'panel.dashboard'],
        ['panel.sesi.index', 'Sesi Presensi', ['admin', 'guru', 'wali'], 'panel.sesi.*'],
        ['panel.rekap', 'Rekap Kelas', ['admin', 'guru', 'wali'], 'panel.rekap'],
        ['panel.izin', 'Izin Harian', ['admin', 'wali'], 'panel.izin*'],
        ['panel.jadwal.index', 'Jadwal', ['admin'], 'panel.jadwal.*'],
        ['panel.pengumuman.index', 'Pengumuman', ['admin'], 'panel.pengumuman.*'],
        ['panel.siswa.index', 'Data Siswa', ['admin'], 'panel.siswa.*'],
        ['panel.staf.index', 'Guru & Wali', ['admin'], 'panel.staf.*'],
        ['panel.kelas.index', 'Kelas', ['admin'], 'panel.kelas.*'],
        ['panel.mapel.index', 'Mata Pelajaran', ['admin'], 'panel.mapel.*'],
    ];
@endphp
<div class="min-h-screen md:flex">
    <aside class="md:w-60 md:min-h-screen bg-white border-b md:border-b-0 md:border-r border-slate-200 p-4 md:fixed md:inset-y-0">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-brand text-white font-bold text-xl flex items-center justify-center">A</div>
            <div>
                <p class="font-bold leading-tight">Attendify</p>
                <p class="text-xs text-slate-500">Panel sekolah</p>
            </div>
        </div>
        <nav class="flex md:flex-col gap-1 overflow-x-auto">
            @foreach ($menu as [$rute, $label, $peran, $pola])
                @if (in_array($u->role, $peran, true))
                    <a href="{{ route($rute) }}"
                       class="whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($pola) ? 'bg-brand-soft text-brand' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="mt-6 pt-4 border-t border-slate-200 text-sm md:absolute md:bottom-4 md:left-4 md:right-4">
            <p class="font-semibold truncate">{{ $u->name }}</p>
            <p class="text-xs text-slate-500 mb-3">{{ $u->labelPeran() }}</p>
            <form method="POST" action="{{ route('panel.logout') }}">
                @csrf
                <button class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">Keluar</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 md:ml-60 p-5 md:p-8">
        @if (session('status'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
