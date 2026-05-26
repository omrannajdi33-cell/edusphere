@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <a href="{{ route('student.matieres.index') }}" class="text-sm font-bold text-teal-400">← Matières</a>
    <div class="flex items-center gap-4">
        <span class="text-5xl">{{ $subject->icon }}</span>
        <div>
            <h2 class="text-2xl font-extrabold text-white">{{ $subject->name }}</h2>
            <p class="text-sm text-slate-400">Niveau {{ $student->level?->shortLabel() }}</p>
        </div>
    </div>

    @foreach ($subject->competencies as $competency)
        <section class="edu-card p-5">
            <h3 class="text-lg font-extrabold text-white">{{ $competency->name }}</h3>
            <div class="mt-4 space-y-2">
                @forelse ($competency->activities as $activity)
                    <a href="{{ route('student.activites.show', $activity) }}" class="flex touch-target items-center justify-between rounded-2xl bg-white/5 px-4 py-4 transition hover:bg-teal-500/10 active:scale-[0.99]">
                        <div>
                            <p class="font-bold text-white">{{ $activity->title }}</p>
                            <p class="mt-1 flex flex-wrap gap-2 text-sm">
                                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $activity->purpose->badgeColor() }}">{{ $activity->purpose->label() }}</span>
                                <span class="text-slate-400">{{ $activity->type->label() }}</span>
                            </p>
                        </div>
                        <span class="text-2xl text-teal-400">→</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Pas encore d'activité ici.</p>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
@endsection
