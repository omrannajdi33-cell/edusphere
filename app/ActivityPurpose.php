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
            self::Exercise => 'bg-sky-500 text-white',
            self::Exam => 'bg-rose-600 text-white',
        };
    }

    public function chipClass(): string
    {
        return match ($this) {
            self::Exercise => 'edu-purpose-exercise',
            self::Exam => 'edu-purpose-exam',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Exercise => '📝',
            self::Exam => '📋',
        };
    }

    public function studentDescription(): string
    {
        return match ($this) {
            self::Exercise => 'Exercice d\'entraînement — tu peux t\'entraîner sans pression.',
            self::Exam => 'Examen officiel — compte pour le bulletin, une seule tentative.',
        };
    }

    public function countsForBulletin(): bool
    {
        return $this === self::Exam;
    }
}
