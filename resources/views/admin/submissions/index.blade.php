@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-3xl font-extrabold text-white">Corrections en attente</h2>
        <p class="text-slate-400">Feuilles PDF et examens à corriger.</p>
    </div>

    @forelse ($pending as $submission)
        <article class="edu-card flex flex-wrap items-center justify-between gap-4 p-5">
            <div>
                <p class="font-bold text-white">{{ $submission->student->name }}</p>
                <p class="text-sm text-slate-400">{{ $submission->activity->title }} — {{ $submission->activity->competency->subject->name }}</p>
                <p class="mt-1 text-xs font-semibold {{ $submission->activity->type === \App\ActivityType::Pdf ? 'text-teal-400' : 'text-rose-400' }}">
                    {{ $submission->activity->type === \App\ActivityType::Pdf ? 'Feuille PDF' : 'Examen' }}
                    · {{ $submission->submitted_at?->translatedFormat('d M Y, H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.corrections.edit', $submission) }}" class="touch-target rounded-xl bg-teal-500 px-5 py-3 font-bold text-white">Corriger</a>
        </article>
    @empty
        <p class="edu-card p-8 text-center text-slate-500">Aucune correction en attente.</p>
    @endforelse
</div>
@endsection
