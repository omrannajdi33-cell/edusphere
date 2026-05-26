@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <p class="text-sm text-slate-500">{{ $competency->subject->name }} → {{ $competency->name }}</p>
        <h2 class="text-2xl font-extrabold text-slate-800">Nouvelle activité</h2>
    </div>

    <form method="POST" action="{{ route('admin.competences.activites.store', $competency) }}" enctype="multipart/form-data" class="rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @include('admin.activities._form', ['activity' => null, 'types' => $types, 'questionTypes' => $questionTypes])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Créer</button>
            <a href="{{ route('admin.matieres.show', $competency->subject) }}" class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700">Annuler</a>
        </div>
    </form>
</div>
@endsection
