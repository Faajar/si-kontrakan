<?php

namespace App\Enums;

enum StatusKontrak: string
{
    case Diajukan = 'diajukan';
    case Aktif = 'aktif';
    case Ditolak = 'ditolak';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::Aktif => 'Aktif',
            self::Ditolak => 'Ditolak',
            self::Selesai => 'Selesai',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
