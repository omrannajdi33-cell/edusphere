@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">{{ $competency->subject->name }} → {{ $competency->name }}</p>
            <h2 class="text-2xl font-extrabold text-slate-800">{{ $activity->title }}</h2>
        </div>
        <form method="POST" action="{{ route('admin.competences.activites.destroy', [$competency, $activity]) }}" onsubmit="return confirm('Supprimer cette activité ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-700">Supprimer</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.competences.activites.update', [$competency, $activity]) }}" enctype="multipart/form-data" class="rounded-2xl bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.activities._form', ['activity' => $activity, 'types' => $types, 'questionTypes' => $questionTypes])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-bold text-white">Enregistrer</button>
            <a href="{{ route('admin.matieres.show', $competency->subject) }}" class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-700">Retour</a>
        </div>
    </form>
</div>
@endsection
