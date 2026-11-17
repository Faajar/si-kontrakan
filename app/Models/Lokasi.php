<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'nama', 'alamat', 'latitude', 'longitude', 'link_maps', 'lingkungan_sekitar',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /** Link Google Maps: pakai link manual, kalau kosong dibuat dari koordinat. */
    public function mapsUrl(): ?string
    {
        if ($this->link_maps) {
            return $this->link_maps;
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return null;
    }
}
