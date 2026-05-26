<?php

namespace App;

enum StudentLevel: int
{
    case Level1 = 1;
    case Level2 = 2;
    case Level3 = 3;

    public function label(): string
    {
        return match ($this) {
            self::Level1 => 'Niveau 1 — Débutant',
            self::Level2 => 'Niveau 2 — Intermédiaire',
            self::Level3 => 'Niveau 3 — Avancé',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Level1 => 'Niveau 1',
            self::Level2 => 'Niveau 2',
            self::Level3 => 'Niveau 3',
        };
    }
}
