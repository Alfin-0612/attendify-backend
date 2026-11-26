<?php

namespace App\Support;

use Carbon\Carbon;

class Semester
{
    public static function mulai(): Carbon
    {
        return Carbon::parse(config('attendify.semester_mulai'))->startOfDay();
    }

    /** Pekan pertemuan ke-berapa (1 = pekan pertama semester). */
    public static function pekanKe($tanggal): int
    {
        $t = Carbon::parse($tanggal)->startOfDay();
        $selisih = self::mulai()->diffInDays($t, false);

        return (int) floor($selisih / 7) + 1;
    }
}
