@include('student.activities._worksheet_header', ['activity' => $activity, 'backUrl' => route('student.matieres.show', $activity->competency->subject)])

<main class="min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6 sm:py-5">
    @if ($activity->description)
        <p class="mb-4 text-center text-slate-400">{{ $activity->description }}</p>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-center text-rose-300">{{ session('error') }}</div>
    @endif

    @if ($submission->isLocked())
        <div class="flex min-h-[50dvh] flex-col items-center justify-center rounded-2xl bg-white/5 p-8 text-center">
            <p class="text-lg font-bold text-amber-300">
                @if ($submission->status === \App\SubmissionStatus::Submitted)
                    Examen envoyé — correction en cours.
                @else
                    Épreuve terminée.
                @endif
            </p>
            <a href="{{ route('student.activites.resultat', $activity) }}" class="mt-4 rounded-2xl bg-teal-500 px-6 py-3 font-bold text-white">Voir le résultat</a>
        </div>
    @else
        <form id="activity-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto flex w-full max-w-3xl flex-col gap-5 pb-4">
            @csrf
            @foreach ($activity->sections as $section)
                <div class="space-y-4">
                    <h3 class="text-center text-lg font-bold text-white">{{ $section->title }}</h3>
                    @foreach ($section->questions as $question)
                        @if ($question->type->isAnswerable())
                            @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                        @endif
                    @endforeach
                </div>
            @endforeach
        </form>
    @endif
</main>

@unless ($submission->isLocked())
    <footer class="shrink-0 border-t border-white/10 bg-slate-900/95 p-4 backdrop-blur-xl sm:p-5">
        <button type="submit" form="activity-submit-form" class="touch-target w-full rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-500 py-4 text-lg font-extrabold text-white shadow-lg">
            {{ $activity->isExam() ? 'Envoyer mon examen ✓' : 'Terminer l\'exercice ✓' }}
        </button>
    </footer>
@endunless
