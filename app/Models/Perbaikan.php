<?php

namespace App\Models;

use App\Enums\JenisPerbaikan;
use App\Enums\StatusPerbaikan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Kerusakan (dilaporkan penyewa/pemilik) dan proyek upgrade unit. */
class Perbaikan extends Model
{
    use HasFactory;

    protected $table = 'perbaikan';

    protected $fillable = [
        'unit_id', 'penyewa_id', 'jenis', 'judul', 'deskripsi', 'foto',
        'tanggal_lapor', 'tanggal_selesai', 'status',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => JenisPerbaikan::class,
            'status' => StatusPerbaikan::class,
            'tanggal_lapor' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id');
    }

    /** Rincian biaya: material, upah tukang. */
    public function biaya(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
