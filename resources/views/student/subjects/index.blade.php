@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <h2 class="text-2xl font-extrabold text-white">Mes matières</h2>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($subjects as $subject)
            <a href="{{ route('student.matieres.show', $subject) }}" class="overflow-hidden rounded-3xl p-6 shadow-xl transition active:scale-[0.98]" style="background: linear-gradient(145deg, {{ $subject->color }} 0%, #0f172a 120%);">
                <div class="text-5xl">{{ $subject->icon }}</div>
                <h3 class="mt-4 text-xl font-extrabold text-white">{{ $subject->name }}</h3>
            </a>
        @endforeach
    </div>
</div>
@endsection
