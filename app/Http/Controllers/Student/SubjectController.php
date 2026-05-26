<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\Subject;
use App\SubmissionStatus;
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
                'activities' => fn ($a) => $a->published()
                    ->forStudent($student)
                    ->availableForStudent($student)
                    ->orderBy('sort_order'),
            ]),
        ]);

        $completedActivities = Activity::query()
            ->published()
            ->forStudent($student)
            ->whereHas('competency', fn ($q) => $q->where('subject_id', $subject->id))
            ->whereHas('submissions', fn ($q) => $q
                ->where('student_id', $student->id)
                ->whereIn('status', [SubmissionStatus::Submitted, SubmissionStatus::Graded]))
            ->with(['competency', 'submissions' => fn ($q) => $q->where('student_id', $student->id)])
            ->orderBy('sort_order')
            ->get();

        return view('student.subjects.show', compact('subject', 'student', 'completedActivities'));
    }
}
