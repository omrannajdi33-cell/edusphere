@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <a href="{{ route('student.matieres.index') }}" class="edu-link">← Matières</a>
    <div class="flex items-center gap-4">
        <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-4xl shadow-md" style="background: color-mix(in srgb, {{ $subject->color }} 25%, white);">
            {{ $subject->icon }}
        </div>
        <div>
            <h2 class="edu-title">{{ $subject->name }}</h2>
            <p class="edu-subtitle">Niveau {{ $student->level?->shortLabel() }}</p>
        </div>
    </div>

    @foreach ($subject->competencies as $competency)
        @if ($competency->activities->isNotEmpty())
            <section class="edu-glass p-5">
                <h3 class="text-lg font-bold text-slate-900">{{ $competency->name }}</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($competency->activities as $activity)
                        <a href="{{ route('student.activites.show', $activity) }}" class="edu-inner edu-card-hover block touch-target overflow-hidden px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        @if ($activity->isExam())
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                                                📋 Examen
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-sky-500 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">
                                                📝 Exercice
                                            </span>
                                        @endif
                                        @if ($activity->isExam() && $activity->exam_duration_minutes)
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">⏱ {{ $activity->exam_duration_minutes }} min</span>
                                        @endif
                                    </div>
                                    <p class="font-semibold text-slate-900">{{ $activity->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $activity->purpose->studentDescription() }}</p>
                                </div>
                                <span class="shrink-0 text-xl text-indigo-500">→</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($completedActivities->isNotEmpty())
        <section class="edu-glass p-5">
            <h3 class="text-lg font-bold text-slate-700">Activités terminées</h3>
            <p class="mt-1 text-sm text-slate-500">Tu ne peux plus les refaire — consulte ton résultat.</p>
            <div class="mt-4 space-y-2">
                @foreach ($completedActivities as $activity)
                    @php $sub = $activity->submissions->first(); @endphp
                    <a href="{{ route('student.activites.resultat', $activity) }}" class="edu-inner flex items-center justify-between px-4 py-3 opacity-80 hover:opacity-100">
                        <div>
                            <p class="font-medium text-slate-700">{{ $activity->title }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $activity->isExam() ? 'Examen' : 'Exercice' }} ·
                                @if ($sub?->status === \App\SubmissionStatus::Graded && $sub->score !== null)
                                    {{ $sub->score }}/{{ $sub->max_score }}
                                @else
                                    En attente de correction
                                @endif
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-indigo-600">Résultat →</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($subject->competencies->every(fn ($c) => $c->activities->isEmpty()) && $completedActivities->isEmpty())
        <p class="edu-glass p-6 text-center text-slate-500">Aucune activité disponible pour le moment.</p>
    @endif
</div>
@endsection
