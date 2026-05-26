@extends('layouts.app')

@section('title', 'Admin — EduSphere')

@section('body')
<div class="flex min-h-dvh" x-data="{ sidebarOpen: false }">
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-40 w-72 border-r border-white/10 bg-slate-950/95 backdrop-blur-xl transition-transform lg:static lg:translate-x-0"
        style="padding-top: env(safe-area-inset-top);"
    >
        <div class="flex h-full flex-col p-4">
            <a href="{{ route('admin.dashboard') }}" class="mb-8 block">
                <p class="text-xs font-bold uppercase tracking-widest text-teal-400">EduSphere</p>
                <p class="text-xl font-extrabold text-white">Espace prof</p>
            </a>
            <nav class="space-y-1">
                @foreach ([
                    ['admin.dashboard', 'Tableau de bord'],
                    ['admin.eleves.index', 'Élèves'],
                    ['admin.matieres.index', 'Matières'],
                    ['admin.points.index', 'Points'],
                    ['admin.corrections.index', 'Corrections'],
                    ['admin.bulletins.index', 'Bulletins'],
                    ['admin.annonces.index', 'Annonces'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white {{ request()->routeIs(str_replace('.index', '.*', $route)) || request()->routeIs($route) ? 'bg-teal-500/15 text-teal-300' : '' }}">{{ $label }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-4">
                @csrf
                <button type="submit" class="w-full rounded-2xl border border-white/10 py-3 text-sm font-bold text-slate-400">Déconnexion</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-white/10 bg-slate-950/80 px-4 py-3 backdrop-blur-xl lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" type="button" class="touch-target rounded-xl bg-white/10 px-3 text-white">☰</button>
            <span class="font-bold text-white">Admin</span>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8" style="padding-bottom: calc(2rem + env(safe-area-inset-bottom));">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-300">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-30 bg-black/60 lg:hidden"></div>
</div>
@endsection
