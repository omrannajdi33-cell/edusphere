@if ($submission->isLocked())
    <div class="edu-glass flex min-h-[50dvh] flex-col items-center justify-center p-8 text-center">
        <p class="text-lg font-semibold text-amber-700">
            @if ($submission->status === \App\SubmissionStatus::Submitted)
                Épreuve envoyée — correction en cours.
            @else
                Épreuve terminée.
            @endif
        </p>
        <a href="{{ route('student.activites.resultat', $activity) }}" class="edu-btn-primary mt-4">Voir le résultat</a>
    </div>
@endif
