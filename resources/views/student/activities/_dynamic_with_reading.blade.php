@php $initialView = 'reading'; @endphp
<div
    class="relative h-dvh w-full"
    x-data='{ view: @json($initialView) }'
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    <section x-show="view === 'reading'" class="absolute inset-0 flex flex-col" style="display: none">
        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-white/70 bg-white/60 px-4 py-3 backdrop-blur-xl sm:px-6">
            <div class="min-w-0">
                <p class="edu-kicker">Étape 1 — Lecture</p>
                <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg">{{ $activity->title }}</h1>
            </div>
            <button type="button" @click="view = 'exercise'" class="edu-btn-primary shrink-0 text-sm">Exercice →</button>
        </header>
        <div class="relative min-h-0 flex-1 overflow-y-auto bg-white/40 p-4 sm:p-6">
            @if ($activity->hasReadingPdf())
                <iframe src="{{ $activity->readingPdfUrl() }}" class="absolute inset-0 h-full w-full border-0 bg-white" title="Texte PDF"></iframe>
            @else
                <article class="mx-auto max-w-4xl text-lg leading-relaxed text-slate-800 sm:text-xl">{{ $activity->reading_text }}</article>
            @endif
        </div>
        <footer class="shrink-0 border-t border-white/70 bg-white/60 p-4 backdrop-blur-xl sm:p-5">
            <button type="button" @click="view = 'exercise'" class="edu-btn-primary w-full py-3.5 text-base">J'ai lu — Passer à l'exercice →</button>
        </footer>
    </section>

    <section x-show="view === 'exercise'" class="absolute inset-0 flex flex-col">
        @include('student.activities._dynamic_exercise_body', ['activity' => $activity, 'submission' => $submission, 'alpineToolbar' => true])
    </section>
</div>
