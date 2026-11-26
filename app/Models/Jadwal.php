<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = [
        'kelas_id', 'mapel_id', 'guru_id', 'nama_kegiatan', 'jenis',
        'hari', 'jam_mulai', 'jam_selesai', 'ruang',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function getNamaAttribute(): string
    {
        return $this->mapel?->nama ?? $this->nama_kegiatan ?? '-';
    }
}
