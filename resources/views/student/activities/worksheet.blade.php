@extends('layouts.student-activity')

@section('title', $activity->title . ' — EduSphere')

@section('content')
@if ($activity->hasReadingMaterial())
    @include('student.activities._worksheet_with_reading', ['activity' => $activity, 'submission' => $submission])
@else
    <div
        class="flex min-h-0 flex-1 flex-col bg-slate-950"
        style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
    >
        @include('student.activities._worksheet_header', ['activity' => $activity])

        @if (! $activity->worksheetPdfUrl())
            <p class="flex flex-1 items-center justify-center text-slate-400">Aucune feuille disponible.</p>
        @else
            <x-worksheet-viewer
                :activity="$activity"
                :submission="$submission"
                mode="student"
                :read-only="! $submission->canEditWorksheet()"
            />
        @endif
    </div>
@endif
@endsection
