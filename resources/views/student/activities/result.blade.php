@extends('layouts.student')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-6 pb-24 text-center">
    <header class="edu-glass w-full p-6">
        @if ($activity->isExam())
            <span class="inline-flex items-center gap-1 rounded-full bg-rose-600 px-4 py-1.5 text-sm font-bold uppercase text-white">📋 Examen terminé</span>
        @else
            <span class="inline-flex items-center gap-1 rounded-full bg-sky-500 px-4 py-1.5 text-sm font-bold uppercase text-white">📝 Exercice terminé</span>
        @endif
        <h2 class="mt-3 text-xl font-bold text-slate-900">{{ $activity->title }}</h2>
        @if ($submission->status === \App\SubmissionStatus::Submitted)
            <p class="mt-6 text-2xl font-bold text-amber-600">En attente de correction</p>
            <p class="mt-2 text-slate-600">Le professeur va corriger ta copie.</p>
        @else
            <p class="mt-6 text-5xl font-bold text-indigo-600">{{ $submission->percentage ?? 0 }} %</p>
            <p class="text-lg text-slate-600">{{ $submission->score }} / {{ $submission->max_score }} points</p>
        @endif
        @if ($submission->teacher_comment)
            <p class="edu-inner mt-4 px-4 py-3 text-left text-sm text-slate-700">{{ $submission->teacher_comment }}</p>
        @endif
    </header>

    <a href="{{ route('student.matieres.show', $activity->competency->subject) }}" class="edu-btn-primary inline-flex">← Retour aux matières</a>
</div>
@endsection
