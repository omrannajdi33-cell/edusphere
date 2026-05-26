@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <p class="text-sm text-slate-500">{{ $competency->subject->name }} → {{ $competency->name }}</p>
        <h2 class="edu-title">Nouvelle activité</h2>
    </div>

    <form method="POST" action="{{ route('admin.competences.activites.store', $competency) }}" enctype="multipart/form-data" class="edu-glass p-6 space-y-4">
        @csrf
        @include('admin.activities._form', ['activity' => null, 'types' => $types, 'questionTypes' => $questionTypes])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="edu-btn-primary px-5 py-3">Créer</button>
            <a href="{{ route('admin.matieres.show', $competency->subject) }}" class="edu-btn-secondary px-5 py-3">Annuler</a>
        </div>
    </form>
</div>
@endsection
