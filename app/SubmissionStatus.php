<?php

namespace App;

enum SubmissionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Returned = 'returned';
    case Graded = 'graded';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Submitted => 'Soumis',
            self::Returned => 'À corriger',
            self::Graded => 'Corrigé',
        };
    }
}
