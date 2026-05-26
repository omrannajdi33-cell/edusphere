@extends('layouts.student-activity')

@section('title', $activity->title . ' — Géométrie')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="geometrieBoard(@js([
        'progressUrl' => route('student.activites.progress', $activity),
        'csrf' => csrf_token(),
        'saved' => $moduleData,
    ]))"
    style="padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);"
>
    @include('modules.partials.activity-chrome')

    @include('modules.partials.locked')

    @unless ($submission->isLocked())
        <div class="edu-module-toolbar shrink-0">
            <button type="button" class="edu-module-tool" :class="activeTool === 'select' && 'is-active'" @click="activeTool = 'select'">↖ Déplacer</button>
            <button type="button" class="edu-module-tool" :class="activeTool === 'line' && 'is-active'" @click="activeTool = 'line'">／ Tracer</button>
            <button type="button" class="edu-module-tool" @click="addShape('triangle')">△ Triangle</button>
            <button type="button" class="edu-module-tool" @click="addShape('circle')">○ Cercle</button>
            <button type="button" class="edu-module-tool" @click="addShape('rectangle')">▭ Rectangle</button>
        </div>

        <main class="min-h-0 flex-1 overflow-y-auto p-4">
            <p class="edu-glass mb-4 px-4 py-3 text-sm text-slate-700">{{ $activity->description ?: 'Construis, trace et identifie les figures.' }}</p>

            <div class="edu-glass relative mx-auto aspect-[4/3] w-full max-w-5xl overflow-hidden">
                <svg class="h-full w-full bg-white" @click="canvasClick($event)">
                    <template x-for="line in lines" :key="`${line.from.x}-${line.to.x}`">
                        <line :x1="line.from.x" :y1="line.from.y" :x2="line.to.x" :y2="line.to.y" stroke="#6366f1" stroke-width="3" />
                    </template>
                    <template x-for="shape in shapes" :key="shape.id">
                        <g @pointerdown.stop="startDrag($event, shape)" @pointermove.window="drag($event)" @pointerup.window="endDrag()" style="cursor: grab">
                            <template x-if="shape.type === 'triangle'">
                                <polygon :points="`${shape.x},${shape.y + 50} ${shape.x + 45},${shape.y} ${shape.x + 90},${shape.y + 50}`" fill="rgba(99,102,241,0.15)" stroke="#6366f1" stroke-width="2" />
                            </template>
                            <template x-if="shape.type === 'circle'">
                                <circle :cx="shape.x + 35" :cy="shape.y + 35" r="35" fill="rgba(236,72,153,0.12)" stroke="#ec4899" stroke-width="2" />
                            </template>
                            <template x-if="shape.type === 'rectangle'">
                                <rect :x="shape.x" :y="shape.y" width="90" height="55" fill="rgba(6,182,212,0.12)" stroke="#06b6d4" stroke-width="2" />
                            </template>
                            <text :x="shape.x + 5" :y="shape.y - 8" fill="#334155" font-weight="700" x-text="shape.label"></text>
                        </g>
                    </template>
                </svg>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-4 max-w-5xl">
                @csrf
                <input type="hidden" name="module_data[shapes]" :value="JSON.stringify(shapes)">
                <input type="hidden" name="module_data[lines]" :value="JSON.stringify(lines)">
                @if ($activity->sections->flatMap->questions->count())
                    <div class="mt-4 space-y-4">
                        @foreach ($activity->sections as $section)
                            @foreach ($section->questions as $question)
                                @if ($question->type->isAnswerable())
                                    @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                @endif
            </form>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
