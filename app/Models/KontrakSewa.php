<?php

namespace App\Models;

use App\Enums\PeriodeSewa;
use App\Enums\StatusKontrak;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KontrakSewa extends Model
{
    use HasFactory;

    protected $table = 'kontrak_sewa';

    protected $fillable = [
        'unit_id', 'penyewa_id', 'periode', 'tanggal_mulai', 'tanggal_selesai',
        'tarif', 'status', 'file_perjanjian', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'periode' => PeriodeSewa::class,
            'status' => StatusKontrak::class,
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tarif' => 'integer',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
}
