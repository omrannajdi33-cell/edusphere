@extends('layouts.admin')

@section('content')
<div class="flex h-[calc(100dvh-5rem)] flex-col gap-4">
    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3">
        <div>
            <p class="edu-kicker">{{ $submission->activity->competency->subject->name }}</p>
            <h2 class="edu-title">{{ $submission->activity->title }}</h2>
            <p class="edu-subtitle">Élève : <strong>{{ $submission->student->name }}</strong></p>
        </div>
        <a href="{{ route('admin.corrections.index') }}" class="edu-btn-secondary">← Retour</a>
    </div>

    <form id="worksheet-correction-form" method="POST" action="{{ route('admin.corrections.update', $submission) }}" class="grid min-h-0 flex-1 gap-4 lg:grid-cols-2">
        @csrf
        @method('PUT')
        <input type="hidden" name="annotations" id="teacher-annotations-input" value="">

        <div class="edu-glass flex min-h-0 flex-col overflow-hidden">
            <div class="shrink-0 border-b border-white/60 p-4">
                <p class="edu-kicker">Feuille de l'élève</p>
            </div>
            <div class="min-h-0 flex-1 overflow-hidden">
                <x-worksheet-viewer :activity="$submission->activity" :submission="$submission" mode="teacher" :read-only="false" />
            </div>
        </div>

        <div class="edu-glass flex min-h-0 flex-col overflow-hidden">
            @php
                $details = old('grading_details', $submission->grading_details ?? []);
                $criteria = \App\GradingCriterion::options();
            @endphp
            <div class="shrink-0 border-b border-white/60 p-5">
                <p class="edu-kicker">Fiche de correction</p>
            </div>
            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                <div>
                    <label class="edu-label">Note /100</label>
                    <input type="number" name="score" min="0" max="100" value="{{ old('score', $submission->score) }}" class="edu-input text-lg font-bold">
                </div>
                <div>
                    <label class="edu-label">Commentaire</label>
                    <textarea name="teacher_comment" rows="3" class="edu-textarea">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
                </div>
                <div>
                    <label class="edu-label">Genre / type</label>
                    <input type="text" name="grading_details[genre]" value="{{ $details['genre'] ?? '' }}" class="edu-input">
                </div>
                @foreach (['effort' => 'Effort fourni', 'comprehension' => 'Compréhension', 'expression' => 'Expression', 'orthographe' => 'Orthographe', 'progression' => 'Progression'] as $key => $label)
                    <div>
                        <label class="edu-label">{{ $label }}</label>
                        <select name="grading_details[{{ $key }}]" class="edu-input">
                            <option value="">— Choisir —</option>
                            @foreach ($criteria as $value => $cLabel)
                                <option value="{{ $value }}" @selected(($details[$key] ?? '') === $value)>{{ $cLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
            </div>
            <div class="flex shrink-0 flex-wrap gap-3 border-t border-white/60 p-5">
                <button type="submit" name="action" value="return" class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white">Renvoyer à corriger</button>
                <button type="submit" name="action" value="validate" class="edu-btn-primary flex-1">Valider la correction</button>
            </div>
        </div>
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
