@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <section>
        <h2 class="text-3xl font-extrabold text-white">Tableau de bord</h2>
        <p class="mt-1 text-slate-400">Vue d'ensemble de ton école d'été.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        <div class="edu-card p-6">
            <p class="text-sm font-semibold text-slate-400">Élèves</p>
            <p class="mt-2 text-4xl font-extrabold text-teal-400">{{ $studentsCount }}</p>
        </div>
        <div class="edu-card p-6">
            <p class="text-sm font-semibold text-slate-400">Matières</p>
            <p class="mt-2 text-4xl font-extrabold text-violet-400">{{ $subjectsCount }}</p>
        </div>
        <div class="edu-card p-6">
            <p class="text-sm font-semibold text-slate-400">Annonces</p>
            <p class="mt-2 text-4xl font-extrabold text-sky-400">{{ $announcementsCount }}</p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="edu-card p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Matières</h3>
                <a href="{{ route('admin.matieres.index') }}" class="text-sm font-semibold text-teal-400">Gérer →</a>
            </div>
            <div class="space-y-2">
                @foreach ($subjects as $subject)
                    <div class="flex items-center gap-3 rounded-2xl bg-white/5 px-4 py-3">
                        <span class="text-2xl">{{ $subject->icon }}</span>
                        <div>
                            <p class="font-bold text-white">{{ $subject->name }}</p>
                            <p class="text-xs text-slate-400">{{ $subject->competencies_count }} compétences</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="edu-card p-6">
            <h3 class="mb-4 text-lg font-bold text-white">Prochains événements</h3>
            @forelse ($upcomingEvents as $event)
                <div class="mb-3 rounded-2xl bg-white/5 px-4 py-3">
                    <p class="font-semibold text-white">{{ $event->title }}</p>
                    <p class="text-sm text-slate-400">{{ $event->starts_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Aucun événement planifié.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
