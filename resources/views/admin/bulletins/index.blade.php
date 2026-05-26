@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800">Bulletins</h2>
        <p class="text-slate-600">Moyennes basées sur les <strong>examens</strong> uniquement (pas les exercices d'entraînement).</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($students as $student)
            <a href="{{ route('admin.bulletins.show', $student) }}" class="rounded-2xl bg-white p-5 shadow-sm transition hover:shadow-md">
                <h3 class="font-bold text-slate-800">{{ $student->name }}</h3>
                <p class="text-sm text-slate-500">{{ $student->level?->shortLabel() }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
