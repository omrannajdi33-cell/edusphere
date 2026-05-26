<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\StudentLevel;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', UserRole::Student)
            ->orderBy('name')
            ->paginate(12);

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create', [
            'levels' => StudentLevel::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'password' => ['required', 'string', 'min:6'],
            'birth_date' => ['nullable', 'date'],
            'level' => ['required', 'integer', 'in:1,2,3'],
        ]);

        User::query()->create([
            ...$data,
            'role' => UserRole::Student,
            'level' => StudentLevel::from((int) $data['level']),
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.eleves.index')->with('success', 'Élève ajouté avec succès.');
    }

    public function show(User $student): View
    {
        abort_unless($student->role === UserRole::Student, 404);

        return view('admin.students.show', [
            'student' => $student->load('pointTransactions.givenBy'),
        ]);
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === UserRole::Student, 404);

        return view('admin.students.edit', [
            'student' => $student,
            'levels' => StudentLevel::cases(),
        ]);
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === UserRole::Student, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,'.$student->id],
            'birth_date' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'level' => ['required', 'integer', 'in:1,2,3'],
        ]);

        $student->update([
            'name' => $data['name'],
            'username' => $data['username'],
            'birth_date' => $data['birth_date'] ?? null,
            'level' => StudentLevel::from((int) $data['level']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.eleves.show', $student)->with('success', 'Profil élève mis à jour.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === UserRole::Student, 404);

        $student->delete();

        return redirect()->route('admin.eleves.index')->with('success', 'Élève supprimé.');
    }

    public function resetPassword(User $student): RedirectResponse
    {
        abort_unless($student->role === UserRole::Student, 404);

        $temporaryPassword = Str::password(8, letters: true, numbers: true, symbols: false);

        $student->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return back()->with('success', "Mot de passe réinitialisé : {$temporaryPassword}");
    }
}
