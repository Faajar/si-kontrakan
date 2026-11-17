<?php

namespace App\Models;

use App\Enums\StatusKontrak;
use App\Enums\StatusUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'lokasi_id', 'nomor_unit', 'tarif_bulanan', 'tarif_tahunan', 'jumlah_kamar',
        'luas_bangunan', 'fasilitas', 'status', 'ketentuan_penghuni', 'izin_hewan',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusUnit::class,
            'izin_hewan' => 'boolean',
            'tarif_bulanan' => 'integer',
            'tarif_tahunan' => 'integer',
            'jumlah_kamar' => 'integer',
            'luas_bangunan' => 'decimal:2',
        ];
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function kontrak(): HasMany
    {
        return $this->hasMany(KontrakSewa::class);
    }

    public function kontrakAktif(): HasOne
    {
        // Filter status harus di dalam subquery ofMany; kalau di luar, kontrak
        // "diajukan" yang lebih baru membuat kontrak aktif ikut hilang.
        return $this->hasOne(KontrakSewa::class)->ofMany(
            ['tanggal_mulai' => 'max', 'id' => 'max'],
            fn ($q) => $q->where('status', StatusKontrak::Aktif),
        );
    }

    public function perbaikan(): HasMany
    {
        return $this->hasMany(Perbaikan::class);
    }

    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
