@php
    $section = $activity?->sections->first();
@endphp

<div class="space-y-4" x-data="activityForm()">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Titre</label>
            <input name="title" value="{{ old('title', $activity?->title) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Exercice ou examen</label>
            <select name="purpose" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
                @foreach ($purposes as $p)
                    <option value="{{ $p->value }}" @selected(old('purpose', $activity?->purpose?->value ?? 'exercise') === $p->value)>{{ $p->label() }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Examen → bulletin · Exercice → entraînement</p>
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Format technique</label>
        <select name="type" x-model="type" class="w-full max-w-md rounded-xl border border-slate-200 px-4 py-3">
            @foreach ($types as $t)
                <option value="{{ $t->value }}" @selected(old('type', $activity?->type?->value ?? 'dynamic') === $t->value)>{{ $t->label() }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Description (courte)</label>
        <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('description', $activity?->description) }}</textarea>
    </div>

    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5">
        <h4 class="mb-1 font-bold text-amber-900">📖 Texte de lecture (consultable pendant l'activité)</h4>
        <p class="mb-4 text-xs text-amber-800">L'élève pourra rouvrir ce texte à tout moment pendant l'exercice ou l'examen.</p>

        <input type="hidden" name="reading_mode" x-model="readingMode">

        <div class="mb-4 flex flex-wrap gap-2">
            <button type="button" @click="readingMode = 'none'"
                :class="readingMode === 'none' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700'"
                class="rounded-xl px-4 py-2 text-sm font-bold shadow-sm">Aucun texte</button>
            <button type="button" @click="readingMode = 'text'"
                :class="readingMode === 'text' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700'"
                class="rounded-xl px-4 py-2 text-sm font-bold shadow-sm">✍️ Écrire le texte</button>
            <button type="button" @click="readingMode = 'pdf'"
                :class="readingMode === 'pdf' ? 'bg-amber-600 text-white' : 'bg-white text-slate-700'"
                class="rounded-xl px-4 py-2 text-sm font-bold shadow-sm">📄 Téléverser un PDF</button>
        </div>

        <div x-show="readingMode === 'text'" x-cloak>
            <textarea name="reading_text" rows="12" placeholder="Colle ou écris ici le texte que l'élève doit lire..."
                class="w-full rounded-xl border border-amber-200 bg-white px-4 py-3 font-serif text-base leading-relaxed">{{ old('reading_text', $activity?->reading_text) }}</textarea>
        </div>

        <div x-show="readingMode === 'pdf'" x-cloak>
            <input type="file" name="reading_pdf_file" accept="application/pdf"
                class="w-full rounded-xl border border-amber-200 bg-white px-4 py-3">
            @if ($activity?->hasReadingPdf())
                <p class="mt-2 text-sm text-amber-800">
                    PDF actuel :
                    <a class="font-semibold text-indigo-600 underline" href="{{ $activity->readingPdfUrl() }}" target="_blank">Ouvrir</a>
                    (laisse vide pour garder le même fichier)
                </p>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-4">
        <h4 class="font-bold text-slate-800">Pour quels élèves ?</h4>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Niveau cible</label>
            <select name="level" class="w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Tous les niveaux</option>
                @foreach ($levels as $lvl)
                    <option value="{{ $lvl->value }}" @selected((int) old('level', $activity?->level?->value) === $lvl->value)>{{ $lvl->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Ou élèves précis</label>
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($students as $student)
                    <label class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm">
                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                            @checked(in_array($student->id, old('student_ids', $assignedStudentIds))) class="h-4 w-4">
                        <span>{{ $student->name }}</span>
                        <span class="text-xs text-slate-400">({{ $student->level?->shortLabel() }})</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <div x-show="type === 'pdf'" x-cloak>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Feuille de travail PDF (activité à compléter)</label>
        <input type="file" name="pdf_file" accept="application/pdf" class="w-full rounded-xl border border-slate-200 px-4 py-3">
        @if ($activity?->pdf_path)
            <p class="mt-2 text-sm text-slate-500">PDF actuel : <a class="text-indigo-600" href="{{ \Illuminate\Support\Facades\Storage::url($activity->pdf_path) }}" target="_blank">Voir</a></p>
        @endif
    </div>

    <div x-show="type === 'dynamic'" x-cloak class="space-y-4 rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Titre de l'étape (questions)</label>
            <input name="section_title" value="{{ old('section_title', $section?->title ?? 'Questions') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div class="flex items-center justify-between">
            <h4 class="font-bold text-slate-800">Questions</h4>
            <button type="button" @click="addQuestion()" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-bold text-white">+ Question</button>
        </div>
        <template x-for="(question, index) in questions" :key="index">
            <div class="space-y-3 rounded-xl bg-white p-4 shadow-sm">
                <div class="flex justify-between gap-2">
                    <span class="text-sm font-bold text-indigo-600" x-text="'Question ' + (index + 1)"></span>
                    <button type="button" @click="removeQuestion(index)" class="text-sm text-red-600">Supprimer</button>
                </div>
                <input type="hidden" :name="'questions['+index+'][type]'" x-model="question.type">
                <select x-model="question.type" class="w-full rounded-lg border border-slate-200 px-3 py-2">
                    @foreach ($questionTypes ?? [] as $qt)
                        <option value="{{ $qt->value }}">{{ $qt->label() }}</option>
                    @endforeach
                </select>
                <textarea :name="'questions['+index+'][prompt]'" x-model="question.prompt" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="Énoncé"></textarea>
                <input type="number" :name="'questions['+index+'][points]'" x-model="question.points" min="1" class="w-24 rounded-lg border border-slate-200 px-3 py-2">
                <div x-show="['multiple_choice','multiple_select','checkbox'].includes(question.type)">
                    <template x-for="(opt, oi) in question.options" :key="oi">
                        <div class="mt-2 flex gap-2">
                            <input type="hidden" :name="'questions['+index+'][options]['+oi+'][id]'" :value="'opt_'+oi">
                            <input type="text" :name="'questions['+index+'][options]['+oi+'][text]'" x-model="opt.text" class="flex-1 rounded-lg border px-3 py-2">
                            <label class="flex items-center gap-1 text-sm"><input type="checkbox" :name="'questions['+index+'][options]['+oi+'][correct]'" value="1"> OK</label>
                        </div>
                    </template>
                    <button type="button" @click="addOption(index)" class="mt-2 text-sm font-semibold text-indigo-600">+ Option</button>
                </div>
                <div x-show="question.type === 'true_false'">
                    <select :name="'questions['+index+'][correct]'" class="w-full rounded-lg border px-3 py-2">
                        <option value="true">Vrai</option>
                        <option value="false">Faux</option>
                    </select>
                </div>
                <div x-show="question.type === 'short_answer'">
                    <input type="text" :name="'questions['+index+'][acceptable]'" x-model="question.acceptable" class="w-full rounded-lg border px-3 py-2" placeholder="Réponses acceptées, virgules">
                </div>
                <div x-show="question.type === 'numeric'">
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" step="any" :name="'questions['+index+'][numeric_answer]'" x-model="question.numeric_answer" class="rounded-lg border px-3 py-2">
                        <input type="number" step="any" :name="'questions['+index+'][tolerance]'" x-model="question.tolerance" class="rounded-lg border px-3 py-2">
                    </div>
                </div>
            </div>
        </template>
    </div>

    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $activity?->is_published ?? false)) class="h-5 w-5">
        <span class="text-sm font-semibold text-slate-700">Publier</span>
    </label>
</div>

@push('scripts')
<script>
function activityForm() {
    const existing = {!! json_encode($questionsJson) !!};
    return {
        type: {!! json_encode($defaultType) !!},
        readingMode: {!! json_encode($readingMode) !!},
        questions: existing.length ? existing : [],
        addQuestion() {
            this.questions.push({ type: 'multiple_choice', prompt: '', points: 1, options: [{text:'',correct:false},{text:'',correct:false}], acceptable: '', numeric_answer: 0, tolerance: 0, items: '', correct_order: '0,1', blank_text: '', blanks: '' });
        },
        removeQuestion(i) { this.questions.splice(i, 1); },
        addOption(qi) { this.questions[qi].options.push({text:'',correct:false}); },
    };
}
</script>
@endpush
