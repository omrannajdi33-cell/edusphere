<?php

namespace App\Services;

use App\ActivityPurpose;
use App\Models\ActivitySubmission;
use App\Models\Subject;
use App\Models\User;
use App\SubmissionStatus;
use App\UserRole;
use Illuminate\Support\Collection;

class BulletinService
{
    /**
     * @return Collection<int, array{subject: Subject, exam_average: ?float, exercise_average: ?float, exams_count: int, exercises_count: int}>
     */
    public function forStudent(User $student): Collection
    {
        $subjects = Subject::query()->orderBy('sort_order')->get();

        return $subjects->map(function (Subject $subject) use ($student) {
            $submissions = ActivitySubmission::query()
                ->where('student_id', $student->id)
                ->where('status', SubmissionStatus::Graded)
                ->whereHas('activity.competency', fn ($q) => $q->where('subject_id', $subject->id))
                ->with('activity')
                ->get();

            $exams = $submissions->filter(fn ($s) => $s->activity->purpose === ActivityPurpose::Exam);
            $exercises = $submissions->filter(fn ($s) => $s->activity->purpose === ActivityPurpose::Exercise);

            return [
                'subject' => $subject,
                'exam_average' => $this->averagePercentage($exams),
                'exercise_average' => $this->averagePercentage($exercises),
                'exams_count' => $exams->count(),
                'exercises_count' => $exercises->count(),
            ];
        });
    }

    public function generalExamAverage(User $student): ?float
    {
        $avg = ActivitySubmission::query()
            ->where('student_id', $student->id)
            ->where('status', SubmissionStatus::Graded)
            ->where('counts_for_bulletin', true)
            ->whereNotNull('percentage')
            ->avg('percentage');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * @return Collection<int, User>
     */
    public function studentsWithBulletins(): Collection
    {
        return User::query()
            ->where('role', UserRole::Student)
            ->orderBy('name')
            ->get();
    }

    private function averagePercentage(Collection $submissions): ?float
    {
        if ($submissions->isEmpty()) {
            return null;
        }

        return round($submissions->avg('percentage'), 1);
    }
}
