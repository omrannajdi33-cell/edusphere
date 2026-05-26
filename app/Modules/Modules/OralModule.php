<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class OralModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Oral;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }
}
