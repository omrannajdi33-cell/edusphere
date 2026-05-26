@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <h2 class="edu-title">Modifier l'annonce</h2>

    <form method="POST" action="{{ route('admin.annonces.update', $announcement) }}" class="space-y-4 edu-glass p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="edu-label">Titre</label>
            <input id="title" name="title" value="{{ old('title', $announcement->title) }}" required class="edu-input">
        </div>
        <div>
            <label for="body" class="edu-label">Message</label>
            <textarea id="body" name="body" rows="6" required class="edu-input">{{ old('body', $announcement->body) }}</textarea>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="publish_now" value="1" @checked(old('publish_now', (bool) $announcement->published_at)) class="h-5 w-5">
            <span class="text-sm font-semibold text-slate-700">Publiée (visible par les élèves)</span>
        </label>
        <div class="flex gap-3">
            <button type="submit" class="edu-btn-primary px-5 py-3">Mettre à jour</button>
            <a href="{{ route('admin.annonces.index') }}" class="edu-btn-secondary px-5 py-3">Annuler</a>
        </div>
    </form>
</div>
@endsection
