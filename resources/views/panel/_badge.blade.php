@php
    $petaBadge = [
        'hadir' => ['Hadir', 'bg-green-100 text-green-700'],
        'izin' => ['Izin', 'bg-amber-100 text-amber-700'],
        'alfa' => ['Alfa', 'bg-red-100 text-red-700'],
        'aktif' => ['Aktif', 'bg-green-100 text-green-700'],
        'ditutup' => ['Ditutup', 'bg-slate-200 text-slate-600'],
        'dijadwalkan' => ['Dijadwalkan', 'bg-blue-100 text-blue-700'],
        'belum' => ['Belum dibuka', 'bg-slate-100 text-slate-500'],
        'kosong' => ['Belum presensi', 'bg-slate-100 text-slate-500'],
    ];
    $infoBadge = $petaBadge[$status] ?? [$status, 'bg-slate-100 text-slate-600'];
@endphp
<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $infoBadge[1] }}">{{ $infoBadge[0] }}</span>
