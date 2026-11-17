<?php

namespace App\Enums;

enum StatusPembayaran: string
{
    case Lunas = 'lunas';
    case Nunggak = 'nunggak';

    public function label(): string
    {
        return match ($this) {
            self::Lunas => 'Lunas',
            self::Nunggak => 'Nunggak',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
