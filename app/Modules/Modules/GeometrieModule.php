<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class GeometrieModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Geometrie;
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
