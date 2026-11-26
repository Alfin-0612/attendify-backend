<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi';
    protected $fillable = ['jadwal_id', 'tanggal', 'waktu_buka', 'batas_presensi'];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_buka' => 'datetime',
            'batas_presensi' => 'datetime',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'sesi_presensi_id');
    }

    public function aktif(): bool
    {
        return now()->between($this->waktu_buka, $this->batas_presensi);
    }

    /** aktif | ditutup | dijadwalkan */
    public function status(): string
    {
        if ($this->aktif()) {
            return 'aktif';
        }

        return now()->lt($this->waktu_buka) ? 'dijadwalkan' : 'ditutup';
    }
}
