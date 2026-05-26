@extends('layouts.app')

@section('title', 'Mon espace — EduSphere')

@section('body')
<div class="flex min-h-dvh h-dvh-safe w-full flex-col">
    <header class="sticky top-0 z-30 shrink-0 border-b border-white/60 bg-white/55 backdrop-blur-xl" style="padding-top: env(safe-area-inset-top);">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="min-w-0">
                <p class="edu-kicker">{{ auth()->user()->name }} · {{ auth()->user()->level?->shortLabel() }}</p>
                <h1 class="truncate text-xl font-bold text-slate-900 sm:text-2xl">Mon espace</h1>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ route('student.points') }}" class="edu-chip edu-chip-points touch-target px-4 py-2">
                    <span>⭐</span>
                    <span class="text-base font-bold">{{ auth()->user()->points_total }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="edu-btn-secondary touch-target px-3 py-2 text-sm">Sortir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 overflow-y-auto px-4 py-6 sm:px-6 sm:py-8" style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom));">
        @yield('content')
    </main>
</div>
@endsection
