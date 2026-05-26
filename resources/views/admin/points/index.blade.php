@extends('layouts.admin')

@section('content')
<div
    class="space-y-8"
    x-data="{
        open: false,
        studentId: null,
        studentName: '',
        pickStudent(id, name) {
            this.studentId = id;
            this.studentName = name;
            this.open = true;
        }
    }"
>
    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 px-4 py-3 font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <div>
        <h2 class="edu-title">Points — style ClassDojo</h2>
        <p class="edu-subtitle">Clique sur un élève, puis choisis une action positive ou à améliorer.</p>
    </div>

    <section>
        <h3 class="mb-4 text-lg font-bold text-slate-800">Ma classe</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @foreach ($students as $student)
                @php
                    $initial = mb_strtoupper(mb_substr($student->name, 0, 1));
                    $hue = crc32($student->name) % 360;
                @endphp
                <button
                    type="button"
                    @click="pickStudent({{ $student->id }}, @js($student->name))"
                    class="group flex aspect-square flex-col items-center justify-center rounded-3xl border-2 border-white bg-white p-4 shadow-md transition hover:scale-[1.03] hover:shadow-lg active:scale-95"
                >
                    <div
                        class="mb-3 flex h-20 w-20 items-center justify-center rounded-2xl edu-title shadow-inner sm:h-24 sm:w-24"
                        style="background: hsl({{ $hue }} 65% 48%)"
                    >{{ $initial }}</div>
                    <p class="line-clamp-2 text-center text-sm font-bold text-slate-800">{{ $student->name }}</p>
                    <p class="mt-2 rounded-full bg-amber-100 px-3 py-1 text-sm font-extrabold text-amber-800">{{ $student->points_total }}</p>
                </button>
            @endforeach
        </div>
    </section>

    {{-- Pop-up élève --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/60 p-4 sm:items-center" @keydown.escape.window="open = false">
        <div @click.outside="open = false" class="max-h-[92dvh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b bg-white px-5 py-4">
                <h3 class="text-lg font-extrabold text-slate-800" x-text="studentName"></h3>
                <button type="button" @click="open = false" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold">✕</button>
            </div>

            <div class="space-y-6 p-5">
                <div>
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-extrabold uppercase tracking-wide text-emerald-700">
                        <span class="text-xl">👍</span> Positif
                    </h4>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($positiveBehaviors as $behavior)
                            <form method="POST" action="{{ route('admin.points.store') }}">
                                @csrf
                                <input type="hidden" name="student_id" :value="studentId">
                                <input type="hidden" name="point_behavior_id" value="{{ $behavior->id }}">
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl border-2 border-emerald-100 bg-emerald-50 px-4 py-3 text-left transition hover:border-emerald-300 active:scale-[0.98]">
                                    <span class="text-2xl">{{ $behavior->icon }}</span>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $behavior->label }}</p>
                                        <p class="text-sm font-extrabold text-emerald-600">+{{ $behavior->points }}</p>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="mb-3 flex items-center gap-2 text-sm font-extrabold uppercase tracking-wide text-red-700">
                        <span class="text-xl">👎</span> À améliorer
                    </h4>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ($negativeBehaviors as $behavior)
                            <form method="POST" action="{{ route('admin.points.store') }}">
                                @csrf
                                <input type="hidden" name="student_id" :value="studentId">
                                <input type="hidden" name="point_behavior_id" value="{{ $behavior->id }}">
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl border-2 border-red-100 bg-red-50 px-4 py-3 text-left transition hover:border-red-300 active:scale-[0.98]">
                                    <span class="text-2xl">{{ $behavior->icon }}</span>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $behavior->label }}</p>
                                        <p class="text-sm font-extrabold text-red-600">-{{ $behavior->points }}</p>
                                    </div>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="grid gap-8 lg:grid-cols-2">
        <div class="edu-glass p-5">
            <h3 class="mb-4 font-bold text-slate-800">Ajouter une action</h3>
            <form method="POST" action="{{ route('admin.points.behaviors.store') }}" class="space-y-3">
                @csrf
                <input type="text" name="label" required placeholder="Nom de l'action" class="w-full rounded-xl border px-4 py-2">
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="points" min="1" max="20" value="1" required class="rounded-xl border px-4 py-2" placeholder="Valeur">
                    <select name="type" class="rounded-xl border px-4 py-2">
                        <option value="positive">Positive (+)</option>
                        <option value="negative">Négative (−)</option>
                    </select>
                </div>
                <input type="text" name="icon" maxlength="4" placeholder="Emoji (optionnel)" class="w-full rounded-xl border px-4 py-2">
                <button type="submit" class="edu-btn-primary w-full py-2">Ajouter</button>
            </form>
        </div>

        <div class="edu-glass p-5">
            <h3 class="mb-4 font-bold text-slate-800">Activité récente</h3>
            <div class="max-h-80 space-y-2 overflow-y-auto">
                @forelse ($recent as $tx)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <div>
                            <p class="font-semibold">{{ $tx->student->name }}</p>
                            <p class="text-slate-500">{{ $tx->behavior?->icon }} {{ $tx->reason }}</p>
                        </div>
                        <span class="font-extrabold {{ $tx->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-500">Aucun point donné encore.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
