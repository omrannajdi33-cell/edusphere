<?php

namespace App\Services;

use App\ActivityPurpose;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\Question;
use App\QuestionType;
use App\SubmissionStatus;
use Illuminate\Support\Facades\Auth;

class SubmissionProcessor
{
    public function __construct(private QuestionGrader $grader) {}

    public function canSubmit(Activity $activity, ActivitySubmission $submission): bool
    {
        if ($submission->status === SubmissionStatus::Draft) {
            return true;
        }

        return $activity->purpose === ActivityPurpose::Exercise;
    }

    public function process(Activity $activity, ActivitySubmission $submission, array $answers): ActivitySubmission
    {
        $activity->loadMissing(['sections.questions']);

        $result = $this->grader->gradeActivity($activity, $answers);
        $needsManual = $activity->purpose === ActivityPurpose::Exam && $this->needsManualReview($activity);
        $percentage = $result['max_score'] > 0
            ? (int) round(($result['score'] / $result['max_score']) * 100)
            : 0;

        $isExam = $activity->purpose === ActivityPurpose::Exam;
        $isFullyGraded = ! $needsManual;

        $submission->update([
            'answers' => $answers,
            'score' => $result['score'],
            'max_score' => $result['max_score'],
            'percentage' => $percentage,
            'counts_for_bulletin' => $isExam,
            'needs_manual_review' => $needsManual,
            'status' => $isFullyGraded ? SubmissionStatus::Graded : SubmissionStatus::Submitted,
            'submitted_at' => now(),
            'graded_at' => $isFullyGraded ? now() : null,
            'graded_by_id' => $isFullyGraded ? null : null,
        ]);

        if ($isFullyGraded && $isExam) {
            $submission->update([
                'graded_at' => now(),
            ]);
        }

        return $submission->fresh();
    }

    public function finalizeExamGrade(ActivitySubmission $submission, ?int $score, ?string $comment): void
    {
        $submission->update([
            'score' => $score ?? $submission->score,
            'percentage' => ($submission->max_score ?? 0) > 0
                ? (int) round((($score ?? $submission->score) / $submission->max_score) * 100)
                : 0,
            'teacher_comment' => $comment,
            'needs_manual_review' => false,
            'status' => SubmissionStatus::Graded,
            'graded_at' => now(),
            'graded_by_id' => Auth::id(),
        ]);
    }

    private function needsManualReview(Activity $activity): bool
    {
        foreach ($activity->sections as $section) {
            foreach ($section->questions as $question) {
                if ($this->questionRequiresManualReview($question)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function questionRequiresManualReview(Question $question): bool
    {
        return in_array($question->type, [
            QuestionType::LongAnswer,
            QuestionType::Audio,
            QuestionType::DragDrop,
            QuestionType::ImageHotspot,
            QuestionType::ImageAssociation,
            QuestionType::TableFill,
        ], true);
    }
}
