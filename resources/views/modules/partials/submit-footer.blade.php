<footer class="shrink-0 border-t border-white/70 bg-white/60 p-4 backdrop-blur-xl sm:p-5">
    <button type="submit" form="module-submit-form" class="edu-btn-primary touch-target w-full py-3.5 text-base">
        {{ $activity->isExam() ? 'Envoyer mon examen ✓' : 'Terminer l\'exercice ✓' }}
    </button>
</footer>
