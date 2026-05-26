@extends('layouts.app')

@section('title', 'Admin — EduSphere')

@section('body')
<div class="flex min-h-dvh" x-data="{ sidebarOpen: false }">
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-40 w-64 border-r border-white/70 bg-white/60 backdrop-blur-2xl transition-transform lg:static lg:translate-x-0"
        style="padding-top: env(safe-area-inset-top);"
    >
        <div class="flex h-full flex-col p-4">
            <a href="{{ route('admin.dashboard') }}" class="mb-6 block px-2">
                <p class="edu-kicker">EduSphere</p>
                <p class="text-lg font-bold text-slate-900">Espace prof</p>
            </a>
            <nav class="space-y-0.5">
                @foreach ([
                    ['admin.dashboard', 'Tableau de bord'],
                    ['admin.eleves.index', 'Élèves'],
                    ['admin.matieres.index', 'Matières'],
                    ['admin.points.index', 'Points'],
                    ['admin.corrections.index', 'Corrections'],
                    ['admin.bulletins.index', 'Bulletins'],
                    ['admin.annonces.index', 'Annonces'],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="edu-nav-link {{ request()->routeIs(str_replace('.index', '.*', $route)) || request()->routeIs($route) ? 'edu-nav-link-active' : '' }}">{{ $label }}</a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-4">
                @csrf
                <button type="submit" class="edu-btn-secondary w-full">Déconnexion</button>
            </form>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-white/60 bg-white/55 px-4 py-3 backdrop-blur-xl lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" type="button" class="edu-btn-secondary touch-target px-3">☰</button>
            <span class="font-semibold text-slate-900">Administration</span>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8" style="padding-bottom: calc(2rem + env(safe-area-inset-bottom));">
            @if (session('success'))
                <div class="edu-alert-success mb-6">{{ session('success') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-30 bg-indigo-900/20 backdrop-blur-sm lg:hidden"></div>
</div>
@endsection
