@extends('layouts.app')

@section('title', 'Mon espace — EduSphere')

@section('body')
<div class="flex min-h-dvh h-dvh-safe w-full flex-col">
    <header class="sticky top-0 z-30 shrink-0 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl" style="padding-top: env(safe-area-inset-top);">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6 sm:py-4">
            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wider text-teal-400">Salut {{ auth()->user()->name }} · {{ auth()->user()->level?->shortLabel() }}</p>
                <h1 class="truncate text-xl font-extrabold text-white sm:text-2xl">Mon espace</h1>
            </div>
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                <a href="{{ route('student.points') }}" class="touch-target rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 px-4 py-2 text-center shadow-lg shadow-amber-500/20">
                    <p class="text-[10px] font-bold uppercase text-amber-950/70">Points</p>
                    <p class="text-lg font-extrabold text-amber-950">{{ auth()->user()->points_total }}</p>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="touch-target rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-bold text-white">Sortir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 overflow-y-auto px-4 py-5 sm:px-6 sm:py-8" style="padding-bottom: calc(1.25rem + env(safe-area-inset-bottom));">
        @yield('content')
    </main>
</div>
@endsection
