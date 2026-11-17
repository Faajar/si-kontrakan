<?php

namespace App\Enums;

enum PeriodeSewa: string
{
    case Bulanan = 'bulanan';
    case Tahunan = 'tahunan';

    public function label(): string
    {
        return match ($this) {
            self::Bulanan => 'Bulanan',
            self::Tahunan => 'Tahunan',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
