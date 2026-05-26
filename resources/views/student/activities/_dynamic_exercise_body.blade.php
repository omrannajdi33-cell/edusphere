@include('student.activities._worksheet_header', ['activity' => $activity, 'backUrl' => route('student.matieres.show', $activity->competency->subject)])

<main class="min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6 sm:py-5">
    @if ($activity->description)
        <p class="mb-4 text-center text-sm text-slate-600">{{ $activity->description }}</p>
    @endif

    @if (session('error'))
        <div class="edu-alert-error mb-4 text-center">{{ session('error') }}</div>
    @endif

    @if ($submission->isLocked())
        <div class="edu-glass flex min-h-[50dvh] flex-col items-center justify-center p-8 text-center">
            <p class="text-lg font-semibold text-amber-700">
                @if ($submission->status === \App\SubmissionStatus::Submitted)
                    Examen envoyé — correction en cours.
                @else
                    Épreuve terminée.
                @endif
            </p>
            <a href="{{ route('student.activites.resultat', $activity) }}" class="edu-btn-primary mt-4">Voir le résultat</a>
        </div>
    @else
        <form id="activity-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto flex w-full max-w-3xl flex-col gap-5 pb-4">
            @csrf
            @foreach ($activity->sections as $section)
                <div class="space-y-4">
                    <h3 class="text-center text-base font-bold text-slate-800">{{ $section->title }}</h3>
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
    <footer class="shrink-0 border-t border-white/70 bg-white/60 p-4 backdrop-blur-xl sm:p-5">
        <button type="submit" form="activity-submit-form" class="edu-btn-primary touch-target w-full py-3.5 text-base">
            {{ $activity->isExam() ? 'Envoyer mon examen ✓' : 'Terminer l\'exercice ✓' }}
        </button>
    </footer>
@endunless
