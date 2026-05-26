@if ($activity->isExam() && $submission->status === \App\SubmissionStatus::Draft)
    @php
        $duration = $activity->exam_duration_minutes ?? 45;
        $startedAt = ($submission->exam_started_at ?? now())->toIso8601String();
    @endphp
    <div
        class="edu-purpose-bar edu-purpose-exam shrink-0 border-b border-rose-200/80 bg-gradient-to-r from-rose-600 to-rose-500 px-4 py-3 text-white sm:px-6"
        x-data="examTimer(@js([
            'durationMinutes' => $duration,
            'startedAt' => $startedAt,
            'submitFormId' => 'module-submit-form',
            'fallbackFormId' => 'activity-submit-form',
        ]))"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="text-2xl">📋</span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-rose-100">Examen officiel</p>
                    <p class="text-sm font-medium text-white/90">Compte pour le bulletin · une seule tentative</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-black/20 px-4 py-2 text-center backdrop-blur-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-rose-100">Temps restant</p>
                    <p class="font-mono text-2xl font-bold tabular-nums" x-text="formatted" :class="urgent && 'text-amber-200'"></p>
                </div>
                <p class="hidden text-sm sm:block" x-show="expired" x-cloak>Temps écoulé — envoie ta copie !</p>
            </div>
        </div>
    </div>
@elseif ($activity->isExercise())
    <div class="edu-purpose-bar edu-purpose-exercise shrink-0 border-b border-sky-200/80 bg-gradient-to-r from-sky-500 to-indigo-500 px-4 py-3 text-white sm:px-6">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📝</span>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-sky-100">Exercice d'entraînement</p>
                <p class="text-sm font-medium text-white/90">Pas de pression — entraîne-toi à ton rythme</p>
            </div>
        </div>
    </div>
@endif
