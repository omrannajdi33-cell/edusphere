@extends('layouts.student')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-8 pb-8">
    <div class="text-center">
        <p class="edu-kicker">Mes points</p>
        <p class="mt-2 text-5xl font-bold text-slate-900">{{ $student->points_total }}</p>
        @if ($rank)
            <p class="mt-2 text-slate-600">Tu es <strong class="text-indigo-600">{{ $rank }}e</strong> de la classe 🏆</p>
        @endif
    </div>

    @php
        $hue = crc32($student->name) % 360;
        $initial = mb_strtoupper(mb_substr($student->name, 0, 1));
    @endphp
    <div class="edu-glass mx-auto flex aspect-square max-w-[200px] flex-col items-center justify-center p-6">
        <div class="flex h-24 w-24 items-center justify-center rounded-2xl text-4xl font-bold text-white shadow-md" style="background: hsl({{ $hue }} 70% 55%)">{{ $initial }}</div>
        <p class="mt-4 text-lg font-bold text-slate-900">{{ $student->name }}</p>
        <p class="text-xl font-bold text-amber-600">{{ $student->points_total }} pts</p>
    </div>

    <section>
        <h2 class="mb-4 text-lg font-bold text-slate-900">Mon historique</h2>
        <div class="space-y-2">
            @forelse ($history as $tx)
                <article class="edu-glass flex items-center gap-4 p-4 {{ $tx->points >= 0 ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-rose-400' }}">
                    <span class="text-2xl">{{ $tx->behavior?->icon ?? ($tx->points >= 0 ? '👍' : '👎') }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-slate-900">{{ $tx->reason }}</p>
                        <p class="text-xs text-slate-500">{{ $tx->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <span class="text-lg font-bold {{ $tx->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}</span>
                </article>
            @empty
                <p class="edu-glass p-6 text-center text-slate-500">Pas encore de points — continue comme ça !</p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-lg font-bold text-slate-900">Classement</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            @foreach ($classmates as $index => $mate)
                @php
                    $mi = mb_strtoupper(mb_substr($mate->name, 0, 1));
                    $mh = crc32($mate->name) % 360;
                    $isMe = $mate->id === $student->id;
                @endphp
                <div class="flex aspect-square flex-col items-center justify-center rounded-2xl p-3 {{ $isMe ? 'ring-2 ring-indigo-400 edu-glass-strong' : 'edu-glass' }}">
                    <span class="mb-1 text-xs font-bold {{ $index < 3 ? 'text-amber-600' : 'text-slate-400' }}">{{ $index + 1 }}</span>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl text-lg font-bold text-white" style="background: hsl({{ $mh }} 65% 52%)">{{ $mi }}</div>
                    <p class="mt-2 line-clamp-1 text-center text-xs font-semibold text-slate-800">{{ $mate->name }}</p>
                    <p class="font-bold text-amber-700">{{ $mate->points_total }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <a href="{{ route('student.dashboard') }}" class="edu-link block text-center">← Accueil</a>
</div>
@endsection
