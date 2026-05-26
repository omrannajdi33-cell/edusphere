<header class="flex shrink-0 items-center gap-2 border-b border-white/70 bg-white/60 px-3 py-2.5 backdrop-blur-xl sm:gap-3 sm:px-4 sm:py-3">
    <a href="{{ $backUrl ?? route('student.matieres.show', $activity->competency->subject) }}" class="edu-btn-secondary touch-target shrink-0 px-3 py-2 text-sm">
        ← Quitter
    </a>
    <div class="min-w-0 flex-1">
        <p class="truncate text-xs font-medium text-violet-600">{{ $activity->competency->subject->name }} · {{ $activity->competency->name }}</p>
        <p class="truncate text-sm font-bold text-slate-900 sm:text-base">{{ $activity->title }}</p>
    </div>
    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $activity->purpose->badgeColor() }}">{{ $activity->purpose->label() }}</span>
</header>
