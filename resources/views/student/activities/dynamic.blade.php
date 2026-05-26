@extends('layouts.student-activity')

@section('title', $activity->title . ' — EduSphere')

@section('content')
@if ($activity->hasReadingMaterial())
    @include('student.activities._dynamic_with_reading', ['activity' => $activity, 'submission' => $submission])
@else
    <div class="flex min-h-0 flex-1 flex-col" style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);">
        @include('modules.partials.purpose-bar')
        @include('student.activities._dynamic_exercise_body', ['activity' => $activity, 'submission' => $submission, 'module' => $module])
    </div>
@endif
@endsection
