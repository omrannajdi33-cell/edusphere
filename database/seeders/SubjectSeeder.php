<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Français',
                'slug' => 'francais',
                'color' => '#ef4444',
                'icon' => '🇫🇷',
                'competencies' => [
                    ['name' => 'Lecture / compréhension', 'module_type' => 'lecture'],
                    ['name' => 'Écriture', 'module_type' => 'ecriture'],
                    ['name' => 'Communication orale', 'module_type' => 'oral'],
                    ['name' => 'Vocabulaire', 'module_type' => null],
                    ['name' => 'Orthographe', 'module_type' => null],
                    ['name' => 'Grammaire', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Mathématiques',
                'slug' => 'mathematiques',
                'color' => '#3b82f6',
                'icon' => '➗',
                'competencies' => [
                    ['name' => 'Arithmétique', 'module_type' => 'calcul'],
                    ['name' => 'Résolution de problèmes', 'module_type' => 'problemes'],
                    ['name' => 'Géométrie', 'module_type' => 'geometrie'],
                    ['name' => 'Mesures', 'module_type' => null],
                    ['name' => 'Fractions', 'module_type' => null],
                    ['name' => 'Logique', 'module_type' => null],
                    ['name' => 'Multiplication / division', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Sciences',
                'slug' => 'sciences',
                'color' => '#10b981',
                'icon' => '🔬',
                'competencies' => [
                    ['name' => 'Univers vivant', 'module_type' => null],
                    ['name' => 'Univers matériel', 'module_type' => null],
                    ['name' => 'Terre et espace', 'module_type' => null],
                    ['name' => 'Observation', 'module_type' => 'observation'],
                    ['name' => 'Expérimentation', 'module_type' => 'experience'],
                ],
            ],
            [
                'name' => 'Éducation physique',
                'slug' => 'education-physique',
                'color' => '#f59e0b',
                'icon' => '🏃',
                'competencies' => [
                    ['name' => 'Coordination', 'module_type' => 'activite'],
                    ['name' => 'Santé', 'module_type' => null],
                    ['name' => 'Mouvement', 'module_type' => null],
                    ['name' => 'Activité physique', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Natation',
                'slug' => 'natation',
                'color' => '#06b6d4',
                'icon' => '🏊',
                'competencies' => [
                    ['name' => 'Flottaison', 'module_type' => 'techniques'],
                    ['name' => 'Respiration', 'module_type' => null],
                    ['name' => 'Déplacements', 'module_type' => null],
                    ['name' => 'Sécurité aquatique', 'module_type' => 'securite'],
                    ['name' => 'Techniques de nage', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Histoire',
                'slug' => 'histoire',
                'color' => '#a855f7',
                'icon' => '🏛️',
                'competencies' => [
                    ['name' => 'Temps historique', 'module_type' => 'timeline'],
                    ['name' => 'Civilisations', 'module_type' => null],
                    ['name' => 'Territoires', 'module_type' => null],
                    ['name' => 'Cartes', 'module_type' => 'carte'],
                    ['name' => 'Sociétés', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Géographie',
                'slug' => 'geographie',
                'color' => '#84cc16',
                'icon' => '🌍',
                'competencies' => [
                    ['name' => 'Territoires', 'module_type' => 'carte'],
                    ['name' => 'Cartes', 'module_type' => 'carte'],
                    ['name' => 'Sociétés', 'module_type' => null],
                    ['name' => 'Environnement', 'module_type' => null],
                ],
            ],
            [
                'name' => 'Islam',
                'slug' => 'islam',
                'color' => '#14b8a6',
                'icon' => '☪️',
                'competencies' => [
                    ['name' => 'Lecture', 'module_type' => 'lecture_islamique'],
                    ['name' => 'Compréhension', 'module_type' => null],
                    ['name' => 'Mémorisation', 'module_type' => null],
                    ['name' => 'Histoire islamique', 'module_type' => 'histoire_islamique'],
                    ['name' => 'Valeurs', 'module_type' => null],
                ],
            ],
        ];

        foreach ($subjects as $index => $subjectData) {
            $subject = Subject::query()->updateOrCreate(
                ['slug' => $subjectData['slug']],
                [
                    'name' => $subjectData['name'],
                    'color' => $subjectData['color'],
                    'icon' => $subjectData['icon'],
                    'sort_order' => $index + 1,
                ]
            );

            foreach ($subjectData['competencies'] as $competencyIndex => $competencyData) {
                Competency::query()->updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'slug' => Str::slug($competencyData['name']),
                    ],
                    [
                        'name' => $competencyData['name'],
                        'module_type' => $competencyData['module_type'],
                        'sort_order' => $competencyIndex + 1,
                    ]
                );
            }
        }
    }
}
