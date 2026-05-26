@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <h2 class="text-2xl font-extrabold text-slate-800">Nouvelle annonce</h2>

    <form method="POST" action="{{ route('admin.annonces.store') }}" class="space-y-4 rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label for="title" class="mb-2 block text-sm font-semibold text-slate-700">Titre</label>
            <input id="title" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="body" class="mb-2 block text-sm font-semibold text-slate-700">Message</label>
            <textarea id="body" name="body" rows="6" required class="w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('body') }}</textarea>
            @error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="publish_now" value="1" @checked(old('publish_now', true)) class="h-5 w-5">
            <span class="text-sm font-semibold text-slate-700">Publier immédiatement</span>
        </label>
        <div class="flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Enregistrer</button>
            <a href="{{ route('admin.annonces.index') }}" class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700">Annuler</a>
        </div>
    </form>
</div>
@endsection
