@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <h2 class="edu-title">Mes matières</h2>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($subjects as $subject)
            <a href="{{ route('student.matieres.show', $subject) }}" class="edu-card-hover overflow-hidden rounded-3xl p-6 shadow-lg" style="background: linear-gradient(145deg, {{ $subject->color }} 0%, color-mix(in srgb, {{ $subject->color }} 65%, #8b5cf6) 100%);">
                <div class="text-5xl">{{ $subject->icon }}</div>
                <h3 class="mt-4 text-xl font-bold text-white">{{ $subject->name }}</h3>
            </a>
        @endforeach
    </div>
</div>
@endsection
