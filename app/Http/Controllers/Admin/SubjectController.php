<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\StudentLevel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::query()
            ->withCount('competencies')
            ->orderBy('sort_order')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function show(Request $request, Subject $subject): View
    {
        $selectedLevel = $request->filled('niveau')
            ? (int) $request->input('niveau')
            : null;

        $subject->load([
            'competencies.activities' => function ($q) use ($selectedLevel) {
                $q->orderBy('sort_order')->with('assignedStudents');
                if ($selectedLevel) {
                    $q->forAdminLevel($selectedLevel);
                }
            },
        ]);

        $levels = StudentLevel::cases();

        return view('admin.subjects.show', compact('subject', 'levels', 'selectedLevel'));
    }
}
