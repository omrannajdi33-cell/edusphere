@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.bulletins.index') }}" class="text-sm font-semibold text-indigo-600">← Bulletins</a>
            <h2 class="text-2xl font-extrabold text-slate-800">Bulletin — {{ $student->name }}</h2>
            <p class="text-slate-600">{{ $student->level?->label() }}</p>
        </div>
        @if ($generalAverage !== null)
            <div class="rounded-2xl bg-indigo-600 px-6 py-4 text-center text-white">
                <p class="text-xs font-bold uppercase opacity-80">Moyenne examens</p>
                <p class="text-4xl font-extrabold">{{ $generalAverage }} %</p>
            </div>
        @endif
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
        <table class="w-full min-w-[500px] text-left text-sm">
            <thead class="border-b bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3">Matière</th>
                    <th class="px-4 py-3">Moy. examens (bulletin)</th>
                    <th class="px-4 py-3">Examens</th>
                    <th class="px-4 py-3">Moy. exercices (info)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr class="border-b border-slate-100">
                        <td class="px-4 py-3 font-semibold">{{ $row['subject']->icon }} {{ $row['subject']->name }}</td>
                        <td class="px-4 py-3 font-bold text-indigo-700">{{ $row['exam_average'] !== null ? $row['exam_average'].' %' : '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $row['exams_count'] }}</td>
                        <td class="px-4 py-3 text-slate-400">{{ $row['exercise_average'] !== null ? $row['exercise_average'].' %' : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="text-xs text-slate-500">Les exercices servent à s'entraîner et n'entrent pas dans la moyenne du bulletin.</p>
</div>
@endsection
