<?php

namespace App\Modules\Modules;

use App\CompetencyModuleType;
use App\Modules\AbstractCompetencyModule;

class EcritureModule extends AbstractCompetencyModule
{
    public function type(): CompetencyModuleType
    {
        return CompetencyModuleType::Ecriture;
    }

    public function requiresManualReview(): bool
    {
        return true;
    }
}
