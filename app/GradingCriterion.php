<?php

namespace App;

enum GradingCriterion: string
{
    case Insufficient = 'insufficient';
    case Fragile = 'fragile';
    case Satisfactory = 'satisfactory';
    case VeryGood = 'very_good';

    public function label(): string
    {
        return match ($this) {
            self::Insufficient => 'Insuffisant',
            self::Fragile => 'Fragile',
            self::Satisfactory => 'Satisfaisant',
            self::VeryGood => 'Très bien',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
