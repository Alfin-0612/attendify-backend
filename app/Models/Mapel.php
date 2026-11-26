<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $fillable = ['nama'];

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
