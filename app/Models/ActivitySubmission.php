<?php

namespace App\Models;

use App\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySubmission extends Model
{
    protected $fillable = [
        'activity_id',
        'student_id',
        'status',
        'answers',
        'annotations',
        'score',
        'max_score',
        'counts_for_bulletin',
        'percentage',
        'needs_manual_review',
        'teacher_comment',
        'grading_details',
        'graded_by_id',
        'graded_at',
        'submitted_at',
        'exam_started_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'answers' => 'array',
            'annotations' => 'array',
            'counts_for_bulletin' => 'boolean',
            'needs_manual_review' => 'boolean',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'exam_started_at' => 'datetime',
            'grading_details' => 'array',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_id');
    }

    public function isLocked(): bool
    {
        $this->loadMissing('activity');

        if ($this->activity->type === \App\ActivityType::Pdf) {
            return ! $this->canEditWorksheet();
        }

        return $this->activity->isExam() && $this->status !== SubmissionStatus::Draft;
    }

    public function canEditWorksheet(): bool
    {
        return in_array($this->status, [SubmissionStatus::Draft, SubmissionStatus::Returned], true);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [SubmissionStatus::Submitted, SubmissionStatus::Graded], true);
    }

    public function canAccessActivity(): bool
    {
        return in_array($this->status, [SubmissionStatus::Draft, SubmissionStatus::Returned], true);
    }
}
