@extends('layouts.student-activity')

@section('title', $activity->title . ' — Activité physique')

@section('content')
@include('modules.partials.context')

<div class="flex h-dvh w-full flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('modules.partials.activity-chrome')
    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        @include('modules.partials.checklist-body', [
            'items' => [
                ['id' => 1, 'label' => 'Échauffement terminé', 'done' => false],
                ['id' => 2, 'label' => 'Exercice principal réalisé', 'done' => false],
                ['id' => 3, 'label' => 'Retour au calme', 'done' => false],
                ['id' => 4, 'label' => 'Hydratation', 'done' => false],
            ],
        ])
        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
