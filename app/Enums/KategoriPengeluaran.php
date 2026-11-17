<?php

namespace App\Enums;

enum KategoriPengeluaran: string
{
    case Material = 'material';
    case UpahTukang = 'upah_tukang';
    case Operasional = 'operasional';

    public function label(): string
    {
        return match ($this) {
            self::Material => 'Material',
            self::UpahTukang => 'Upah Tukang',
            self::Operasional => 'Operasional',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
