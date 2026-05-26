@extends('layouts.student-activity')

@section('title', $activity->title . ' — Lecture islamique')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $arabicText = $activity->reading_text ?: "بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ\nالْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ";
    $segments = [
        ['label' => 'Partie 1', 'start' => 0],
        ['label' => 'Partie 2', 'start' => 5],
        ['label' => 'Partie 3', 'start' => 10],
    ];
@endphp

<div
    class="relative flex h-dvh w-full flex-col"
    x-data="lectureIslamiqueModule(@js([
        'initialView' => 'reading',
        'hasReading' => true,
        'readingText' => $arabicText,
        'segments' => $segments,
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        @include('modules.partials.purpose-bar')

        <section x-show="view === 'reading'" class="absolute inset-0 flex flex-col">
            @include('modules.partials.header', ['headerActions' => '<button type="button" @click="openExercise()" class="edu-btn-primary text-sm">Exercice →</button>'])

            <div class="edu-module-toolbar">
                <button type="button" class="edu-module-tool" @click="zoomIn()">A+</button>
                <button type="button" class="edu-module-tool" @click="zoomOut()">A−</button>
                <button type="button" class="edu-module-tool" @click="toggleAudio()">🔊 Écouter</button>
                <template x-for="(segment, index) in segments" :key="index">
                    <button type="button" class="edu-module-tool" :class="segmentIndex === index && 'is-active'" @click="playSegment(index)" x-text="segment.label"></button>
                </template>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto bg-white/50 p-6 sm:p-10">
                <article class="edu-arabic-text mx-auto max-w-4xl whitespace-pre-wrap" :style="`font-size: ${zoom}%`">{{ $arabicText }}</article>
            </div>

            <audio x-ref="segmentAudio" class="hidden"></audio>
        </section>

        <section x-show="view === 'exercise'" class="absolute inset-0 flex flex-col">
            <header class="edu-module-header flex shrink-0 items-center justify-between border-b border-white/70 bg-white/60 px-4 py-3 backdrop-blur-xl">
                <h1 class="text-base font-bold">{{ $activity->title }}</h1>
                <button type="button" @click="openReading()" class="edu-btn-secondary text-sm">📖 Retour lecture</button>
            </header>
            <main class="min-h-0 flex-1 overflow-y-auto p-4">
                @include('modules.partials.questions-form')
            </main>
            @include('modules.partials.submit-footer')
        </section>
    @endunless
</div>
@endsection
