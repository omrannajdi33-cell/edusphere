@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <section>
        <h2 class="edu-title">Tableau de bord</h2>
        <p class="edu-subtitle mt-1">Vue d'ensemble de ton école d'été.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="edu-glass p-6">
            <p class="text-sm font-medium text-slate-500">Élèves</p>
            <p class="edu-stat-indigo mt-2 text-4xl font-bold">{{ $studentsCount }}</p>
        </div>
        <div class="edu-glass p-6">
            <p class="text-sm font-medium text-slate-500">Matières</p>
            <p class="edu-stat-violet mt-2 text-4xl font-bold">{{ $subjectsCount }}</p>
        </div>
        <div class="edu-glass p-6">
            <p class="text-sm font-medium text-slate-500">Annonces</p>
            <p class="edu-stat-cyan mt-2 text-4xl font-bold">{{ $announcementsCount }}</p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="edu-glass p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Matières</h3>
                <a href="{{ route('admin.matieres.index') }}" class="edu-link">Gérer →</a>
            </div>
            <div class="space-y-2">
                @foreach ($subjects as $subject)
                    <div class="edu-inner flex items-center gap-3 px-4 py-3">
                        <span class="text-2xl">{{ $subject->icon }}</span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $subject->name }}</p>
                            <p class="text-xs text-slate-500">{{ $subject->competencies_count }} compétences</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="edu-glass p-6">
            <h3 class="mb-4 font-bold text-slate-900">Prochains événements</h3>
            @forelse ($upcomingEvents as $event)
                <div class="edu-inner mb-2 px-4 py-3">
                    <p class="font-medium text-slate-900">{{ $event->title }}</p>
                    <p class="text-sm text-slate-500">{{ $event->starts_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Aucun événement planifié.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
