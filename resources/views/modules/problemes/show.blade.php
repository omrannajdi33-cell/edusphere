@extends('layouts.student-activity')

@section('title', $activity->title . ' — Problèmes')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $hasQuestions = $activity->sections->flatMap->questions->contains(fn ($q) => $q->type->isAnswerable());
@endphp

<div
    class="relative flex h-dvh w-full flex-col"
    x-data="problemesWorkspace(@js([
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.purpose-bar')

    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        <div class="edu-module-toolbar shrink-0">
            <a href="{{ route('student.matieres.show', $activity->competency->subject) }}" class="edu-btn-secondary shrink-0 px-3 py-2 text-sm">←</a>
            <button type="button" class="edu-module-tool" :class="tool === 'pen' && 'is-active'" @click="tool = 'pen'">✏️</button>
            <button type="button" class="edu-module-tool" :class="tool === 'highlighter' && 'is-active'" @click="tool = 'highlighter'">🖍</button>
            <button type="button" class="edu-module-tool" :class="tool === 'eraser' && 'is-active'" @click="tool = 'eraser'">🧽</button>
            <button type="button" class="edu-module-tool" @click="clearCanvas()">Effacer</button>
            <button type="button" class="edu-module-tool" :class="showEnonce && 'is-active'" @click="showEnonce = !showEnonce">📋</button>
            <span class="ml-auto truncate text-sm font-semibold text-slate-700">{{ $activity->title }}</span>
            <span class="edu-module-save" :class="{ 'is-saving': saveStatus === 'saving', 'is-saved': saveStatus === 'saved' }" x-text="saveStatus === 'saved' ? '✓' : ''"></span>
        </div>

        <main class="relative min-h-0 flex-1 bg-white">
            <div
                x-show="showEnonce"
                x-cloak
                class="absolute inset-x-3 top-3 z-20 max-h-[40%] overflow-y-auto rounded-2xl border border-white/80 bg-white/95 p-4 shadow-xl backdrop-blur-xl sm:inset-x-auto sm:left-1/2 sm:max-w-lg sm:-translate-x-1/2"
            >
                <div class="mb-2 flex items-center justify-between gap-2">
                    <p class="edu-kicker">Énoncé</p>
                    <button type="button" class="text-slate-400 hover:text-slate-700" @click="showEnonce = false">✕</button>
                </div>
                <div class="text-base leading-relaxed text-slate-800">
                    {{ $activity->description ?: 'Résous le problème sur la feuille.' }}
                </div>
                @foreach ($activity->sections as $section)
                    @foreach ($section->questions as $question)
                        @if ($question->type->isAnswerable())
                            <p class="mt-3 font-semibold text-slate-900">{{ $question->prompt }}</p>
                        @endif
                    @endforeach
                @endforeach
            </div>

            <canvas
                x-ref="canvas"
                class="absolute inset-0 h-full w-full touch-none"
                @pointerdown="startDraw($event)"
                @pointermove="draw($event)"
                @pointerup="endDraw()"
                @pointercancel="endDraw()"
                @pointerleave="endDraw()"
            ></canvas>
        </main>

        <button
            type="button"
            class="fixed z-30 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-2xl text-white shadow-lg shadow-indigo-500/40 transition hover:bg-indigo-700"
            style="right: calc(1rem + env(safe-area-inset-right)); bottom: calc(1rem + env(safe-area-inset-bottom));"
            @click="showAnswer = true"
            title="Ma réponse"
        >
            ✓
        </button>

        <div
            x-show="showAnswer"
            x-cloak
            class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/40 p-4 sm:items-center"
            @click.self="showAnswer = false"
        >
            <div
                class="edu-glass w-full max-w-md overflow-hidden rounded-3xl shadow-2xl"
                @click.stop
            >
                <div class="flex items-center justify-between border-b border-white/60 px-5 py-4">
                    <div>
                        <p class="edu-kicker">Ma réponse</p>
                        <p class="text-sm font-semibold text-slate-800">Saisis ta réponse finale</p>
                    </div>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600" @click="showAnswer = false">✕</button>
                </div>

                <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="max-h-[60dvh] overflow-y-auto p-5">
                    @csrf
                    <input type="hidden" name="module_data[canvas]" :value="exportCanvas()">

                    @if ($hasQuestions)
                        <div class="space-y-4">
                            @foreach ($activity->sections as $section)
                                @foreach ($section->questions as $question)
                                    @if ($question->type->isAnswerable())
                                        @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-600">Ton brouillon sera envoyé au professeur.</p>
                    @endif

                    <button type="submit" class="edu-btn-primary mt-5 w-full py-3.5">
                        {{ $activity->isExam() ? 'Envoyer mon examen ✓' : 'Terminer l\'exercice ✓' }}
                    </button>
                </form>
            </div>
        </div>
    @endunless
</div>
@endsection
