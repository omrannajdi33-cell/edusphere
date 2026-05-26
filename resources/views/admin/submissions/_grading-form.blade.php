@php
    $details = old('grading_details', $submission->grading_details ?? []);
    $criteria = \App\GradingCriterion::options();
@endphp

<form method="POST" action="{{ route('admin.corrections.update', $submission) }}" class="edu-glass flex h-full flex-col overflow-hidden">
    @csrf
    @method('PUT')

    <div class="shrink-0 border-b border-white/60 p-5">
        <p class="edu-kicker">Fiche de correction</p>
        <h3 class="text-lg font-bold text-slate-900">{{ $submission->student->name }}</h3>
        <p class="text-sm text-slate-500">{{ $submission->activity->title }}</p>
    </div>

    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
        <div>
            <label class="edu-label">Note (sur {{ $submission->max_score ?? '?' }})</label>
            <input type="number" name="score" value="{{ old('score', $submission->score) }}" min="0" @if($submission->max_score) max="{{ $submission->max_score }}" @endif required class="edu-input text-lg font-bold">
        </div>

        <div>
            <label class="edu-label">Commentaire général</label>
            <textarea name="teacher_comment" rows="4" class="edu-textarea" placeholder="Retour pour l'élève…">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
        </div>

        <div>
            <label class="edu-label">Genre / type de production</label>
            <input type="text" name="grading_details[genre]" value="{{ $details['genre'] ?? '' }}" class="edu-input" placeholder="Récit, description, argumentation…">
        </div>

        <div>
            <label class="edu-label">Effort fourni</label>
            <select name="grading_details[effort]" class="edu-input">
                <option value="">— Choisir —</option>
                @foreach ($criteria as $value => $label)
                    <option value="{{ $value }}" @selected(($details['effort'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="edu-label">Compréhension</label>
            <select name="grading_details[comprehension]" class="edu-input">
                <option value="">— Choisir —</option>
                @foreach ($criteria as $value => $label)
                    <option value="{{ $value }}" @selected(($details['comprehension'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="edu-label">Expression / rédaction</label>
            <select name="grading_details[expression]" class="edu-input">
                <option value="">— Choisir —</option>
                @foreach ($criteria as $value => $label)
                    <option value="{{ $value }}" @selected(($details['expression'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="edu-label">Orthographe & grammaire</label>
            <select name="grading_details[orthographe]" class="edu-input">
                <option value="">— Choisir —</option>
                @foreach ($criteria as $value => $label)
                    <option value="{{ $value }}" @selected(($details['orthographe'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="edu-label">Progression / attitude</label>
            <select name="grading_details[progression]" class="edu-input">
                <option value="">— Choisir —</option>
                @foreach ($criteria as $value => $label)
                    <option value="{{ $value }}" @selected(($details['progression'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="shrink-0 border-t border-white/60 p-5">
        <button type="submit" class="edu-btn-primary w-full py-3.5">Valider la correction</button>
    </div>
</form>
