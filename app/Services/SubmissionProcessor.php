<?php

namespace App\Services;

use App\ActivityPurpose;
use App\GradingMode;
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
        return $submission->canAccessActivity();
    }

    public function process(Activity $activity, ActivitySubmission $submission, array $answers): ActivitySubmission
    {
        $activity->loadMissing(['sections.questions']);

        if ($activity->grading_mode === GradingMode::Manual) {
            return $this->processManual($activity, $submission, $answers);
        }

        $result = $this->grader->gradeActivity($activity, $answers);
        $needsManual = $this->needsManualReview($activity);
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
            'graded_by_id' => null,
        ]);

        if ($isFullyGraded && $isExam) {
            $submission->update([
                'graded_at' => now(),
            ]);
        }

        return $submission->fresh();
    }

    private function processManual(Activity $activity, ActivitySubmission $submission, array $answers): ActivitySubmission
    {
        $maxScore = $activity->sections->flatMap->questions->sum('points');

        $submission->update([
            'answers' => $answers,
            'score' => null,
            'max_score' => $maxScore > 0 ? $maxScore : null,
            'percentage' => null,
            'counts_for_bulletin' => $activity->purpose === ActivityPurpose::Exam,
            'needs_manual_review' => true,
            'status' => SubmissionStatus::Submitted,
            'submitted_at' => now(),
            'graded_at' => null,
            'graded_by_id' => null,
        ]);

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
