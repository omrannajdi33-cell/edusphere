<?php

namespace App\Modules;

use App\CompetencyModuleType;
use App\Modules\Modules\ActiviteModule;
use App\Modules\Modules\CalculModule;
use App\Modules\Modules\CarteModule;
use App\Modules\Modules\EcritureModule;
use App\Modules\Modules\ExperienceModule;
use App\Modules\Modules\GeometrieModule;
use App\Modules\Modules\HistoireIslamiqueModule;
use App\Modules\Modules\LectureIslamiqueModule;
use App\Modules\Modules\LectureModule;
use App\Modules\Modules\ObservationModule;
use App\Modules\Modules\OralModule;
use App\Modules\Modules\ProblemesModule;
use App\Modules\Modules\SecuriteModule;
use App\Modules\Modules\TechniquesModule;
use App\Modules\Modules\TimelineModule;

class CompetencyModuleRegistry
{
    /** @var array<string, CompetencyModuleInterface> */
    private array $modules = [];

    public function __construct()
    {
        $this->registerDefaults();
    }

    public function resolve(?string $moduleType): CompetencyModuleInterface
    {
        $type = CompetencyModuleType::tryFromString($moduleType);

        if ($type === null) {
            return new GenericModule;
        }

        return $this->modules[$type->value] ?? new GenericModule;
    }

    /** @return array<string, CompetencyModuleInterface> */
    public function all(): array
    {
        return $this->modules;
    }

    private function registerDefaults(): void
    {
        $map = [
            CompetencyModuleType::Lecture->value => LectureModule::class,
            CompetencyModuleType::Ecriture->value => EcritureModule::class,
            CompetencyModuleType::Oral->value => OralModule::class,
            CompetencyModuleType::Calcul->value => CalculModule::class,
            CompetencyModuleType::Problemes->value => ProblemesModule::class,
            CompetencyModuleType::Geometrie->value => GeometrieModule::class,
            CompetencyModuleType::Observation->value => ObservationModule::class,
            CompetencyModuleType::Experience->value => ExperienceModule::class,
            CompetencyModuleType::Activite->value => ActiviteModule::class,
            CompetencyModuleType::Techniques->value => TechniquesModule::class,
            CompetencyModuleType::Securite->value => SecuriteModule::class,
            CompetencyModuleType::Timeline->value => TimelineModule::class,
            CompetencyModuleType::Carte->value => CarteModule::class,
            CompetencyModuleType::LectureIslamique->value => LectureIslamiqueModule::class,
            CompetencyModuleType::HistoireIslamique->value => HistoireIslamiqueModule::class,
        ];

        foreach ($map as $key => $class) {
            $this->modules[$key] = app($class);
        }
    }
}
