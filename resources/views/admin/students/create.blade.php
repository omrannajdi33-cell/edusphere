@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-xl space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800">Ajouter un élève</h2>
        <p class="text-slate-600">Crée un compte avec identifiant et mot de passe.</p>
    </div>

    <form method="POST" action="{{ route('admin.eleves.store') }}" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="name">Nom complet</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="username">Identifiant</label>
            <input id="username" name="username" value="{{ old('username') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
            @error('username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="birth_date">Date de naissance</label>
            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="level">Niveau scolaire</label>
            <select id="level" name="level" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
                @foreach ($levels as $lvl)
                    <option value="{{ $lvl->value }}" @selected((int) old('level', 1) === $lvl->value)>{{ $lvl->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Enregistrer</button>
            <a href="{{ route('admin.eleves.index') }}" class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700">Annuler</a>
        </div>
    </form>
</div>
@endsection
