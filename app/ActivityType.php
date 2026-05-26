<?php

namespace App;

enum ActivityType: string
{
    case Pdf = 'pdf';
    case Dynamic = 'dynamic';

    public function label(): string
    {
        return match ($this) {
            self::Pdf => 'Activité PDF',
            self::Dynamic => 'Activité dynamique',
        };
    }
}
