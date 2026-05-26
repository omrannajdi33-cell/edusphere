@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-xl space-y-6">
    <div>
        <h2 class="edu-title">Ajouter un élève</h2>
        <p class="edu-subtitle">Crée un compte avec identifiant et mot de passe.</p>
    </div>

    <form method="POST" action="{{ route('admin.eleves.store') }}" class="space-y-4 edu-glass p-6 space-y-4">
        @csrf
        <div>
            <label class="edu-label" for="name">Nom complet</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="edu-input">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="edu-label" for="username">Identifiant</label>
            <input id="username" name="username" value="{{ old('username') }}" required class="edu-input">
            @error('username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="edu-label" for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required class="edu-input">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="edu-label" for="birth_date">Date de naissance</label>
            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" class="edu-input">
        </div>
        <div>
            <label class="edu-label" for="level">Niveau scolaire</label>
            <select id="level" name="level" required class="edu-input">
                @foreach ($levels as $lvl)
                    <option value="{{ $lvl->value }}" @selected((int) old('level', 1) === $lvl->value)>{{ $lvl->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="edu-btn-primary px-5 py-3">Enregistrer</button>
            <a href="{{ route('admin.eleves.index') }}" class="edu-btn-secondary px-5 py-3">Annuler</a>
        </div>
    </form>
</div>
@endsection
