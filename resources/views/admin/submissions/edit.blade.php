@extends('layouts.admin')

@section('content')
<div class="flex h-[calc(100dvh-5rem)] flex-col gap-4">
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $submission->activity->competency->subject->name }}</p>
            <h2 class="edu-title">Corriger : {{ $submission->activity->title }}</h2>
        </div>
        <a href="{{ route('admin.corrections.index') }}" class="edu-btn-secondary">← Retour aux corrections</a>
    </div>

    <div class="grid min-h-0 flex-1 gap-4 lg:grid-cols-2">
        <div class="edu-glass min-h-0 overflow-hidden">
            @include('admin.submissions._student-work')
        </div>
        <div class="min-h-0 overflow-hidden">
            @include('admin.submissions._grading-form')
        </div>
    </div>
</div>
@endsection
