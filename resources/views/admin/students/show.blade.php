@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800">{{ $student->name }}</h2>
            <p class="text-slate-600">Identifiant : {{ $student->username }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.eleves.edit', $student) }}" class="rounded-xl bg-indigo-50 px-4 py-2 font-semibold text-indigo-700">Modifier</a>
            <form method="POST" action="{{ route('admin.students.reset-password', $student) }}">
                @csrf
                <button type="submit" class="rounded-xl bg-amber-50 px-4 py-2 font-semibold text-amber-700">Réinitialiser mot de passe</button>
            </form>
            <form method="POST" action="{{ route('admin.eleves.destroy', $student) }}" onsubmit="return confirm('Supprimer cet élève ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 font-semibold text-red-700">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Points totaux</p>
            <p class="mt-2 text-3xl font-extrabold text-amber-600">{{ $student->points_total }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Statut</p>
            <p class="mt-2 text-lg font-bold {{ $student->is_active ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $student->is_active ? 'Actif' : 'Inactif' }}
            </p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Niveau</p>
            <p class="mt-2 text-lg font-bold text-indigo-700">{{ $student->level?->label() ?? '—' }}</p>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Naissance</p>
            <p class="mt-2 text-lg font-bold text-slate-800">{{ $student->birth_date?->translatedFormat('d F Y') ?? '—' }}</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-lg font-bold text-slate-800">Historique des points</h3>
        @forelse ($student->pointTransactions as $transaction)
            <div class="mb-2 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                <div>
                    <p class="font-semibold text-slate-800">{{ $transaction->reason }}</p>
                    <p class="text-xs text-slate-500">{{ $transaction->created_at->translatedFormat('d/m/Y H:i') }}</p>
                </div>
                <span class="font-bold {{ $transaction->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $transaction->points >= 0 ? '+' : '' }}{{ $transaction->points }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-500">Aucun point enregistré.</p>
        @endforelse
    </div>
</div>
@endsection
