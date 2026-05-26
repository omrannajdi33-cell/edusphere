<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class ActiviteModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Activite;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }
}
