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
    <div class="flex shrink-0 flex-wrap items-center gap-2 border-b border-white/70 bg-white/65 px-3 py-2 backdrop-blur-xl sm:gap-3 sm:px-4">
        @if ($mode === 'student')
            <button type="button" data-ws-prev class="edu-btn-secondary touch-target px-3 py-2 text-sm disabled:opacity-40">← Page</button>
            <p data-ws-page-label class="min-w-[7rem] text-center text-sm font-semibold text-slate-700">Page 1</p>
            <button type="button" data-ws-next class="edu-btn-secondary touch-target px-3 py-2 text-sm disabled:opacity-40">Page →</button>
        @else
            <button type="button" data-ws-prev class="edu-btn-secondary touch-target px-3 py-2 text-sm disabled:opacity-40">←</button>
            <p data-ws-page-label class="text-sm font-semibold text-slate-700">Page 1</p>
            <button type="button" data-ws-next class="edu-btn-secondary touch-target px-3 py-2 text-sm disabled:opacity-40">→</button>
        @endif

        @unless ($readOnly)
            <div class="flex flex-wrap items-center gap-2 sm:ml-2">
                <button type="button" data-ws-pen data-tool="pen" data-active="true" class="touch-target rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white data-[active=true]:ring-2 data-[active=true]:ring-indigo-300">✏️ Stylo</button>
                <button type="button" data-ws-highlighter data-tool="highlighter" class="touch-target rounded-xl bg-amber-400 px-3 py-2 text-sm font-semibold text-amber-950 data-[active=true]:ring-2 data-[active=true]:ring-amber-200">🖍 Surligner</button>
                <button type="button" data-ws-eraser data-tool="eraser" class="edu-btn-secondary touch-target px-3 py-2 text-sm data-[active=true]:ring-2 data-[active=true]:ring-slate-300">⌫ Gomme</button>
                <button type="button" data-ws-undo class="edu-btn-secondary touch-target px-3 py-2 text-sm">↩ Annuler</button>
            </div>
        @endunless

        <p data-ws-status class="ml-auto text-xs font-medium text-slate-500" data-tone="muted">Chargement…</p>
    </div>

    <div data-ws-alert hidden class="shrink-0 border-b px-4 py-2 text-sm font-medium data-[tone=warning]:border-amber-200 data-[tone=warning]:bg-amber-50 data-[tone=warning]:text-amber-800 data-[tone=info]:border-sky-200 data-[tone=info]:bg-sky-50 data-[tone=info]:text-sky-800"></div>

    <div class="relative min-h-0 flex-1 overflow-auto bg-slate-200/80 p-3 sm:p-4">
        <div class="mx-auto flex min-h-full items-start justify-center">
            <div data-ws-stage class="relative shadow-lg">
                <canvas data-ws-pdf class="absolute inset-0 block"></canvas>
                <canvas data-ws-draw class="absolute inset-0 block touch-none"></canvas>
                <canvas data-ws-teacher class="pointer-events-none absolute inset-0 block"></canvas>
            </div>
        </div>
    </div>

    @if ($mode === 'student' && ! $readOnly)
        <footer class="shrink-0 border-t border-white/70 bg-white/65 p-4 backdrop-blur-xl">
            <button type="button" data-ws-submit class="edu-btn-primary touch-target w-full py-3.5 text-base">
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
