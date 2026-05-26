@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center gap-4">
        <span class="text-4xl">{{ $subject->icon }}</span>
        <div>
            <h2 class="text-2xl font-extrabold" style="color: {{ $subject->color }}">{{ $subject->name }}</h2>
            <p class="text-slate-600">Compétences et activités — filtre par niveau</p>
        </div>
        <a href="{{ route('admin.matieres.index') }}" class="ml-auto text-sm font-semibold text-indigo-600">← Retour</a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.matieres.show', $subject) }}"
           class="rounded-xl px-4 py-2 text-sm font-bold {{ $selectedLevel === null ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 shadow-sm' }}">
            Tous les niveaux
        </a>
        @foreach ($levels as $lvl)
            <a href="{{ route('admin.matieres.show', ['subject' => $subject, 'niveau' => $lvl->value]) }}"
               class="rounded-xl px-4 py-2 text-sm font-bold {{ $selectedLevel === $lvl->value ? 'bg-indigo-600 text-white' : 'bg-white text-slate-700 shadow-sm' }}">
                {{ $lvl->shortLabel() }}
            </a>
        @endforeach
    </div>

    @foreach ($subject->competencies as $competency)
        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $competency->name }}</h3>
                    @if ($competency->module_type)
                        <p class="text-xs text-slate-500">Module : {{ $competency->module_type }}</p>
                    @endif
                </div>
                <a href="{{ route('admin.competences.activites.create', $competency) }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white">+ Activité</a>
            </div>

            <div class="space-y-2">
                @forelse ($competency->activities as $activity)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 px-4 py-3">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $activity->title }}</p>
                            <p class="text-xs text-slate-500">
                                <span class="rounded px-1.5 py-0.5 font-bold {{ $activity->purpose->badgeColor() }}">{{ $activity->purpose->label() }}</span>
                                · {{ $activity->type->label() }}
                                · {{ $activity->is_published ? 'Publiée' : 'Brouillon' }}
                                · {{ $activity->assignmentLabel() }}
                                @if ($activity->hasReadingMaterial())
                                    · 📖 Lecture {{ $activity->hasReadingPdf() ? 'PDF' : 'texte' }}
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('admin.competences.activites.edit', [$competency, $activity]) }}" class="text-sm font-semibold text-indigo-600">Modifier</a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aucune activité pour ce filtre.</p>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
@endsection
