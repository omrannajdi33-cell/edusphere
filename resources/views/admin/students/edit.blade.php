@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-xl space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800">Modifier {{ $student->name }}</h2>
    </div>

    <form method="POST" action="{{ route('admin.eleves.update', $student) }}" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="name">Nom complet</label>
            <input id="name" name="name" value="{{ old('name', $student->name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="username">Identifiant</label>
            <input id="username" name="username" value="{{ old('username', $student->username) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="birth_date">Date de naissance</label>
            <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700" for="level">Niveau scolaire</label>
            <select id="level" name="level" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
                @foreach ($levels as $lvl)
                    <option value="{{ $lvl->value }}" @selected((int) old('level', $student->level?->value ?? 1) === $lvl->value)>{{ $lvl->label() }}</option>
                @endforeach
            </select>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active)) class="h-5 w-5">
            <span class="text-sm font-semibold text-slate-700">Compte actif</span>
        </label>
        <div class="flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Mettre à jour</button>
            <a href="{{ route('admin.eleves.show', $student) }}" class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700">Annuler</a>
        </div>
    </form>
</div>
@endsection
