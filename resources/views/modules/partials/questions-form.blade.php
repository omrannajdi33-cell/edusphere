<form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto flex w-full max-w-3xl flex-col gap-5 pb-4">
    @csrf
    @foreach ($activity->sections as $section)
        <div class="space-y-4">
            @if ($section->title !== 'Questions')
                <h3 class="text-center text-base font-bold text-slate-800">{{ $section->title }}</h3>
            @endif
            @foreach ($section->questions as $question)
                @if ($question->type->isAnswerable())
                    @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                @endif
            @endforeach
        </div>
    @endforeach
</form>
