@php
    $moduleData = $submission->answers['_module'] ?? [];
    $checklistItems = $items ?? [
        ['id' => 1, 'label' => 'Objectif compris', 'done' => false],
        ['id' => 2, 'label' => 'Exercice réalisé', 'done' => false],
        ['id' => 3, 'label' => 'Auto-évaluation', 'done' => false],
    ];
@endphp

<div
    class="flex min-h-0 flex-1 flex-col"
    x-data="checklistProgress(@js([
        'items' => $checklistItems,
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
>
    <main class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6">
        <div class="mx-auto max-w-3xl space-y-5">
            <div class="edu-glass flex items-center gap-5 p-5">
                <div class="edu-module-progress-ring" :style="`--progress: ${progressPercent}%`">
                    <span x-text="`${progressPercent}%`"></span>
                </div>
                <div>
                    <p class="edu-kicker">{{ $module->label() }}</p>
                    <p class="text-lg font-bold text-slate-900">Progression de la compétence</p>
                    <p class="text-sm text-slate-500" x-text="`${completedCount} / ${items.length} validé(s)`"></p>
                </div>
            </div>

            @if ($activity->description)
                <p class="edu-glass px-4 py-3 text-sm text-slate-700">{{ $activity->description }}</p>
            @endif

            <div class="space-y-3">
                <template x-for="item in items" :key="item.id">
                    <label class="edu-glass flex cursor-pointer items-center gap-4 px-4 py-4">
                        <input type="checkbox" class="h-6 w-6" :checked="item.done" @change="toggle(item.id)">
                        <span class="text-base font-medium" :class="item.done ? 'text-emerald-700 line-through' : 'text-slate-800'" x-text="item.label"></span>
                    </label>
                </template>
            </div>

            <div class="edu-glass p-4">
                <label class="edu-label">Commentaire / ressenti</label>
                <textarea x-model="reflection" @input="saveReflection()" rows="3" class="edu-textarea" placeholder="Comment s'est passée la séance ?"></textarea>
            </div>
        </div>

        <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-5 max-w-3xl">
            @csrf
            <input type="hidden" name="module_data[reflection]" :value="reflection">
            <template x-for="(item, index) in items" :key="`item-${item.id}`">
                <input type="hidden" :name="`module_data[items][${index}][id]`" :value="item.id">
                <input type="hidden" :name="`module_data[items][${index}][done]`" :value="item.done ? 1 : 0">
            </template>
        </form>
    </main>
</div>
