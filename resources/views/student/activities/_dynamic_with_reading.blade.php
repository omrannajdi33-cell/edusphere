@php $initialView = 'reading'; @endphp
<div
    class="relative h-dvh w-full"
    x-data="{ view: @json($initialView) }"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    <section
        x-show="view === 'reading'"
        class="absolute inset-0 flex flex-col bg-gradient-to-b from-amber-50 to-orange-50"
        style="display: none"
    >
        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-amber-200 bg-white/90 px-4 py-3 sm:px-6">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase text-amber-700">Étape 1 — Lecture</p>
                <h1 class="truncate text-base font-extrabold text-slate-800 sm:text-lg">{{ $activity->title }}</h1>
            </div>
            <button type="button" @click="view = 'exercise'" class="shrink-0 rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-extrabold text-white sm:px-6">
                Exercice →
            </button>
        </header>
        <div class="relative min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
            @if ($activity->hasReadingPdf())
                <iframe src="{{ $activity->readingPdfUrl() }}" class="absolute inset-0 h-full w-full border-0 bg-white" title="Texte PDF"></iframe>
            @else
                <article class="mx-auto max-w-4xl whitespace-pre-wrap font-serif text-lg leading-relaxed text-slate-800 sm:text-xl">
                    {{ $activity->reading_text }}
                </article>
            @endif
        </div>
        <footer class="shrink-0 border-t border-amber-200 bg-white p-4 sm:p-5">
            <button type="button" @click="view = 'exercise'" class="w-full rounded-2xl bg-indigo-600 py-4 text-lg font-extrabold text-white">
                J'ai lu — Passer à l'exercice →
            </button>
        </footer>
    </section>

    <section
        x-show="view === 'exercise'"
        class="absolute inset-0 flex flex-col bg-gradient-to-b from-sky-50 to-indigo-50"
    >
        @include('student.activities._dynamic_exercise_body', ['activity' => $activity, 'submission' => $submission, 'alpineToolbar' => true])
    </section>
</div>
