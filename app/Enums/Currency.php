<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case KHR = 'KHR';

    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::KHR => '៛',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::KHR => 'Cambodian Riel',
        };
    }

    public static function options(): array
    {
        return [
            self::USD->value => self::USD->label(),
            self::KHR->value => self::KHR->label(),
        ];
    }
}
