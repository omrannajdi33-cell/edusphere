<?php

namespace App;

enum GradingMode: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => 'Correction automatique',
            self::Manual => 'Je corrige moi-même',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Automatic => 'QCM, vrai/faux, chiffres… sont corrigés par le système.',
            self::Manual => 'Toutes les copies arrivent dans Corrections pour validation manuelle.',
        };
    }
}
