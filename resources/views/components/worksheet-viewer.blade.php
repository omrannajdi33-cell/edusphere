@props([
    'activity',
    'submission',
    'mode' => 'student',
    'readOnly' => false,
])

@php
    $viewerConfig = [
        'pdfUrl' => $activity->worksheetPdfUrl(),
        'saveUrl' => $mode === 'student' && ! $readOnly ? route('student.activites.annotations', $activity) : null,
        'submitUrl' => $mode === 'student' && ! $readOnly ? route('student.activites.submit', $activity) : null,
        'resultUrl' => route('student.activites.resultat', $activity),
        'initialData' => $submission->annotations,
        'mode' => $mode,
        'readOnly' => $readOnly,
        'returned' => $submission->status === \App\SubmissionStatus::Returned,
        'csrf' => csrf_token(),
    ];
@endphp

<div
    class="worksheet-viewer flex min-h-0 flex-1 flex-col"
    data-worksheet-viewer
    data-config='@json($viewerConfig)'
>
    <div class="flex shrink-0 flex-wrap items-center gap-2 border-b border-white/10 bg-slate-900/90 px-3 py-2 backdrop-blur-xl sm:gap-3 sm:px-4">
        @if ($mode === 'student')
            <button type="button" data-ws-prev class="touch-target rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white disabled:opacity-40">← Page</button>
            <p data-ws-page-label class="min-w-[7rem] text-center text-sm font-bold text-white">Page 1</p>
            <button type="button" data-ws-next class="touch-target rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white disabled:opacity-40">Page →</button>
        @else
            <button type="button" data-ws-prev class="touch-target rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white disabled:opacity-40">←</button>
            <p data-ws-page-label class="text-sm font-bold text-white">Page 1</p>
            <button type="button" data-ws-next class="touch-target rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white disabled:opacity-40">→</button>
        @endif

        @unless ($readOnly)
            <div class="flex flex-wrap items-center gap-2 sm:ml-2">
                <button type="button" data-ws-pen data-tool="pen" data-active="true" class="touch-target rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white data-[active=true]:ring-2 data-[active=true]:ring-white">✏️ Stylo</button>
                <button type="button" data-ws-highlighter data-tool="highlighter" class="touch-target rounded-xl bg-yellow-500 px-4 py-2 text-sm font-bold text-slate-900 data-[active=true]:ring-2 data-[active=true]:ring-white">🖍 Surligner</button>
                <button type="button" data-ws-eraser data-tool="eraser" class="touch-target rounded-xl bg-white/15 px-4 py-2 text-sm font-bold text-white data-[active=true]:ring-2 data-[active=true]:ring-white">⌫ Gomme</button>
                <button type="button" data-ws-undo class="touch-target rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white">↩ Annuler</button>
            </div>
        @endunless

        <p data-ws-status class="ml-auto text-xs font-semibold text-emerald-300" data-tone="muted">Chargement…</p>
    </div>

    <div data-ws-alert hidden class="shrink-0 border-b px-4 py-2 text-sm font-semibold data-[tone=warning]:bg-amber-500/20 data-[tone=warning]:text-amber-100 data-[tone=info]:bg-sky-500/20 data-[tone=info]:text-sky-100"></div>

    <div class="relative min-h-0 flex-1 overflow-auto bg-slate-800 p-3 sm:p-4">
        <div class="mx-auto flex min-h-full items-start justify-center">
            <div data-ws-stage class="relative shadow-2xl">
                <canvas data-ws-pdf class="absolute inset-0 block"></canvas>
                <canvas data-ws-draw class="absolute inset-0 block touch-none"></canvas>
                <canvas data-ws-teacher class="pointer-events-none absolute inset-0 block"></canvas>
            </div>
        </div>
    </div>

    @if ($mode === 'student' && ! $readOnly)
        <footer class="shrink-0 border-t border-white/10 bg-slate-900/95 p-4 backdrop-blur-xl">
            <button type="button" data-ws-submit class="touch-target w-full rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-500 py-4 text-lg font-extrabold text-white shadow-lg">
                Envoyer ma feuille au professeur ✓
            </button>
        </footer>
    @endif
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-worksheet-viewer]').forEach((root) => {
                    if (root.dataset.initialized) return;
                    root.dataset.initialized = '1';
                    const config = JSON.parse(root.dataset.config || '{}');
                    root._worksheetApi = window.initWorksheetViewer?.(root, config);
                });
            });
        </script>
    @endpush
@endonce
