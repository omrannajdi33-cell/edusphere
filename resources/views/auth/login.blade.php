@extends('layouts.app')

@section('title', 'Connexion — EduSphere')

@section('body')
<div class="flex min-h-dvh h-dvh-safe items-center justify-center px-4 py-8" style="padding: env(safe-area-inset-top) 1rem env(safe-area-inset-bottom);">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-teal-400">École d'été</p>
            <h1 class="mt-2 text-4xl font-extrabold text-white">EduSphere</h1>
            <p class="mt-2 text-slate-400">Connecte-toi pour continuer</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="edu-card space-y-5 p-8">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Identifiant</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder:text-slate-500 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-300">Mot de passe</label>
                <input type="password" name="password" required autocomplete="current-password" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-400">
                <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-teal-500">
                Se souvenir de moi
            </label>

            <button type="submit" class="touch-target w-full rounded-2xl bg-gradient-to-r from-teal-500 to-emerald-500 py-4 text-lg font-extrabold text-white shadow-lg shadow-teal-500/25">
                Entrer
            </button>
        </form>
    </div>
</div>
@endsection
