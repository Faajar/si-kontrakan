<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Penyewa = 'penyewa';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Pemilik (Admin)',
            self::Penyewa => 'Penyewa',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
