@extends('layouts.student-activity')

@section('title', $activity->title . ' — Calcul')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="calculModule(@js([
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
            <div class="mx-auto max-w-3xl space-y-5">
                @if ($activity->description)
                    <p class="edu-glass px-4 py-3 text-center text-sm text-slate-700">{{ $activity->description }}</p>
                @endif

                <div class="edu-glass p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <p class="edu-kicker">Brouillon numérique</p>
                        <button type="button" class="edu-btn-secondary text-sm" @click="showScratch = !showScratch" x-text="showScratch ? 'Masquer' : 'Afficher'"></button>
                    </div>
                    <div x-show="showScratch" x-cloak>
                        <textarea x-model="scratch" @input="scheduleSave()" rows="3" class="edu-textarea mb-3 font-mono text-lg" placeholder="0123456789+-×÷"></textarea>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="key in ['7','8','9','÷','4','5','6','×','1','2','3','−','0','.','C','+']" :key="key">
                                <button type="button" class="edu-module-tool py-3 text-lg" @click="key === 'C' ? clearScratch() : appendDigit(key)" x-text="key"></button>
                            </template>
                        </div>
                    </div>
                </div>

                @include('modules.partials.questions-form')
            </div>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
