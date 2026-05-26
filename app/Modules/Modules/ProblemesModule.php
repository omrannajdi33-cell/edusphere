<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class ProblemesModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Problemes;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }

    protected function usesCustomWorksheet(): bool
    {
        return true;
    }
}
