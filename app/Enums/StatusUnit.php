<?php

namespace App\Enums;

enum StatusUnit: string
{
    case Kosong = 'kosong';
    case Terisi = 'terisi';
    case Perbaikan = 'perbaikan';

    public function label(): string
    {
        return match ($this) {
            self::Kosong => 'Kosong',
            self::Terisi => 'Terisi',
            self::Perbaikan => 'Dalam Perbaikan',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
