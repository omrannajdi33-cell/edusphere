<?php

namespace App;

enum ActivityPurpose: string
{
    case Exercise = 'exercise';
    case Exam = 'exam';

    public function label(): string
    {
        return match ($this) {
            self::Exercise => 'Exercice',
            self::Exam => 'Examen',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Exercise => 'bg-sky-100 text-sky-800',
            self::Exam => 'bg-rose-100 text-rose-800',
        };
    }

    public function countsForBulletin(): bool
    {
        return $this === self::Exam;
    }
}
