<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class ObservationModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Observation;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }
}
