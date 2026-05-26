@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="edu-title">Élèves</h2>
            <p class="edu-subtitle">Gère les comptes élèves de l'école d'été.</p>
        </div>
        <a href="{{ route('admin.eleves.create') }}" class="edu-btn-primary touch-target px-5 py-3">+ Ajouter un élève</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($students as $student)
            <article class="edu-glass p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ $student->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $student->username }} · {{ $student->level?->shortLabel() ?? 'Niveau ?' }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $student->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $student->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <p class="mt-4 text-sm text-slate-600">⭐ {{ $student->points_total }} points</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.eleves.show', $student) }}" class="edu-btn-secondary px-3 py-2 text-sm">Voir</a>
                    <a href="{{ route('admin.eleves.edit', $student) }}" class="edu-btn-secondary px-3 py-2 text-sm text-indigo-700">Modifier</a>
                </div>
            </article>
        @endforeach
    </div>

    <div>{{ $students->links() }}</div>
</div>
@endsection
