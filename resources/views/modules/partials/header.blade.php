<header class="edu-module-header flex shrink-0 items-center justify-between gap-3 border-b border-white/70 bg-white/60 px-4 py-3 backdrop-blur-xl sm:px-6">
    <div class="flex min-w-0 items-center gap-3">
        <a href="{{ $backUrl ?? route('student.matieres.show', $activity->competency->subject) }}" class="edu-btn-secondary shrink-0 px-3 py-2 text-sm">←</a>
        <div class="min-w-0">
            <p class="edu-kicker">{{ $module->label() }}</p>
            <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg">{{ $activity->title }}</h1>
        </div>
    </div>
    @isset($headerActions)
        <div class="flex shrink-0 items-center gap-2">{!! $headerActions !!}</div>
    @endisset
</header>
