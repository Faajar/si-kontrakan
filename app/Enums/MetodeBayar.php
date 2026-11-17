<?php

namespace App\Enums;

enum MetodeBayar: string
{
    case Tunai = 'tunai';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Tunai => 'Tunai',
            self::Transfer => 'Transfer Bank',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
