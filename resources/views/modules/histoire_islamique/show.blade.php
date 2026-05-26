@extends('layouts.student-activity')

@section('title', $activity->title . ' — Histoire islamique')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $events = ['Hégire', 'Califat des Rashidun', 'Omeyyades', 'Abbassides', 'Andalousie'];
@endphp

<div class="flex h-dvh w-full flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('modules.partials.activity-chrome')

    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        <div
            class="min-h-0 flex-1 overflow-y-auto"
            x-data="timelineSorter(@js([
                'events' => $events,
                'progressUrl' => route('student.activites.progress', $activity),
                'csrf' => csrf_token(),
                'saved' => $moduleData,
            ]))"
        >
            <div class="mx-auto max-w-3xl p-4 sm:p-6">
                <p class="edu-glass mb-4 px-4 py-3 text-sm text-slate-700">{{ $activity->description ?: 'Classe les périodes historiques dans l\'ordre.' }}</p>
                <div class="space-y-3">
                    <template x-for="(event, index) in events" :key="`${event}-${index}`">
                        <div class="edu-glass flex items-center gap-4 px-4 py-4" draggable="true" @dragstart="dragStart(index)" @dragover.prevent @drop="drop(index)">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-700" x-text="index + 1"></span>
                            <p class="flex-1 font-medium" x-text="event"></p>
                        </div>
                    </template>
                </div>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto max-w-3xl px-4 pb-4">
                @csrf
                <template x-for="(event, index) in events" :key="`order-${index}`">
                    <input type="hidden" :name="`module_data[order][${index}]`" :value="event">
                </template>
                @foreach ($activity->sections as $section)
                    @foreach ($section->questions as $question)
                        @if ($question->type->isAnswerable())
                            @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                        @endif
                    @endforeach
                @endforeach
            </form>
        </div>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
