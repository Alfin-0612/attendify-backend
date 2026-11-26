<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $fillable = [
        'user_id', 'sesi_presensi_id', 'waktu_tercatat', 'status',
        'skor_kecocokan', 'foto_path', 'dikoreksi_oleh', 'catatan',
    ];

    protected function casts(): array
    {
        return ['waktu_tercatat' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_presensi_id');
    }

    public function dikoreksiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikoreksi_oleh');
    }
}
