@extends('layouts.student-activity')

@section('title', 'Résultat — ' . $activity->title)

@section('content')
<div class="flex min-h-0 flex-1 flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('student.activities._worksheet_header', ['activity' => $activity])

    <div class="shrink-0 border-b border-white/70 bg-white/55 px-4 py-4 text-center backdrop-blur-xl">
        @if ($submission->status === \App\SubmissionStatus::Submitted)
            <p class="text-lg font-bold text-amber-600">En attente de correction</p>
            <p class="mt-1 text-sm text-slate-600">Le professeur va corriger ta feuille.</p>
        @elseif ($submission->status === \App\SubmissionStatus::Returned)
            <p class="text-lg font-bold text-rose-600">À corriger</p>
            <p class="mt-1 text-sm text-slate-600">{{ $submission->teacher_comment ?: 'Le professeur a annoté ta feuille. Corrige et renvoie.' }}</p>
            <a href="{{ route('student.activites.show', $activity) }}" class="edu-btn-primary mt-4 inline-flex">Corriger ma feuille</a>
        @else
            <p class="text-4xl font-bold text-indigo-600">{{ $submission->score ?? '—' }}<span class="text-2xl text-slate-400">/100</span></p>
            @if ($submission->teacher_comment)
                <p class="edu-inner mt-3 px-4 py-3 text-sm text-slate-700">{{ $submission->teacher_comment }}</p>
            @endif
            <a href="{{ route('student.matieres.show', $activity->competency->subject) }}" class="edu-btn-primary mt-4 inline-flex">← Retour aux matières</a>
        @endif
    </div>

    @if ($activity->worksheetPdfUrl())
        <x-worksheet-viewer :activity="$activity" :submission="$submission" mode="student" :read-only="true" />
    @endif
</div>
@endsection
