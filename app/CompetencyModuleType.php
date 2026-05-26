<?php

namespace App;

enum CompetencyModuleType: string
{
    case Lecture = 'lecture';
    case Ecriture = 'ecriture';
    case Oral = 'oral';
    case Calcul = 'calcul';
    case Problemes = 'problemes';
    case Geometrie = 'geometrie';
    case Observation = 'observation';
    case Experience = 'experience';
    case Activite = 'activite';
    case Techniques = 'techniques';
    case Securite = 'securite';
    case Timeline = 'timeline';
    case Carte = 'carte';
    case LectureIslamique = 'lecture_islamique';
    case HistoireIslamique = 'histoire_islamique';

    public function label(): string
    {
        return match ($this) {
            self::Lecture => 'Lecture / compréhension',
            self::Ecriture => 'Écriture',
            self::Oral => 'Communication orale',
            self::Calcul => 'Calcul',
            self::Problemes => 'Résolution de problèmes',
            self::Geometrie => 'Géométrie',
            self::Observation => 'Observation',
            self::Experience => 'Expérimentation',
            self::Activite => 'Activité physique',
            self::Techniques => 'Techniques',
            self::Securite => 'Sécurité',
            self::Timeline => 'Ligne du temps',
            self::Carte => 'Cartes',
            self::LectureIslamique => 'Lecture islamique',
            self::HistoireIslamique => 'Histoire islamique',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Lecture => '📖',
            self::Ecriture => '✍️',
            self::Oral => '🎙️',
            self::Calcul => '🔢',
            self::Problemes => '🧮',
            self::Geometrie => '📐',
            self::Observation => '🔍',
            self::Experience => '🧪',
            self::Activite => '🏃',
            self::Techniques => '🏊',
            self::Securite => '🛟',
            self::Timeline => '📅',
            self::Carte => '🗺️',
            self::LectureIslamique => '📿',
            self::HistoireIslamique => '🕌',
        };
    }

    public static function tryFromString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value);
    }
}
