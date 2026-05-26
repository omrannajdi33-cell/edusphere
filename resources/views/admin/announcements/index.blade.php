@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="edu-title">Annonces</h2>
            <p class="edu-subtitle">Publie des messages pour les élèves.</p>
        </div>
        <a href="{{ route('admin.annonces.create') }}" class="touch-target edu-btn-primary px-5 py-3 hover:bg-indigo-700">+ Nouvelle annonce</a>
    </div>

    <div class="space-y-3">
        @forelse ($announcements as $announcement)
            <article class="edu-glass p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ $announcement->title }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            @if ($announcement->published_at)
                                Publiée le {{ $announcement->published_at->translatedFormat('d M Y, H:i') }}
                            @else
                                Brouillon
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.annonces.edit', $announcement) }}" class="rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700">Modifier</a>
                        <form method="POST" action="{{ route('admin.annonces.destroy', $announcement) }}" onsubmit="return confirm('Supprimer cette annonce ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">Supprimer</button>
                        </form>
                    </div>
                </div>
                <p class="mt-3 text-slate-600">{{ Str::limit($announcement->body, 200) }}</p>
            </article>
        @empty
            <p class="rounded-2xl bg-white p-6 text-slate-500 shadow-sm">Aucune annonce pour le moment.</p>
        @endforelse
    </div>

    {{ $announcements->links() }}
</div>
@endsection
