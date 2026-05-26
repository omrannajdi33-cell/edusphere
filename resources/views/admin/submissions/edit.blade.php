@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <p class="text-sm text-slate-500">{{ $submission->activity->competency->subject->name }}</p>
        <h2 class="text-2xl font-extrabold text-slate-800">Corriger : {{ $submission->activity->title }}</h2>
        <p class="text-slate-600">Élève : <strong>{{ $submission->student->name }}</strong></p>
        <p class="text-sm text-rose-600">Score auto (questions QCM, etc.) : {{ $submission->score }} / {{ $submission->max_score }}</p>
    </div>

    <form method="POST" action="{{ route('admin.corrections.update', $submission) }}" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Note finale (sur {{ $submission->max_score }})</label>
            <input type="number" name="score" value="{{ old('score', $submission->score) }}" min="0" max="{{ $submission->max_score }}" required class="w-full rounded-xl border px-4 py-3">
            <p class="mt-1 text-xs text-slate-500">Sera comptée au bulletin de l'élève.</p>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Commentaire</label>
            <textarea name="teacher_comment" rows="4" class="w-full rounded-xl border px-4 py-3">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
        </div>
        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Valider la correction</button>
    </form>
</div>
@endsection
