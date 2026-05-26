<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::query()->orderBy('sort_order')->get();

        return view('student.subjects.index', compact('subjects'));
    }

    public function show(Subject $subject): View
    {
        $student = Auth::user();

        $subject->load([
            'competencies' => fn ($q) => $q->with([
                'activities' => fn ($a) => $a->published()->forStudent($student)->orderBy('sort_order'),
            ]),
        ]);

        return view('student.subjects.show', compact('subject', 'student'));
    }
}
