@extends('layouts.student')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-6 pb-24 text-center" x-data="{ view: 'result' }">
    <header class="w-full rounded-3xl bg-white p-6 shadow-sm">
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $activity->purpose->badgeColor() }}">{{ $activity->purpose->label() }}</span>
        <h2 class="mt-3 text-xl font-extrabold text-slate-800">{{ $activity->title }}</h2>
        @if ($submission->status === \App\SubmissionStatus::Submitted)
            <p class="mt-6 text-2xl font-extrabold text-amber-600">En attente de correction</p>
            <p class="mt-2 text-slate-600">Ta note d'examen sera mise à jour au bulletin.</p>
        @else
            <p class="mt-6 text-5xl font-extrabold text-indigo-600">{{ $submission->percentage ?? 0 }} %</p>
            <p class="text-lg text-slate-600">{{ $submission->score }} / {{ $submission->max_score }} points</p>
        @endif
    </header>

    @if ($activity->isExercise() && $submission->status === \App\SubmissionStatus::Graded)
        <a href="{{ route('student.activites.show', $activity) }}" class="inline-block rounded-2xl bg-sky-600 px-6 py-3 font-bold text-white">Refaire l'exercice</a>
    @endif
    <a href="{{ route('student.matieres.show', $activity->competency->subject) }}" class="block font-bold text-indigo-600">← Matières</a>
</div>
@endsection
