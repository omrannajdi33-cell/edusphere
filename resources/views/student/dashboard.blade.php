@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <a href="{{ route('student.points') }}" class="edu-card block overflow-hidden p-6 transition active:scale-[0.99]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-teal-400">Mes points</p>
                <p class="mt-1 text-4xl font-extrabold text-white">{{ auth()->user()->points_total }} <span class="text-2xl">⭐</span></p>
            </div>
            <span class="rounded-2xl bg-teal-500/20 px-4 py-2 text-sm font-bold text-teal-300">Classement →</span>
        </div>
    </a>

    <section class="grid gap-4 lg:grid-cols-2">
        <div class="edu-card p-5">
            <h2 class="text-lg font-extrabold text-white">Annonces</h2>
            <div class="mt-4 space-y-3">
                @forelse ($announcements as $announcement)
                    <article class="rounded-2xl bg-white/5 p-4">
                        <h3 class="font-bold text-teal-300">{{ $announcement->title }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ Str::limit($announcement->body, 120) }}</p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Pas d'annonce pour le moment.</p>
                @endforelse
            </div>
        </div>

        <div class="edu-card p-5">
            <h2 class="text-lg font-extrabold text-white">À venir</h2>
            <div class="mt-4 space-y-3">
                @forelse ($upcomingEvents as $event)
                    <div class="rounded-2xl border border-white/5 px-4 py-3">
                        <p class="font-semibold text-white">{{ $event->title }}</p>
                        <p class="text-sm text-slate-400">{{ $event->starts_at->translatedFormat('d M, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Rien de prévu cette semaine.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-extrabold text-white">Mes matières</h2>
            <a href="{{ route('student.matieres.index') }}" class="text-sm font-bold text-teal-400">Tout voir →</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($subjects as $subject)
                <article class="group relative overflow-hidden rounded-3xl p-5 shadow-xl transition active:scale-[0.98]" style="background: linear-gradient(145deg, {{ $subject->color }}ee 0%, {{ $subject->color }}88 100%);">
                    <div class="text-4xl drop-shadow">{{ $subject->icon }}</div>
                    <h3 class="mt-3 text-lg font-extrabold text-white">{{ $subject->name }}</h3>
                    <a href="{{ route('student.matieres.show', $subject) }}" class="mt-4 inline-flex touch-target items-center rounded-xl bg-black/20 px-4 py-2 text-sm font-bold text-white backdrop-blur">Ouvrir →</a>
                </article>
            @endforeach
        </div>
    </section>

    @if ($recentPoints->isNotEmpty())
        <section class="edu-card p-5">
            <h2 class="text-lg font-extrabold text-white">Derniers points</h2>
            <div class="mt-4 space-y-2">
                @foreach ($recentPoints as $transaction)
                    <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3">
                        <span class="text-sm text-slate-300">{{ $transaction->reason }}</span>
                        <span class="font-bold {{ $transaction->points >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $transaction->points >= 0 ? '+' : '' }}{{ $transaction->points }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
