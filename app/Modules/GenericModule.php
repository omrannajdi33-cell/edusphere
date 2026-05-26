<?php

namespace App\Modules;

use App\ActivityType;
use App\CompetencyModuleType;
use App\Models\Activity;

class GenericModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Calcul;
    }

    public function label(): string
    {
        return 'Activité standard';
    }

    public function resolveStudentView(Activity $activity): string
    {
        if ($activity->type === ActivityType::Pdf) {
            return 'student.activities.worksheet';
        }

        return 'student.activities.dynamic';
    }
}
