<header class="flex shrink-0 items-center gap-2 border-b border-white/10 bg-slate-900/95 px-3 py-2 backdrop-blur-xl sm:gap-3 sm:px-4 sm:py-3">
    <a href="{{ route('student.matieres.show', $activity->competency->subject) }}" class="touch-target shrink-0 rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white">
        ← Quitter
    </a>
    <div class="min-w-0 flex-1">
        <p class="truncate text-xs font-semibold text-teal-300">{{ $activity->competency->subject->name }} · {{ $activity->competency->name }}</p>
        <p class="truncate text-sm font-extrabold text-white sm:text-base">{{ $activity->title }}</p>
    </div>
    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold {{ $activity->purpose->badgeColor() }}">{{ $activity->purpose->label() }}</span>
</header>
