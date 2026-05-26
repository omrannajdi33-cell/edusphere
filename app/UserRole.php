<?php

namespace App;

enum UserRole: string
{
    case Admin = 'admin';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Professeur',
            self::Student => 'Élève',
        };
    }
}
