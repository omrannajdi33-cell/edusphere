<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class CarteModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Carte;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }
}
