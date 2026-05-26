@extends('layouts.student-activity')

@section('title', $activity->title . ' — Carte')

@section('content')
@include('modules.partials.context')
@php $moduleData = $submission->answers['_module'] ?? []; @endphp

<div
    class="flex h-dvh w-full flex-col"
    x-data="carteInteractive(@js([
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
            <input type="text" x-model="placingLabel" placeholder="Élément à placer" class="edu-input max-w-xs py-2 text-sm">
            <button type="button" class="edu-module-tool" :class="linkMode && 'is-active'" @click="toggleLinkMode()">🔗 Relier des lieux</button>
        </div>

        <main class="min-h-0 flex-1 overflow-y-auto p-4">
            <p class="edu-glass mx-auto mb-4 max-w-4xl px-4 py-3 text-sm text-slate-700">{{ $activity->description ?: 'Clique une région pour la sélectionner ou y placer un élément.' }}</p>

            <div class="edu-glass relative mx-auto aspect-[4/3] w-full max-w-4xl overflow-hidden bg-gradient-to-br from-emerald-100 via-sky-100 to-indigo-100">
                <svg viewBox="0 0 100 100" class="h-full w-full">
                    <template x-for="link in links" :key="`${link.from}-${link.to}`">
                        <line
                            :x1="regions.find(r => r.id === link.from)?.x"
                            :y1="regions.find(r => r.id === link.from)?.y"
                            :x2="regions.find(r => r.id === link.to)?.x"
                            :y2="regions.find(r => r.id === link.to)?.y"
                            stroke="#6366f1"
                            stroke-width="1.5"
                            stroke-dasharray="3 2"
                        />
                    </template>
                    <template x-for="region in regions" :key="region.id">
                        <g @click="selectRegion(region); linkRegion(region)">
                            <circle :cx="region.x" :cy="region.y" r="11" :fill="selectedRegion === region.id ? '#6366f1' : 'rgba(255,255,255,0.85)'" stroke="#4338ca" stroke-width="1.5" />
                            <text :x="region.x" :y="region.y + 18" text-anchor="middle" font-size="4" fill="#334155" x-text="region.label"></text>
                        </g>
                    </template>
                    <template x-for="marker in markers" :key="marker.id">
                        <text :x="marker.x" :y="marker.y - 4" text-anchor="middle" font-size="4.5" font-weight="700" fill="#be123c" x-text="marker.label"></text>
                    </template>
                </svg>
            </div>

            <form id="module-submit-form" method="POST" action="{{ route('student.activites.submit', $activity) }}" class="mx-auto mt-4 max-w-4xl space-y-4">
                @csrf
                <input type="hidden" name="module_data[markers]" :value="JSON.stringify(markers)">
                <input type="hidden" name="module_data[links]" :value="JSON.stringify(links)">
                @foreach ($activity->sections as $section)
                    @foreach ($section->questions as $question)
                        @if ($question->type->isAnswerable())
                            @include('student.activities._question', ['question' => $question, 'submission' => $submission])
                        @endif
                    @endforeach
                @endforeach
            </form>
        </main>

        @include('modules.partials.submit-footer')
    @endunless
</div>
@endsection
