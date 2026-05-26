@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="edu-title">Bulletins</h2>
        <p class="edu-subtitle">Moyennes basées sur les <strong>examens</strong> uniquement (pas les exercices d'entraînement).</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($students as $student)
            <a href="{{ route('admin.bulletins.show', $student) }}" class="edu-glass p-5 transition hover:shadow-md">
                <h3 class="font-bold text-slate-800">{{ $student->name }}</h3>
                <p class="text-sm text-slate-500">{{ $student->level?->shortLabel() }}</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
