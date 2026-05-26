<?php

namespace Database\Seeders;

use App\Models\PointBehavior;
use Illuminate\Database\Seeder;

class PointBehaviorSeeder extends Seeder
{
    public function run(): void
    {
        $positive = [
            ['label' => 'Participation', 'points' => 1, 'icon' => '🙋'],
            ['label' => 'Bon effort', 'points' => 2, 'icon' => '💪'],
            ['label' => 'Travail soigné', 'points' => 2, 'icon' => '✨'],
            ['label' => 'Entraide', 'points' => 1, 'icon' => '🤝'],
            ['label' => 'Excellente attitude', 'points' => 3, 'icon' => '⭐'],
        ];

        $negative = [
            ['label' => 'Bavardage', 'points' => -1, 'icon' => '💬'],
            ['label' => 'Pas écouté', 'points' => -1, 'icon' => '👂'],
            ['label' => 'Devoir non fait', 'points' => -2, 'icon' => '📋'],
            ['label' => 'Retard', 'points' => -1, 'icon' => '⏰'],
            ['label' => 'Comportement', 'points' => -2, 'icon' => '⚠️'],
        ];

        foreach ($positive as $i => $row) {
            PointBehavior::query()->updateOrCreate(
                ['label' => $row['label'], 'type' => 'positive'],
                ['points' => $row['points'], 'icon' => $row['icon'], 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        foreach ($negative as $i => $row) {
            PointBehavior::query()->updateOrCreate(
                ['label' => $row['label'], 'type' => 'negative'],
                ['points' => abs($row['points']), 'icon' => $row['icon'], 'sort_order' => $i + 1, 'is_active' => true]
            );
        }
    }
}
