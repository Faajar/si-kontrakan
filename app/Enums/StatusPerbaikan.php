<?php

namespace App\Enums;

enum StatusPerbaikan: string
{
    case Dilaporkan = 'dilaporkan';
    case Diproses = 'diproses';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Dilaporkan => 'Dilaporkan',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
