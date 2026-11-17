<?php

namespace App\Enums;

enum JenisPerbaikan: string
{
    case Kerusakan = 'kerusakan';
    case Upgrade = 'upgrade';

    public function label(): string
    {
        return match ($this) {
            self::Kerusakan => 'Kerusakan',
            self::Upgrade => 'Upgrade',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
