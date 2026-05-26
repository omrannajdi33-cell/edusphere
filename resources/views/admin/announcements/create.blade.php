@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <h2 class="edu-title">Nouvelle annonce</h2>

    <form method="POST" action="{{ route('admin.annonces.store') }}" class="space-y-4 edu-glass p-6 space-y-4">
        @csrf
        <div>
            <label for="title" class="edu-label">Titre</label>
            <input id="title" name="title" value="{{ old('title') }}" required class="edu-input">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="edu-label">Message</label>
            <textarea id="body" name="body" rows="6" required class="edu-input">{{ old('body') }}</textarea>
            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="publish_now" value="1" @checked(old('publish_now', true)) class="h-5 w-5">
            <span class="text-sm font-semibold text-slate-700">Publier immédiatement</span>
        </label>
        <div class="flex gap-3">
            <button type="submit" class="edu-btn-primary px-5 py-3">Enregistrer</button>
            <a href="{{ route('admin.annonces.index') }}" class="edu-btn-secondary px-5 py-3">Annuler</a>
        </div>
    </form>
</div>
@endsection
