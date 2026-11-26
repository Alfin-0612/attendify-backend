<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'nisn', 'kelas_id', 'role', 'tahun_ajaran',
        'foto_wajah', 'email', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    /** Jadwal yang diampu (untuk guru). */
    public function jadwalDiampu(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    /** Kelas yang diwalikan (untuk wali kelas). */
    public function kelasWali(): HasMany
    {
        return $this->hasMany(Kelas::class, 'wali_id');
    }

    // ---------- peran ----------
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function labelPeran(): string
    {
        return match ($this->role) {
            'admin' => 'Admin sekolah',
            'guru' => 'Guru mata pelajaran',
            'wali' => 'Wali kelas',
            default => 'Siswa',
        };
    }

    /** Jadwal yang boleh dilihat: admin semua, guru miliknya, wali untuk kelasnya. */
    public function jadwalQuery(): Builder
    {
        $q = Jadwal::query();

        return match ($this->role) {
            'guru' => $q->where('guru_id', $this->id),
            'wali' => $q->whereIn('kelas_id', Kelas::where('wali_id', $this->id)->select('id')),
            default => $q,
        };
    }

    /** Kelas yang boleh dilihat. */
    public function kelasQuery(): Builder
    {
        return match ($this->role) {
            'wali' => Kelas::where('wali_id', $this->id),
            'guru' => Kelas::whereIn('id', Jadwal::where('guru_id', $this->id)->select('kelas_id')),
            default => Kelas::query(),
        };
    }

    /** Boleh membuka/menutup sesi: admin atau guru pengampu jadwal itu. */
    public function bolehMembuka(Jadwal $jadwal): bool
    {
        return $this->role === 'admin'
            || ($this->role === 'guru' && (int) $jadwal->guru_id === (int) $this->id);
    }
}
