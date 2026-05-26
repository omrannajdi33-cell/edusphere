<?php

namespace App\Modules;

use App\CompetencyModuleType;
use App\Models\Activity;
use App\Models\ActivitySubmission;

interface CompetencyModuleInterface
{
    public function type(): CompetencyModuleType;

    public function label(): string;

    public function resolveStudentView(Activity $activity): string;

    public function resolveResultView(Activity $activity): string;

    public function requiresManualReview(): bool;

    /**
     * @param  array<string, mixed>  $answers
     * @return array<string, mixed>
     */
    public function mergeSubmissionPayload(array $answers, array $moduleData): array;
}
