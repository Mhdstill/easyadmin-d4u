<?php

namespace App\Enum;

enum ProjectStatus: string
{
    case PLANNED = 'planned';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::PLANNED => 'Planifié',
            self::IN_PROGRESS => 'En cours',
            self::COMPLETED => 'Terminé',
            self::CANCELLED => 'Annulé'
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