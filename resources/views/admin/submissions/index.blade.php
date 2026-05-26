@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="edu-title">Corrections en attente</h2>
        <p class="edu-subtitle mt-1">Feuilles PDF et examens à corriger.</p>
    </div>

    @forelse ($pending as $submission)
        <article class="edu-glass flex flex-wrap items-center justify-between gap-4 p-5">
            <div>
                <p class="font-semibold text-slate-900">{{ $submission->student->name }}</p>
                <p class="text-sm text-slate-600">{{ $submission->activity->title }} — {{ $submission->activity->competency->subject->name }}</p>
                <p class="mt-1 text-xs font-medium text-violet-600">
                    {{ $submission->activity->type === \App\ActivityType::Pdf ? 'Feuille PDF' : 'Examen' }}
                    · {{ $submission->submitted_at?->translatedFormat('d M Y, H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.corrections.edit', $submission) }}" class="edu-btn-primary touch-target">Corriger</a>
        </article>
    @empty
        <p class="edu-glass p-8 text-center text-slate-500">Aucune correction en attente.</p>
    @endforelse
</div>
@endsection
