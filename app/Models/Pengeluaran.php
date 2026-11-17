<?php

namespace App\Models;

use App\Enums\KategoriPengeluaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'unit_id', 'perbaikan_id', 'kategori', 'tanggal', 'keterangan', 'nominal',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => KategoriPengeluaran::class,
            'tanggal' => 'date',
            'nominal' => 'integer',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function perbaikan(): BelongsTo
    {
        return $this->belongsTo(Perbaikan::class);
    }
}
