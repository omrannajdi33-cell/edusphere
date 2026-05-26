@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <a href="{{ route('student.points') }}" class="edu-glass edu-card-hover block overflow-hidden p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-violet-600">Mes points</p>
                <p class="mt-1 text-4xl font-bold text-slate-900">{{ auth()->user()->points_total }} <span class="text-2xl">⭐</span></p>
            </div>
            <span class="edu-chip bg-indigo-100 text-indigo-700">Classement →</span>
        </div>
    </a>

    <section class="grid gap-4 lg:grid-cols-2">
        <div class="edu-glass p-5">
            <h2 class="text-lg font-bold text-slate-900">Annonces</h2>
            <div class="mt-4 space-y-3">
                @forelse ($announcements as $announcement)
                    <article class="edu-inner p-4">
                        <h3 class="font-semibold text-indigo-700">{{ $announcement->title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ Str::limit($announcement->body, 120) }}</p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Pas d'annonce pour le moment.</p>
                @endforelse
            </div>
        </div>

        <div class="edu-glass p-5">
            <h2 class="text-lg font-bold text-slate-900">À venir</h2>
            <div class="mt-4 space-y-2">
                @forelse ($upcomingEvents as $event)
                    <div class="edu-inner px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $event->title }}</p>
                        <p class="text-sm text-slate-500">{{ $event->starts_at->translatedFormat('d M, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Rien de prévu cette semaine.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Mes matières</h2>
            <a href="{{ route('student.matieres.index') }}" class="edu-link">Tout voir →</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($subjects as $subject)
                <a href="{{ route('student.matieres.show', $subject) }}" class="edu-card-hover overflow-hidden rounded-3xl p-5 shadow-lg transition" style="background: linear-gradient(145deg, {{ $subject->color }} 0%, color-mix(in srgb, {{ $subject->color }} 70%, #6366f1) 100%);">
                    <div class="text-4xl drop-shadow-sm">{{ $subject->icon }}</div>
                    <h3 class="mt-3 text-lg font-bold text-white">{{ $subject->name }}</h3>
                    <span class="mt-4 inline-flex touch-target items-center rounded-xl bg-white/25 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">Ouvrir →</span>
                </a>
            @endforeach
        </div>
    </section>

    @if ($recentPoints->isNotEmpty())
        <section class="edu-glass p-5">
            <h2 class="text-lg font-bold text-slate-900">Derniers points</h2>
            <div class="mt-4 space-y-2">
                @foreach ($recentPoints as $transaction)
                    <div class="edu-inner flex items-center justify-between px-4 py-3">
                        <span class="text-sm text-slate-700">{{ $transaction->reason }}</span>
                        <span class="font-bold {{ $transaction->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $transaction->points >= 0 ? '+' : '' }}{{ $transaction->points }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
