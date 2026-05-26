<?php

namespace App;

enum QuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case TrueFalse = 'true_false';
    case ShortAnswer = 'short_answer';
    case LongAnswer = 'long_answer';
    case Matching = 'matching';
    case Ordering = 'ordering';
    case FillBlank = 'fill_blank';
    case MultipleSelect = 'multiple_select';
    case ImageAssociation = 'image_association';
    case Numeric = 'numeric';
    case DragDrop = 'drag_drop';
    case ImageHotspot = 'image_hotspot';
    case Audio = 'audio';
    case TableFill = 'table_fill';
    case Checkbox = 'checkbox';

    public function label(): string
    {
        return match ($this) {
            self::MultipleChoice => 'Choix multiples (QCM)',
            self::TrueFalse => 'Vrai / Faux',
            self::ShortAnswer => 'Réponse courte',
            self::LongAnswer => 'Réponse longue',
            self::Matching => 'Relier les éléments',
            self::Ordering => 'Mettre dans l\'ordre',
            self::FillBlank => 'Compléter les trous',
            self::MultipleSelect => 'Sélection multiple',
            self::ImageAssociation => 'Associer image et réponse',
            self::Numeric => 'Calcul numérique',
            self::DragDrop => 'Glisser-déposer',
            self::ImageHotspot => 'Identifier sur image',
            self::Audio => 'Question audio',
            self::TableFill => 'Tableau à compléter',
            self::Checkbox => 'Cases à cocher',
        };
    }

    /** Types supportés dans l'interface élève (phase 2). */
    public function isAnswerable(): bool
    {
        return in_array($this, [
            self::MultipleChoice,
            self::TrueFalse,
            self::ShortAnswer,
            self::LongAnswer,
            self::Numeric,
            self::Ordering,
            self::FillBlank,
            self::MultipleSelect,
            self::Checkbox,
        ], true);
    }
}
