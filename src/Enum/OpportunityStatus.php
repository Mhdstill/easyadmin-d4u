<?php

namespace App\Enum;

enum OpportunityStatus: string
{
    case NEW = 'new';
    case NEGOTIATION = 'negotiation';
    case WON = 'won';
    case LOST = 'lost';

    public function getLabel(): string
    {
        return match($this) {
            self::NEW => 'Nouvelle',
            self::NEGOTIATION => 'En négociation',
            self::WON => 'Gagnée',
            self::LOST => 'Perdue'
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