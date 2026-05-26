<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Question;
use App\QuestionType;

class QuestionGrader
{
    /**
     * @param  array<string, mixed>  $answers  question_id => answer
     * @return array{score: int, max_score: int, details: array<int, array{correct: bool, points: int, max: int}>}
     */
    public function gradeActivity(Activity $activity, array $answers): array
    {
        $activity->load(['sections.questions']);

        $score = 0;
        $maxScore = 0;
        $details = [];

        foreach ($activity->sections as $section) {
            foreach ($section->questions as $question) {
                $maxScore += $question->points;
                $answer = $answers[(string) $question->id] ?? null;
                $correct = $this->isCorrect($question, $answer);
                $earned = $correct ? $question->points : 0;
                $score += $earned;

                $details[$question->id] = [
                    'correct' => $correct,
                    'points' => $earned,
                    'max' => $question->points,
                ];
            }
        }

        return [
            'score' => $score,
            'max_score' => $maxScore,
            'details' => $details,
        ];
    }

    public function isCorrect(Question $question, mixed $answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        $config = $question->config ?? [];

        return match ($question->type) {
            QuestionType::MultipleChoice => $this->gradeSingleChoice($config, $answer),
            QuestionType::TrueFalse => $this->toBool($answer) === (bool) ($config['correct'] ?? false),
            QuestionType::ShortAnswer => $this->gradeShortAnswer($config, (string) $answer),
            QuestionType::Numeric => $this->gradeNumeric($config, $answer),
            QuestionType::MultipleSelect, QuestionType::Checkbox => $this->gradeMultiSelect($config, (array) $answer),
            QuestionType::Ordering => $this->gradeOrdering($config, (array) $answer),
            QuestionType::FillBlank => $this->gradeFillBlank($config, (array) $answer),
            QuestionType::LongAnswer => false,
            default => false,
        };
    }

    private function toBool(mixed $value): bool
    {
        return $value === true || $value === 1 || $value === '1';
    }

    private function gradeSingleChoice(array $config, mixed $answer): bool
    {
        foreach ($config['options'] ?? [] as $option) {
            if (($option['id'] ?? null) === $answer && ($option['correct'] ?? false)) {
                return true;
            }
        }

        return false;
    }

    private function gradeShortAnswer(array $config, string $answer): bool
    {
        $normalized = mb_strtolower(trim($answer));
        $acceptable = $config['acceptable'] ?? [];

        foreach ($acceptable as $item) {
            $candidate = mb_strtolower(trim((string) $item));
            if ($normalized === $candidate) {
                return true;
            }
        }

        return false;
    }

    private function gradeNumeric(array $config, mixed $answer): bool
    {
        if (! is_numeric($answer)) {
            return false;
        }

        $expected = (float) ($config['answer'] ?? 0);
        $tolerance = (float) ($config['tolerance'] ?? 0);

        return abs((float) $answer - $expected) <= $tolerance;
    }

    private function gradeMultiSelect(array $config, array $answer): bool
    {
        $correctIds = collect($config['options'] ?? [])
            ->filter(fn ($o) => $o['correct'] ?? false)
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        $given = collect($answer)->sort()->values()->all();

        return $correctIds === $given;
    }

    private function gradeOrdering(array $config, array $answer): bool
    {
        $correctOrder = $config['correct_order'] ?? [];

        return array_values($answer) === array_values($correctOrder);
    }

    private function gradeFillBlank(array $config, array $answer): bool
    {
        $blanks = $config['blanks'] ?? [];

        foreach ($blanks as $index => $expected) {
            $given = mb_strtolower(trim((string) ($answer[$index] ?? '')));
            $target = mb_strtolower(trim((string) $expected));

            if ($given !== $target) {
                return false;
            }
        }

        return count($blanks) > 0;
    }
}
