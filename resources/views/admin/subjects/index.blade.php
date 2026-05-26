@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800">Matières</h2>
        <p class="text-slate-600">Gère les compétences et les activités par matière.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($subjects as $subject)
            <a href="{{ route('admin.matieres.show', $subject) }}" class="rounded-2xl bg-white p-5 shadow-sm transition hover:shadow-md active:scale-[0.98]">
                <div class="text-4xl">{{ $subject->icon }}</div>
                <h3 class="mt-3 text-lg font-extrabold" style="color: {{ $subject->color }}">{{ $subject->name }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $subject->competencies_count }} compétences</p>
            </a>
        @endforeach
    </div>
</div>
@endsection
