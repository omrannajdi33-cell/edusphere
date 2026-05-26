<?php

namespace App\Modules;

use App\ActivityType;
use App\Models\Activity;

abstract class AbstractCompetencyModule implements CompetencyModuleInterface
{
    public function label(): string
    {
        return $this->type()->label();
    }

    public function resolveStudentView(Activity $activity): string
    {
        if ($activity->type === ActivityType::Pdf && ! $this->usesCustomWorksheet()) {
            if ($activity->hasReadingMaterial()) {
                return 'student.activities.worksheet';
            }

            return 'student.activities.worksheet';
        }

        return 'modules.'.$this->type()->value.'.show';
    }

    public function resolveResultView(Activity $activity): string
    {
        if ($activity->type === ActivityType::Pdf) {
            return 'student.activities.worksheet-result';
        }

        return 'student.activities.result';
    }

    public function requiresManualReview(): bool
    {
        return false;
    }

    public function mergeSubmissionPayload(array $answers, array $moduleData): array
    {
        if ($moduleData !== []) {
            $answers['_module'] = array_merge($answers['_module'] ?? [], $moduleData);
        }

        return $answers;
    }

    protected function usesCustomWorksheet(): bool
    {
        return false;
    }
}
