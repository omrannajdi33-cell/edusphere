@extends('layouts.student-activity')

@section('title', $activity->title . ' — Écriture')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $prompt = $activity->description ?: 'Écris ton texte ici. Prends ton temps, tu peux reprendre plus tard.';
@endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="ecritureEditor(@js([
        'initialContent' => '',
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
            <button type="button" class="edu-module-tool" @click="exec('bold')"><strong>G</strong></button>
            <button type="button" class="edu-module-tool" @click="exec('italic')"><em>I</em></button>
            <button type="button" class="edu-module-tool" @click="exec('underline')"><u>S</u></button>
            <button type="button" class="edu-module-tool" @click="exec('justifyLeft')">⬅</button>
            <button type="button" class="edu-module-tool" @click="exec('justifyCenter')">↔</button>
            <button type="button" class="edu-module-tool" @click="exec('justifyRight')">➡</button>
            <button type="button" class="edu-module-tool" @click="exec('foreColor', '#dc2626')">A</button>
            <button type="button" class="edu-module-tool" @click="exec('hiliteColor', '#fef08a')">🖍</button>
            <button type="button" class="edu-module-tool" @click="setFontSize(16)">Petit</button>
            <button type="button" class="edu-module-tool" @click="setFontSize(20)">Grand</button>
            <button type="button" class="edu-module-tool" @click="undo()">↶</button>
            <button type="button" class="edu-module-tool" @click="redo()">↷</button>
            <span class="edu-module-save ml-auto" :class="{ 'is-saving': saveStatus === 'saving', 'is-saved': saveStatus === 'saved' }" x-text="saveStatus === 'saved' ? 'Sauvegardé' : (saveStatus === 'saving' ? 'Sauvegarde…' : '')"></span>
            <span class="text-sm font-semibold text-slate-600" x-text="`${wordCount} mot(s)`"></span>
        </div>

        <main class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
            <p class="edu-glass mb-4 px-4 py-3 text-sm font-medium text-slate-700">{{ $prompt }}</p>

            <div
                x-ref="editor"
                contenteditable="true"
                class="edu-module-editor min-h-[62dvh] w-full"
                :style="`font-size: ${fontSize}px`"
                @input="syncContent()"
            ></div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="module_data[content]" :value="content">
                <input type="hidden" name="module_data[wordCount]" :value="wordCount">
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
