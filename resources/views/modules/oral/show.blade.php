@extends('layouts.student-activity')

@section('title', $activity->title . ' — Oral')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="oralRecorder(@js([
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.activity-chrome')

    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        <main class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
            <div class="mx-auto max-w-2xl space-y-5">
                @if ($activity->description)
                    <p class="edu-glass px-4 py-3 text-center text-sm text-slate-700">{{ $activity->description }}</p>
                @endif

                <div class="edu-glass flex flex-col items-center gap-4 p-8 text-center">
                    <p class="text-5xl">🎙️</p>
                    <p class="text-lg font-semibold text-slate-800">Enregistre ta réponse orale</p>
                    <p class="text-sm text-slate-500">Tu peux réécouter, supprimer et réenregistrer autant que tu veux.</p>

                    <div class="flex flex-wrap justify-center gap-3">
                        <button type="button" class="edu-btn-primary px-8" x-show="!recording" @click="startRecording()">● Enregistrer</button>
                        <button type="button" class="edu-btn-secondary px-8" x-show="recording" @click="stopRecording()">■ Arrêter</button>
                    </div>

                    <p class="text-sm font-medium text-rose-600" x-show="error" x-text="error"></p>
                    <p class="edu-module-save" :class="{ 'is-saving': saveStatus === 'saving', 'is-saved': saveStatus === 'saved' }" x-text="saveStatus === 'saved' ? 'Enregistrement sauvegardé' : ''"></p>
                </div>

                <template x-for="(rec, index) in recordings" :key="rec.id">
                    <div class="edu-glass flex items-center gap-3 p-4">
                        <span class="text-sm font-bold text-indigo-600" x-text="`Prise ${index + 1}`"></span>
                        <audio class="flex-1" :src="rec.data" controls :x-ref="`audio-${index}`"></audio>
                        <button type="button" class="edu-btn-secondary px-3 py-2 text-sm" @click="play(index)">▶</button>
                        <button type="button" class="text-rose-500" @click="remove(index)">🗑</button>
                    </div>
                </template>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-6 max-w-2xl space-y-4">
                @csrf
                <template x-for="(rec, index) in recordings" :key="rec.id">
                    <input type="hidden" :name="`module_data[recordings][${index}][data]`" :value="rec.data">
                </template>
                @if ($activity->sections->flatMap->questions->count())
                    @foreach ($activity->sections as $section)
                        @foreach ($section->questions as $question)
                            @if ($question->type->isAnswerable())
                                @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </form>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
