@extends('layouts.student')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-8 pb-8">
    <div class="text-center">
        <p class="text-sm font-semibold text-indigo-600">Mes points</p>
        <p class="mt-2 text-6xl font-extrabold text-amber-500">{{ $student->points_total }}</p>
        @if ($rank)
            <p class="mt-2 text-slate-600">Tu es <strong class="text-indigo-700">{{ $rank }}e</strong> de la classe 🏆</p>
        @endif
    </div>

    @php
        $hue = crc32($student->name) % 360;
        $initial = mb_strtoupper(mb_substr($student->name, 0, 1));
    @endphp
    <div class="mx-auto flex aspect-square max-w-[200px] flex-col items-center justify-center rounded-3xl bg-white p-6 shadow-lg">
        <div
            class="flex h-28 w-28 items-center justify-center rounded-3xl text-5xl font-extrabold text-white"
            style="background: hsl({{ $hue }} 65% 48%)"
        >{{ $initial }}</div>
        <p class="mt-4 text-xl font-extrabold text-slate-800">{{ $student->name }}</p>
        <p class="mt-1 text-2xl font-extrabold text-amber-600">{{ $student->points_total }} pts</p>
    </div>

    <section>
        <h2 class="mb-4 text-lg font-extrabold text-slate-800">Mon historique</h2>
        <div class="space-y-3">
            @forelse ($history as $tx)
                <article class="flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm {{ $tx->points >= 0 ? 'border-l-4 border-emerald-400' : 'border-l-4 border-red-400' }}">
                    <span class="text-3xl">{{ $tx->behavior?->icon ?? ($tx->points >= 0 ? '👍' : '👎') }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-800">{{ $tx->reason }}</p>
                        <p class="text-xs text-slate-500">{{ $tx->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-xl font-extrabold {{ $tx->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}
                    </span>
                </article>
            @empty
                <p class="rounded-2xl bg-white p-6 text-center text-slate-500 shadow-sm">Pas encore de points — continue comme ça !</p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-lg font-extrabold text-slate-800">Classement</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($classmates as $index => $mate)
                @php
                    $mi = mb_strtoupper(mb_substr($mate->name, 0, 1));
                    $mh = crc32($mate->name) % 360;
                    $isMe = $mate->id === $student->id;
                @endphp
                <div class="flex aspect-square flex-col items-center justify-center rounded-2xl p-3 {{ $isMe ? 'bg-indigo-100 ring-2 ring-indigo-400' : 'bg-white shadow-sm' }}">
                    <span class="mb-1 text-xs font-bold {{ $index < 3 ? 'text-amber-600' : 'text-slate-400' }}">{{ $index + 1 }}</span>
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl text-xl font-bold text-white" style="background: hsl({{ $mh }} 60% 50%)">{{ $mi }}</div>
                    <p class="mt-2 line-clamp-1 text-center text-xs font-bold text-slate-800">{{ $mate->name }}</p>
                    <p class="font-extrabold text-amber-700">{{ $mate->points_total }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <a href="{{ route('student.dashboard') }}" class="block text-center font-bold text-indigo-600">← Accueil</a>
</div>
@endsection
