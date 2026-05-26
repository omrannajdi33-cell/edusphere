@extends('layouts.student-activity')

@section('title', $activity->title . ' — Lecture')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="relative flex h-dvh w-full flex-col"
    x-data="lectureModule(@js([
        'initialView' => 'reading',
        'hasReading' => $activity->hasReadingMaterial(),
        'readingText' => $activity->reading_text,
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        @include('modules.partials.purpose-bar')

        <section x-show="view === 'reading'" x-cloak class="absolute inset-0 flex flex-col">
            @include('modules.partials.header', ['headerActions' => '<button type="button" @click="openExercise()" class="edu-btn-primary text-sm">Exercice →</button>'])

            <div class="edu-module-toolbar">
                <button type="button" class="edu-module-tool" @click="zoomOut()">A−</button>
                <button type="button" class="edu-module-tool" @click="zoomIn()">A+</button>
                <button type="button" class="edu-module-tool" :class="lineMode && 'is-active'" @click="toggleLineMode()">Ligne par ligne</button>
                <button type="button" class="edu-module-tool" :class="highlightMode && 'is-active'" @click="toggleHighlightMode()">Surligner</button>
                <button type="button" class="edu-module-tool" x-show="highlightMode" @click="highlightSelection()">Marquer la sélection</button>
                <span class="edu-module-save ml-auto" :class="{ 'is-saving': saveStatus === 'saving', 'is-saved': saveStatus === 'saved' }" x-text="saveStatus === 'saved' ? 'Sauvegardé' : (saveStatus === 'saving' ? 'Sauvegarde…' : '')"></span>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto bg-white/50 p-4 sm:p-8">
                @if ($activity->hasReadingPdf())
                    <iframe src="{{ $activity->readingPdfUrl() }}" class="h-full min-h-[65dvh] w-full rounded-2xl border border-white/80 bg-white shadow-md" title="Texte PDF"></iframe>
                @else
                    <article class="edu-module-reading mx-auto max-w-4xl" :style="`font-size: ${zoom}%`">
                        <template x-if="!lineMode">
                            <div class="space-y-6 whitespace-pre-wrap">{!! nl2br(e($activity->reading_text)) !!}</div>
                        </template>
                        <template x-if="lineMode">
                            <div>
                                <p class="mb-4 text-sm font-semibold text-indigo-600" x-text="`Paragraphe ${lineIndex + 1} / ${paragraphs.length}`"></p>
                                <p class="whitespace-pre-wrap" x-text="paragraphs[lineIndex]"></p>
                                <div class="mt-6 flex gap-2">
                                    <button type="button" class="edu-btn-secondary" @click="prevLine()" :disabled="lineIndex === 0">← Précédent</button>
                                    <button type="button" class="edu-btn-primary" @click="nextLine()" :disabled="lineIndex >= paragraphs.length - 1">Suivant →</button>
                                </div>
                            </div>
                        </template>
                    </article>
                @endif

                <div class="mx-auto mt-6 max-w-4xl" x-show="highlights.length">
                    <p class="edu-kicker mb-2">Surlignages</p>
                    <template x-for="(item, index) in highlights" :key="index">
                        <div class="edu-glass mb-2 flex items-center justify-between px-4 py-2 text-sm">
                            <span x-text="item"></span>
                            <button type="button" class="text-rose-500" @click="removeHighlight(index)">×</button>
                        </div>
                    </template>
                </div>
            </div>

            <footer class="shrink-0 border-t border-white/70 bg-white/60 p-4 backdrop-blur-xl">
                <button type="button" @click="openExercise()" class="edu-btn-primary w-full py-3.5">J'ai lu — Passer à l'exercice →</button>
            </footer>
        </section>

        <section x-show="view === 'exercise'" x-cloak class="absolute inset-0 flex flex-col">
            <header class="edu-module-header flex shrink-0 items-center justify-between gap-3 border-b border-white/70 bg-white/60 px-4 py-3 backdrop-blur-xl">
                <div class="min-w-0">
                    <p class="edu-kicker">Exercice / examen</p>
                    <h1 class="truncate text-base font-bold text-slate-900">{{ $activity->title }}</h1>
                </div>
                <button type="button" x-show="hasReading" @click="openReading()" class="edu-btn-secondary shrink-0 text-sm">📖 Retour lecture</button>
            </header>

            <main class="min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6">
                @if ($activity->description)
                    <p class="mb-4 text-center text-sm text-slate-600">{{ $activity->description }}</p>
                @endif
                @include('modules.partials.questions-form')
            </main>

            @include('modules.partials.submit-footer')
        </section>
    @endunless
</div>
@endsection
