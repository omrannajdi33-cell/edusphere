@extends('layouts.student-activity')

@section('title', $activity->title . ' — Expérience')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="experienceLab(@js([
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
            <div class="mx-auto grid max-w-5xl gap-5 lg:grid-cols-[260px_1fr]">
                <aside class="edu-glass p-4">
                    <p class="edu-kicker mb-3">Étapes</p>
                    <template x-for="step in steps" :key="step.id">
                        <label class="mb-2 flex cursor-pointer items-center gap-3 rounded-xl px-3 py-2 hover:bg-white/70">
                            <input type="checkbox" :checked="step.done" @change="toggleStep(step.id)" class="h-5 w-5">
                            <span x-text="step.title" :class="step.done && 'line-through text-slate-400'"></span>
                        </label>
                    </template>
                </aside>

                <section class="space-y-5">
                    <p class="edu-glass px-4 py-3 text-sm text-slate-700">{{ $activity->description ?: 'Suis le protocole et note tes observations.' }}</p>

                    <div class="edu-glass overflow-x-auto p-4">
                        <p class="edu-kicker mb-3">Tableau d'observations</p>
                        <table class="w-full min-w-[640px] text-sm">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="pb-2 pr-3">Moment</th>
                                    <th class="pb-2 pr-3">Observation</th>
                                    <th class="pb-2">Mesure</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in rows" :key="index">
                                    <tr>
                                        <td class="py-2 pr-3"><input type="text" x-model="row.time" @input="saveTable()" class="edu-input py-2"></td>
                                        <td class="py-2 pr-3"><input type="text" x-model="row.observation" @input="saveTable()" class="edu-input py-2"></td>
                                        <td class="py-2"><input type="text" x-model="row.measure" @input="saveTable()" class="edu-input py-2"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <button type="button" class="edu-btn-secondary mt-3 text-sm" @click="addRow()">+ Ajouter une ligne</button>
                    </div>

                    <div class="edu-glass p-4">
                        <label class="edu-label">Conclusion</label>
                        <textarea x-model="conclusion" @input="saveConclusion()" rows="4" class="edu-textarea"></textarea>
                    </div>
                </section>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-5 max-w-5xl space-y-4">
                @csrf
                <input type="hidden" name="module_data[conclusion]" :value="conclusion">
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
