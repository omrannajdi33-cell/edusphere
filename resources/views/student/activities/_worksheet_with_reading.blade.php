@php $initialView = 'reading'; @endphp
<div
    class="relative flex min-h-0 flex-1 flex-col bg-slate-950"
    x-data="{ view: @json($initialView) }"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    <section x-show="view === 'reading'" class="absolute inset-0 flex flex-col bg-gradient-to-b from-amber-950 to-orange-950" style="display:none">
        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-white/10 px-4 py-3">
            <div>
                <p class="text-xs font-bold uppercase text-amber-300">Lecture</p>
                <h1 class="text-lg font-extrabold text-white">{{ $activity->title }}</h1>
            </div>
            <button type="button" @click="view = 'worksheet'" class="touch-target rounded-2xl bg-teal-500 px-5 py-3 text-sm font-extrabold text-white">Feuille →</button>
        </header>
        <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
            @if ($activity->hasReadingPdf())
                <iframe src="{{ $activity->readingPdfUrl() }}" class="h-full min-h-[60dvh] w-full rounded-2xl border-0 bg-white"></iframe>
            @else
                <article class="mx-auto max-w-3xl whitespace-pre-wrap font-serif text-lg leading-relaxed text-amber-50">{{ $activity->reading_text }}</article>
            @endif
        </div>
    </section>

    <section x-show="view === 'worksheet'" class="absolute inset-0 flex flex-col">
        @include('student.activities._worksheet_header', ['activity' => $activity])
        <x-worksheet-viewer :activity="$activity" :submission="$submission" mode="student" :read-only="! $submission->canEditWorksheet()" />
    </section>
</div>
