@extends('layouts.app')

@section('title', 'Connexion — EduSphere')

@section('body')
<div class="flex min-h-dvh h-dvh-safe items-center justify-center px-4 py-8" style="padding: env(safe-area-inset-top) 1rem env(safe-area-inset-bottom);">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <p class="edu-kicker mb-2">École d'été</p>
            <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">EduSphere</h1>
            <p class="mt-2 text-slate-600">Connecte-toi pour continuer</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="edu-glass space-y-5 p-8">
            @csrf

            @if ($errors->any())
                <div class="edu-alert-error">{{ $errors->first() }}</div>
            @endif

            <div>
                <label class="edu-label" for="login">Identifiant</label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" class="edu-input">
            </div>

            <div>
                <label class="edu-label" for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="edu-input">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/30">
                Se souvenir de moi
            </label>

            <button type="submit" class="edu-btn-primary touch-target w-full py-3.5 text-base">
                Entrer
            </button>
        </form>
    </div>
</div>
@endsection
