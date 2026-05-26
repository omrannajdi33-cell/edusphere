@extends('layouts.student-activity')

@section('title', $activity->title . ' — Techniques')

@section('content')
@include('modules.partials.context')

<div class="flex h-dvh w-full flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('modules.partials.activity-chrome')
    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        @include('modules.partials.checklist-body', [
            'items' => [
                ['id' => 1, 'label' => 'Position du corps maîtrisée', 'done' => false],
                ['id' => 2, 'label' => 'Mouvement des bras', 'done' => false],
                ['id' => 3, 'label' => 'Mouvement des jambes', 'done' => false],
                ['id' => 4, 'label' => 'Respiration synchronisée', 'done' => false],
                ['id' => 5, 'label' => 'Enchaînement fluide', 'done' => false],
            ],
        ])
        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
