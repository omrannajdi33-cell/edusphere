<?php

namespace App\Http\Controllers\Admin;

use App\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\ActivitySubmission;
use App\Services\SubmissionProcessor;
use App\Services\WorksheetAnnotationService;
use App\SubmissionStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(): View
    {
        $pending = ActivitySubmission::query()
            ->where('status', SubmissionStatus::Submitted)
            ->where(function ($query) {
                $query->where('needs_manual_review', true)
                    ->orWhereHas('activity', fn ($aq) => $aq->where('type', ActivityType::Pdf));
            })
            ->with(['student', 'activity.competency.subject'])
            ->latest('submitted_at')
            ->get();

        return view('admin.submissions.index', compact('pending'));
    }

    public function edit(ActivitySubmission $submission): View
    {
        $submission->load(['student', 'activity.sections.questions', 'activity.competency.subject']);

        if ($submission->activity->type === ActivityType::Pdf) {
            return view('admin.submissions.worksheet', compact('submission'));
        }

        return view('admin.submissions.edit', compact('submission'));
    }

    public function update(
        Request $request,
        ActivitySubmission $submission,
        SubmissionProcessor $processor,
        WorksheetAnnotationService $annotations
    ): RedirectResponse {
        if ($submission->activity->type === ActivityType::Pdf) {
            return $this->updateWorksheet($request, $submission, $annotations);
        }

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0'],
            'teacher_comment' => ['nullable', 'string'],
        ]);

        $submission->update([
            'score' => min($data['score'], $submission->max_score ?? $data['score']),
        ]);

        $processor->finalizeExamGrade($submission, $submission->score, $data['teacher_comment'] ?? null);

        return redirect()
            ->route('admin.corrections.index')
            ->with('success', 'Correction enregistrée — note comptée au bulletin.');
    }

    private function updateWorksheet(
        Request $request,
        ActivitySubmission $submission,
        WorksheetAnnotationService $annotations
    ): RedirectResponse {
        $data = $request->validate([
            'annotations' => ['nullable', 'string'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'teacher_comment' => ['nullable', 'string'],
            'action' => ['required', 'in:validate,return'],
        ]);

        if (! empty($data['annotations'])) {
            $incoming = json_decode($data['annotations'], true);
            if (is_array($incoming)) {
                $submission->annotations = $annotations->mergeIncoming(
                    $submission->annotations ?? [],
                    $incoming,
                    'teacher'
                );
            }
        }

        if ($data['action'] === 'return') {
            $submission->update([
                'annotations' => $submission->annotations,
                'status' => SubmissionStatus::Returned,
                'needs_manual_review' => false,
                'teacher_comment' => $data['teacher_comment'] ?? null,
            ]);

            return redirect()
                ->route('admin.corrections.index')
                ->with('success', 'Feuille renvoyée à l\'élève pour correction.');
        }

        $submission->update([
            'annotations' => $submission->annotations,
            'status' => SubmissionStatus::Graded,
            'needs_manual_review' => false,
            'score' => $data['score'] ?? $submission->score,
            'max_score' => 100,
            'percentage' => $data['score'] ?? $submission->score,
            'teacher_comment' => $data['teacher_comment'] ?? null,
            'graded_at' => now(),
            'graded_by_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.corrections.index')
            ->with('success', 'Correction validée — l\'élève peut consulter ta correction.');
    }
}
