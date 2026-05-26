@extends('layouts.student-activity')

@section('title', 'Résultat — ' . $activity->title)

@section('content')
<div class="flex min-h-0 flex-1 flex-col bg-slate-950" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
    @include('student.activities._worksheet_header', ['activity' => $activity])

    <div class="shrink-0 border-b border-white/10 bg-slate-900/80 px-4 py-4 text-center">
        @if ($submission->status === \App\SubmissionStatus::Submitted)
            <p class="text-lg font-extrabold text-amber-300">En attente de correction</p>
            <p class="mt-1 text-sm text-slate-400">Le professeur va corriger ta feuille.</p>
        @elseif ($submission->status === \App\SubmissionStatus::Returned)
            <p class="text-lg font-extrabold text-rose-400">À corriger</p>
            <p class="mt-1 text-sm text-slate-400">{{ $submission->teacher_comment ?: 'Le professeur a annoté ta feuille. Corrige et renvoie.' }}</p>
            <a href="{{ route('student.activites.show', $activity) }}" class="mt-4 inline-block rounded-2xl bg-teal-500 px-6 py-3 font-bold text-white">Corriger ma feuille</a>
        @else
            <p class="text-4xl font-extrabold text-teal-300">{{ $submission->score ?? '—' }}<span class="text-2xl text-slate-400">/100</span></p>
            @if ($submission->teacher_comment)
                <p class="mt-3 rounded-2xl bg-white/5 px-4 py-3 text-sm text-slate-300">{{ $submission->teacher_comment }}</p>
            @endif
        @endif
    </div>

    @if ($activity->worksheetPdfUrl())
        <x-worksheet-viewer :activity="$activity" :submission="$submission" mode="student" :read-only="true" />
    @endif
</div>
@endsection
