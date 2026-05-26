<?php

namespace App\Models;

use App\ActivityPurpose;
use App\ActivityType;
use App\GradingMode;
use App\StudentLevel;
use App\SubmissionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    protected $fillable = [
        'competency_id',
        'title',
        'slug',
        'type',
        'purpose',
        'grading_mode',
        'exam_duration_minutes',
        'description',
        'reading_text',
        'reading_pdf_path',
        'level',
        'pdf_path',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'purpose' => ActivityPurpose::class,
            'grading_mode' => GradingMode::class,
            'level' => StudentLevel::class,
            'is_published' => 'boolean',
        ];
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ActivitySection::class)->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ActivitySubmission::class);
    }

    public function assignedStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_student')->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForStudent(Builder $query, User $student): Builder
    {
        return $query->where(function (Builder $q) use ($student) {
            $q->whereHas('assignedStudents', fn (Builder $sq) => $sq->where('users.id', $student->id))
                ->orWhere(function (Builder $q2) use ($student) {
                    $q2->whereDoesntHave('assignedStudents')
                        ->where(function (Builder $q3) use ($student) {
                            $q3->whereNull('level');
                            if ($student->level) {
                                $q3->orWhere('level', $student->level);
                            }
                        });
                });
        });
    }

    public function scopeForAdminLevel(Builder $query, ?int $level): Builder
    {
        if ($level === null) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($level) {
            $q->whereNull('level')->orWhere('level', $level);
        });
    }

    public function isExam(): bool
    {
        return $this->purpose === ActivityPurpose::Exam;
    }

    public function isExercise(): bool
    {
        return $this->purpose === ActivityPurpose::Exercise;
    }

    public function hasReadingMaterial(): bool
    {
        return $this->hasReadingText() || $this->hasReadingPdf();
    }

    public function hasReadingText(): bool
    {
        return filled(trim($this->reading_text ?? ''));
    }

    public function hasReadingPdf(): bool
    {
        return filled($this->reading_pdf_path);
    }

    public function readingPdfUrl(): ?string
    {
        return $this->reading_pdf_path
            ? Storage::disk('public')->url($this->reading_pdf_path)
            : null;
    }

    public function worksheetPdfUrl(): ?string
    {
        return $this->pdf_path
            ? Storage::disk('public')->url($this->pdf_path)
            : null;
    }

    public function scopeAvailableForStudent(Builder $query, User $student): Builder
    {
        return $query->whereDoesntHave('submissions', function (Builder $q) use ($student) {
            $q->where('student_id', $student->id)
                ->whereIn('status', [SubmissionStatus::Submitted, SubmissionStatus::Graded]);
        });
    }

    public function usesManualGrading(): bool
    {
        return $this->grading_mode === GradingMode::Manual;
    }

    public function assignmentLabel(): string
    {
        if ($this->relationLoaded('assignedStudents')) {
            $count = $this->assignedStudents->count();
        } else {
            $count = $this->assignedStudents()->count();
        }

        if ($count > 0) {
            return 'Élèves sélectionnés ('.$count.')';
        }

        if ($this->level) {
            return $this->level->shortLabel();
        }

        return 'Tous les niveaux';
    }
}
