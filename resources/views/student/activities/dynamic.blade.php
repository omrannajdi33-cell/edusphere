@extends('layouts.student-activity')

@section('title', $activity->title . ' — EduSphere')

@section('content')
@if ($activity->hasReadingMaterial())
    @include('student.activities._dynamic_with_reading', ['activity' => $activity, 'submission' => $submission])
@else
    <div class="flex min-h-0 flex-1 flex-col bg-slate-950" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
        @include('student.activities._dynamic_exercise_body', ['activity' => $activity, 'submission' => $submission])
    </div>
@endif
@endsection
