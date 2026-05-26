@php
    use App\QuestionType;
    $name = 'answers['.$question->id.']';
    $saved = old('answers.'.$question->id, $submission->answers[(string) $question->id] ?? null);
@endphp

<article class="edu-glass w-full p-5">
    <h4 class="text-base font-bold leading-snug text-slate-900 sm:text-lg">{{ $question->prompt }}</h4>
    <p class="mb-4 text-xs font-medium text-slate-500">{{ $question->points }} point(s)</p>

    <div class="w-full space-y-2">
        @switch($question->type)
            @case(QuestionType::MultipleChoice)
                @foreach ($question->config['options'] ?? [] as $option)
                    <label class="edu-inner flex w-full cursor-pointer items-center gap-3 px-4 py-3 transition hover:bg-white/70 has-checked:bg-indigo-50/90">
                        <input type="radio" name="{{ $name }}" value="{{ $option['id'] }}" @checked($saved === ($option['id'] ?? null)) class="h-5 w-5 shrink-0 text-indigo-600">
                        <span class="text-base sm:text-lg">{{ $option['text'] }}</span>
                    </label>
                @endforeach
                @break

            @case(QuestionType::TrueFalse)
                <label class="edu-inner flex w-full cursor-pointer items-center gap-3 px-4 py-3 has-checked:bg-indigo-50/90">
                    <input type="radio" name="{{ $name }}" value="1" @checked($saved === true || $saved === '1') class="h-5 w-5 shrink-0 text-indigo-600">
                    <span class="text-base sm:text-lg">Vrai</span>
                </label>
                <label class="edu-inner flex w-full cursor-pointer items-center gap-3 px-4 py-3 has-checked:bg-indigo-50/90">
                    <input type="radio" name="{{ $name }}" value="0" @checked($saved === false || $saved === '0') class="h-5 w-5 shrink-0 text-indigo-600">
                    <span class="text-base sm:text-lg">Faux</span>
                </label>
                @break

            @case(QuestionType::ShortAnswer)
                <input type="text" name="{{ $name }}" value="{{ $saved }}" class="edu-input text-base sm:text-lg">
                @break

            @case(QuestionType::LongAnswer)
                <textarea name="{{ $name }}" rows="5" class="edu-textarea text-base sm:text-lg">{{ $saved }}</textarea>
                @break

            @case(QuestionType::Numeric)
                <input type="number" step="any" name="{{ $name }}" value="{{ $saved }}" class="edu-input text-base sm:text-lg">
                @break

            @case(QuestionType::MultipleSelect)
            @case(QuestionType::Checkbox)
                @foreach ($question->config['options'] ?? [] as $option)
                    <label class="edu-inner flex w-full cursor-pointer items-center gap-3 px-4 py-3">
                        <input type="checkbox" name="{{ $name }}[]" value="{{ $option['id'] }}" @checked(is_array($saved) && in_array($option['id'], $saved)) class="h-5 w-5 shrink-0 text-indigo-600">
                        <span class="text-base">{{ $option['text'] }}</span>
                    </label>
                @endforeach
                @break

            @case(QuestionType::Ordering)
                @foreach ($question->config['items'] ?? [] as $i => $item)
                    <div class="edu-inner flex w-full items-center gap-3 px-4 py-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">{{ $i + 1 }}</span>
                        <input type="hidden" name="{{ $name }}[]" value="{{ $i }}">
                        <span class="flex-1 text-base">{{ $item }}</span>
                    </div>
                @endforeach
                @break

            @case(QuestionType::FillBlank)
                @if (! empty($question->config['text']))
                    <p class="mb-3 text-base text-slate-700">{{ $question->config['text'] }}</p>
                @endif
                @foreach ($question->config['blanks'] ?? [] as $i => $blank)
                    <input type="text" name="{{ $name }}[{{ $i }}]" value="{{ is_array($saved) ? ($saved[$i] ?? '') : '' }}" placeholder="Réponse {{ $i + 1 }}" class="edu-input text-base">
                @endforeach
                @break

            @default
                <p class="text-sm font-medium text-amber-700">Ce type de question arrive bientôt sur tablette.</p>
        @endswitch
    </div>
</article>
