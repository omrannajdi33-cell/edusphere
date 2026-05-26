@extends('layouts.student-activity')

@section('title', $activity->title . ' — Sécurité')

@section('content')
@include('modules.partials.context')

<div class="flex h-dvh w-full flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('modules.partials.activity-chrome')
    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        @include('modules.partials.checklist-body', [
            'items' => [
                ['id' => 1, 'label' => 'Règles de sécurité connues', 'done' => false],
                ['id' => 2, 'label' => 'Comportement adapté en piscine', 'done' => false],
                ['id' => 3, 'label' => 'Situation d\'urgence identifiée', 'done' => false],
                ['id' => 4, 'label' => 'Validation du professeur', 'done' => false],
            ],
        ])
        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
