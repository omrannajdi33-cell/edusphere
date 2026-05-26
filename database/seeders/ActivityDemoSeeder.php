<?php

namespace Database\Seeders;

use App\ActivityPurpose;
use App\ActivityType;
use App\Models\Activity;
use App\Models\ActivitySection;
use App\Models\Competency;
use App\Models\Question;
use App\QuestionType;
use App\StudentLevel;
use Illuminate\Database\Seeder;

class ActivityDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLectureDemo();
        $this->seedEcritureDemo();
        $this->seedOralDemo();
        $this->seedProblemesDemo();
    }

    private function seedLectureDemo(): void
    {
        $competency = Competency::query()->where('slug', 'lecture-comprehension')->first();

        if (! $competency) {
            return;
        }

        $activity = Activity::query()->updateOrCreate(
            ['competency_id' => $competency->id, 'slug' => 'chat-botte'],
            [
                'title' => 'Le Chat Botté — Compréhension',
                'type' => ActivityType::Dynamic,
                'purpose' => ActivityPurpose::Exercise,
                'description' => 'Lis bien le texte — tu peux le rouvrir avec le bouton Retour lecture.',
                'reading_text' => <<<'TEXT'
Il était une fois un pauvre meunier qui, en mourant, laissa à ses trois fils ses seules possessions : la minoterie à l'aîné, un âne au cadet, et un chat au plus jeune.

Le jeune garçon était bien triste. « Mon frère pourra travailler ensemble, dit-il, mais moi, je n'ai qu'un chat ! »

Le chat, qui avait entendu ces paroles, se redressa et dit d'une voix claire : « Ne t'inquiète pas, maître. Donne-moi seulement un sac et une paire de bottes, et tu verras que je ne suis pas un si mauvais héritage. »

Le jeune homme, très surpris qu'un chat parle, lui donna ce qu'il demandait. Le Chat Botté attrapa des lapins, les mit dans son sac et offrit une volaille au roi en disant que c'était un cadeau de son maître, un grand seigneur.

Peu à peu, grâce à son intelligence, le chat fit croire au roi que le jeune meunier était un puissant personnage, et même qu'il possédait un magnifique château. À la fin, le roi offrit la main de sa fille au jeune homme, qui devint riche et heureux — tout cela grâce à son fidèle Chat Botté.
TEXT,
                'level' => StudentLevel::Level1,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $section = $this->resetSection($activity);

        Question::query()->create([
            'activity_section_id' => $section->id,
            'type' => QuestionType::MultipleChoice,
            'prompt' => 'Qui est le héros principal du conte ?',
            'points' => 2,
            'sort_order' => 1,
            'config' => [
                'options' => [
                    ['id' => 'a', 'text' => 'Le chat', 'correct' => true],
                    ['id' => 'b', 'text' => 'Le géant', 'correct' => false],
                    ['id' => 'c', 'text' => 'Le roi', 'correct' => false],
                ],
            ],
        ]);

        Question::query()->create([
            'activity_section_id' => $section->id,
            'type' => QuestionType::TrueFalse,
            'prompt' => 'Le chat parle et aide son maître.',
            'points' => 1,
            'sort_order' => 2,
            'config' => ['correct' => true],
        ]);
    }

    private function seedEcritureDemo(): void
    {
        $competency = Competency::query()->where('slug', 'ecriture')->first();

        if (! $competency) {
            return;
        }

        Activity::query()->updateOrCreate(
            ['competency_id' => $competency->id, 'slug' => 'recit-imagine'],
            [
                'title' => 'Récit imaginaire — Mon héros',
                'type' => ActivityType::Dynamic,
                'purpose' => ActivityPurpose::Exercise,
                'description' => 'Écris un récit d\'au moins 80 mots sur un héros qui te ressemble. Utilise les outils de mise en forme pour structurer ton texte.',
                'level' => StudentLevel::Level1,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );
    }

    private function seedOralDemo(): void
    {
        $competency = Competency::query()->where('slug', 'communication-orale')->first();

        if (! $competency) {
            return;
        }

        $activity = Activity::query()->updateOrCreate(
            ['competency_id' => $competency->id, 'slug' => 'presentation-oral'],
            [
                'title' => 'Présentation orale — Mon animal préféré',
                'type' => ActivityType::Dynamic,
                'purpose' => ActivityPurpose::Exercise,
                'description' => 'Enregistre une présentation de 1 minute sur ton animal préféré.',
                'level' => StudentLevel::Level1,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $section = $this->resetSection($activity);

        Question::query()->create([
            'activity_section_id' => $section->id,
            'type' => QuestionType::ShortAnswer,
            'prompt' => 'Quel animal as-tu choisi ?',
            'points' => 1,
            'sort_order' => 1,
            'config' => ['acceptable' => []],
        ]);
    }

    private function seedProblemesDemo(): void
    {
        $competency = Competency::query()->where('slug', 'resolution-de-problemes')->first();

        if (! $competency) {
            return;
        }

        $activity = Activity::query()->updateOrCreate(
            ['competency_id' => $competency->id, 'slug' => 'probleme-boulangerie'],
            [
                'title' => 'Problème — La boulangerie',
                'type' => ActivityType::Dynamic,
                'purpose' => ActivityPurpose::Exercise,
                'grading_mode' => \App\GradingMode::Manual,
                'description' => 'Ali achète 3 croissants à 1,20 € chacun et 2 pains à 2,50 € chacun. Combien paie-t-il en tout ? Utilise la feuille de brouillon pour tes calculs.',
                'level' => StudentLevel::Level1,
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $section = $this->resetSection($activity);

        Question::query()->create([
            'activity_section_id' => $section->id,
            'type' => QuestionType::Numeric,
            'prompt' => 'Total à payer (en euros)',
            'points' => 2,
            'sort_order' => 1,
            'config' => ['answer' => 8.6, 'tolerance' => 0.01],
        ]);
    }

    private function resetSection(Activity $activity): ActivitySection
    {
        $section = $activity->sections()->firstOrCreate(
            ['title' => 'Questions'],
            ['sort_order' => 1]
        );

        $section->update(['title' => 'Questions']);
        $section->questions()->delete();

        return $section;
    }
}
