<?php

namespace App\Enums;

enum ContactStatusEnum: string
{
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'bg-green-100 text-green-800',
            self::Archived => 'bg-gray-100 text-gray-800',
        };
    }
}
