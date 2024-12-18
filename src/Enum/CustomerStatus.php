<?php

namespace App\Enum;

enum CustomerStatus: string
{
    case PROSPECT = 'prospect';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function getLabel(): string
    {
        return match($this) {
            self::PROSPECT => 'Prospect',
            self::ACTIVE => 'Client Actif',
            self::INACTIVE => 'Client Inactif'
        };
    }

    public static function getChoices(): array
    {
        return array_combine(
            array_map(fn($case) => $case->getLabel(), self::cases()),
            array_column(self::cases(), 'value')
        );
    }
} 