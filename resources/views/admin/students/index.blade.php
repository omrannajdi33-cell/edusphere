@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800">Élèves</h2>
            <p class="text-slate-600">Gère les comptes élèves de l'école d'été.</p>
        </div>
        <a href="{{ route('admin.eleves.create') }}" class="touch-target rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white hover:bg-indigo-700">+ Ajouter un élève</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($students as $student)
            <article class="rounded-2xl bg-white p-5 shadow-sm">
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
                    <a href="{{ route('admin.eleves.show', $student) }}" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Voir</a>
                    <a href="{{ route('admin.eleves.edit', $student) }}" class="rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Modifier</a>
                </div>
            </article>
        @endforeach
    </div>

    <div>{{ $students->links() }}</div>
</div>
@endsection
