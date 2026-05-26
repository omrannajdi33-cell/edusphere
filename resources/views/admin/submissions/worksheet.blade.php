@extends('layouts.admin')

@section('content')
<div class="flex min-h-[calc(100dvh-8rem)] flex-col gap-4">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-slate-400">{{ $submission->activity->competency->subject->name }}</p>
            <h2 class="text-2xl font-extrabold text-white">{{ $submission->activity->title }}</h2>
            <p class="text-slate-300">Élève : <strong class="text-white">{{ $submission->student->name }}</strong></p>
        </div>
        <a href="{{ route('admin.corrections.index') }}" class="rounded-xl bg-white/10 px-4 py-2 text-sm font-bold text-white">← Retour</a>
    </div>

    <form id="worksheet-correction-form" method="POST" action="{{ route('admin.corrections.update', $submission) }}" class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60">
        @csrf
        @method('PUT')
        <input type="hidden" name="annotations" id="teacher-annotations-input" value="">

        <div class="flex shrink-0 flex-wrap items-end gap-4 border-b border-white/10 p-4">
            <div>
                <label class="mb-1 block text-xs font-bold uppercase text-slate-400">Note /100</label>
                <input type="number" name="score" min="0" max="100" value="{{ old('score', $submission->score) }}" class="w-28 rounded-xl border-0 bg-white/10 px-4 py-2 font-bold text-white">
            </div>
            <div class="min-w-[16rem] flex-1">
                <label class="mb-1 block text-xs font-bold uppercase text-slate-400">Commentaire</label>
                <input type="text" name="teacher_comment" value="{{ old('teacher_comment', $submission->teacher_comment) }}" class="w-full rounded-xl border-0 bg-white/10 px-4 py-2 text-white" placeholder="Bravo, attention aux accords…">
            </div>
            <button type="submit" name="action" value="return" class="touch-target rounded-xl bg-amber-500 px-5 py-3 font-bold text-slate-900">Renvoyer à corriger</button>
            <button type="submit" name="action" value="validate" class="touch-target rounded-xl bg-teal-500 px-5 py-3 font-bold text-white">Valider la correction</button>
        </div>

        <x-worksheet-viewer :activity="$submission->activity" :submission="$submission" mode="teacher" :read-only="false" />
    </form>
</div>

@push('scripts')
<script>
document.getElementById('worksheet-correction-form')?.addEventListener('submit', () => {
    const root = document.querySelector('[data-worksheet-viewer]');
    if (root?._worksheetApi?.getDocument) {
        document.getElementById('teacher-annotations-input').value = JSON.stringify(root._worksheetApi.getDocument());
    }
});
</script>
@endpush
@endsection
