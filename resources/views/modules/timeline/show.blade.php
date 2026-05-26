@extends('layouts.student-activity')

@section('title', $activity->title . ' — Ligne du temps')

@section('content')
@include('modules.partials.context')
@php
    $moduleData = $submission->answers['_module'] ?? [];
    $events = collect($activity->sections->flatMap->questions)->map(fn ($q) => $q->prompt)->filter()->values()->all();
    if ($events === []) {
        $events = ['Invention de l\'écriture', 'Construction des pyramides', 'Chute de Rome', 'Révolution française', 'Premier pas sur la Lune'];
    }
@endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="timelineSorter(@js([
        'events' => $events,
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
            <div class="mx-auto max-w-3xl">
                <p class="edu-glass mb-5 px-4 py-3 text-center text-sm text-slate-700">{{ $activity->description ?: 'Remets les événements dans le bon ordre chronologique.' }}</p>
                <div class="mb-4 flex justify-end">
                    <button type="button" class="edu-btn-secondary text-sm" @click="shuffle()">🔀 Mélanger</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(event, index) in events" :key="`${event}-${index}`">
                        <div
                            class="edu-glass flex cursor-grab items-center gap-4 px-4 py-4 active:cursor-grabbing"
                            draggable="true"
                            @dragstart="dragStart(index)"
                            @dragover.prevent
                            @drop="drop(index)"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700" x-text="index + 1"></span>
                            <p class="flex-1 text-base font-medium text-slate-800" x-text="event"></p>
                            <span class="text-xl text-slate-400">⠿</span>
                        </div>
                    </template>
                </div>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-6 max-w-3xl">
                @csrf
                <template x-for="(event, index) in events" :key="`submit-${index}`">
                    <input type="hidden" :name="`module_data[order][${index}]`" :value="event">
                </template>
            </form>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
