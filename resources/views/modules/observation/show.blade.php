@extends('layouts.student-activity')

@section('title', $activity->title . ' — Observation')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $imageUrl = $activity->readingPdfUrl() ?? 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=1200&q=80';
@endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="observationAnnotator(@js([
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.activity-chrome')

    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        <div class="edu-module-toolbar shrink-0">
            <input type="text" x-model="label" placeholder="Nom de l'élément à placer" class="edu-input max-w-xs py-2 text-sm">
            <button type="button" class="edu-module-tool" :class="placing && 'is-active'" @click="togglePlacing()">📍 Placer sur l'image</button>
        </div>

        <main class="min-h-0 flex-1 overflow-y-auto p-4">
            <div class="edu-glass relative mx-auto max-w-5xl overflow-hidden" @click="placePin($event)">
                <img src="{{ $imageUrl }}" alt="Observation" class="max-h-[55dvh] w-full object-cover">
                <template x-for="pin in pins" :key="pin.id">
                    <button type="button" class="edu-module-pin" :style="`left:${pin.x}%;top:${pin.y}%`" @click.stop="removePin(pin.id)" x-text="pin.label"></button>
                </template>
            </div>

            <div class="mx-auto mt-4 max-w-5xl">
                <label class="edu-label">Notes d'observation</label>
                <textarea x-model="notes" @input="saveNotes()" rows="5" class="edu-textarea" placeholder="Décris ce que tu observes…"></textarea>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-4 max-w-5xl space-y-4">
                @csrf
                <input type="hidden" name="module_data[notes]" :value="notes">
                <input type="hidden" name="module_data[pins]" :value="JSON.stringify(pins)">
                @foreach ($activity->sections as $section)
                    @foreach ($section->questions as $question)
                        @if ($question->type->isAnswerable())
                            @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                        @endif
                    @endforeach
                @endforeach
            </form>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
