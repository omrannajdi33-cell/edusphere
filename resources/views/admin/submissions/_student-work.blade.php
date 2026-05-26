@php
    $moduleData = $submission->answers['_module'] ?? [];
    $activity = $submission->activity;
@endphp

<div class="flex h-full min-h-0 flex-col overflow-hidden">
    <div class="shrink-0 border-b border-white/60 p-5">
        <p class="edu-kicker">Travail de l'élève</p>
        <h3 class="text-lg font-bold text-slate-900">{{ $activity->title }}</h3>
        <p class="text-sm text-slate-500">{{ $activity->competency->subject->name }} · {{ $activity->competency->name }}</p>
    </div>

    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
        @if (! empty($moduleData['canvas']))
            <section class="rounded-2xl border border-slate-200 bg-white p-3">
                <p class="edu-kicker mb-2">Feuille de brouillon</p>
                <img src="{{ $moduleData['canvas'] }}" alt="Brouillon" class="w-full rounded-xl">
            </section>
        @endif

        @if (! empty($moduleData['content']))
            <section class="rounded-2xl border border-slate-200 bg-white p-4">
                <p class="edu-kicker mb-2">Production écrite</p>
                <div class="prose max-w-none text-slate-800">{!! $moduleData['content'] !!}</div>
                @if (! empty($moduleData['wordCount']))
                    <p class="mt-2 text-xs text-slate-500">{{ $moduleData['wordCount'] }} mot(s)</p>
                @endif
            </section>
        @endif

        @if (! empty($moduleData['recordings']))
            <section class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4">
                <p class="edu-kicker">Enregistrements oraux</p>
                @foreach ($moduleData['recordings'] as $index => $recording)
                    @if (! empty($recording['data']))
                        <div>
                            <p class="mb-1 text-sm font-semibold">Prise {{ $index + 1 }}</p>
                            <audio controls src="{{ $recording['data'] }}" class="w-full"></audio>
                        </div>
                    @endif
                @endforeach
            </section>
        @endif

        @foreach ($activity->sections as $section)
            @foreach ($section->questions as $question)
                @php $answer = $submission->answers[(string) $question->id] ?? null; @endphp
                @if ($answer !== null && $answer !== '')
                    <section class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="edu-kicker mb-1">Réponse</p>
                        <p class="font-semibold text-slate-900">{{ $question->prompt }}</p>
                        <p class="mt-2 text-slate-700">{{ is_array($answer) ? json_encode($answer, JSON_UNESCAPED_UNICODE) : $answer }}</p>
                    </section>
                @endif
            @endforeach
        @endforeach

        @if (empty($moduleData) && collect($submission->answers)->except('_module')->isEmpty())
            <p class="text-sm text-slate-500">Aucune réponse enregistrée pour cette copie.</p>
        @endif
    </div>
</div>
