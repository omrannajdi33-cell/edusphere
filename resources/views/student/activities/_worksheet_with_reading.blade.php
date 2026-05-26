@php $initialView = 'reading'; @endphp
<div
    class="relative flex min-h-0 flex-1 flex-col"
    x-data='{ view: @json($initialView) }'
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    <section x-show="view === 'reading'" class="absolute inset-0 flex flex-col" style="display:none">
        <header class="flex shrink-0 items-center justify-between gap-3 border-b border-white/70 bg-white/60 px-4 py-3 backdrop-blur-xl">
            <div>
                <p class="edu-kicker">Lecture</p>
                <h1 class="text-lg font-bold text-slate-900">{{ $activity->title }}</h1>
            </div>
            <button type="button" @click="view = 'worksheet'" class="edu-btn-primary touch-target text-sm">Feuille →</button>
        </header>
        <div class="min-h-0 flex-1 overflow-y-auto bg-white/40 p-4 sm:p-6">
            @if ($activity->hasReadingPdf())
                <iframe src="{{ $activity->readingPdfUrl() }}" class="h-full min-h-[60dvh] w-full rounded-2xl border border-white/80 bg-white shadow-md"></iframe>
            @else
                <article class="mx-auto max-w-3xl text-lg leading-relaxed text-slate-800">{{ $activity->reading_text }}</article>
            @endif
        </div>
    </section>

    <section x-show="view === 'worksheet'" class="absolute inset-0 flex flex-col">
        @include('student.activities._worksheet_header', ['activity' => $activity])
        <x-worksheet-viewer :activity="$activity" :submission="$submission" mode="student" :read-only="! $submission->canEditWorksheet()" />
    </section>
</div>
