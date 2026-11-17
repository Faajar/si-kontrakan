<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';

    protected $fillable = [
        'user_id', 'nama', 'nik', 'no_whatsapp', 'alamat_asal', 'foto_ktp', 'catatan',
    ];

    protected function noWhatsapp(): Attribute
    {
        return Attribute::set(fn (?string $value) => Phone::normalize($value));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kontrak(): HasMany
    {
        return $this->hasMany(KontrakSewa::class);
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(Perbaikan::class);
    }
}
