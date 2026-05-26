<?php

namespace App\Http\Controllers\Student;

use App\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Services\SubmissionProcessor;
use App\Services\WorksheetAnnotationService;
use App\SubmissionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function show(Activity $activity): View|RedirectResponse
    {
        $student = Auth::user();

        abort_unless($activity->is_published, 404);
        abort_unless(
            Activity::query()->published()->forStudent($student)->whereKey($activity->id)->exists(),
            403
        );

        $activity->load(['competency.subject', 'sections.questions']);

        $submission = ActivitySubmission::query()->firstOrCreate(
            [
                'activity_id' => $activity->id,
                'student_id' => $student->id,
            ],
            ['status' => SubmissionStatus::Draft]
        );

        if ($activity->type === ActivityType::Pdf) {
            return view('student.activities.worksheet', compact('activity', 'submission'));
        }

        return view('student.activities.dynamic', compact('activity', 'submission'));
    }

    public function saveAnnotations(
        Request $request,
        Activity $activity,
        WorksheetAnnotationService $annotations
    ): JsonResponse {
        $student = Auth::user();
        $this->authorizeWorksheet($activity, $student);

        $submission = $this->findSubmission($activity, $student);
        abort_unless($submission->canEditWorksheet(), 403);

        $data = $request->validate([
            'annotations' => ['required', 'array'],
        ]);

        $merged = $annotations->mergeIncoming(
            $submission->annotations ?? [],
            $data['annotations'],
            'student'
        );

        $submission->update(['annotations' => $merged]);

        return response()->json(['ok' => true]);
    }

    public function submit(
        Request $request,
        Activity $activity,
        SubmissionProcessor $processor,
        WorksheetAnnotationService $annotations
    ): RedirectResponse|JsonResponse {
        $student = Auth::user();

        abort_unless($activity->is_published, 404);
        abort_unless(
            Activity::query()->published()->forStudent($student)->whereKey($activity->id)->exists(),
            403
        );

        if ($activity->type === ActivityType::Pdf) {
            $submission = $this->findSubmission($activity, $student);
            abort_unless($submission->canEditWorksheet(), 403);

            $data = $request->validate([
                'annotations' => ['nullable', 'array'],
            ]);

            if (isset($data['annotations'])) {
                $merged = $annotations->mergeIncoming(
                    $submission->annotations ?? [],
                    $data['annotations'],
                    'student'
                );
                $submission->annotations = $merged;
            }

            $submission->update([
                'annotations' => $submission->annotations,
                'status' => SubmissionStatus::Submitted,
                'needs_manual_review' => true,
                'submitted_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'redirect' => route('student.activites.resultat', $activity),
                ]);
            }

            return redirect()
                ->route('student.activites.resultat', $activity)
                ->with('success', 'Feuille envoyée au professeur !');
        }

        abort_unless($activity->type === ActivityType::Dynamic, 404);

        $submission = $this->findSubmission($activity, $student);

        if (! $processor->canSubmit($activity, $submission)) {
            return back()->with('error', 'Tu ne peux plus modifier cette épreuve (examen déjà envoyé).');
        }

        $submission = $processor->process($activity, $submission, $request->input('answers', []));

        if ($submission->status === SubmissionStatus::Submitted) {
            return redirect()
                ->route('student.activites.resultat', $activity)
                ->with('success', 'Examen envoyé ! Le professeur va corriger les questions restantes.');
        }

        $message = $activity->isExam()
            ? 'Examen terminé !'
            : 'Exercice terminé ! Tu peux le refaire pour t\'entraîner.';

        return redirect()
            ->route('student.activites.resultat', $activity)
            ->with('success', $message);
    }

    public function result(Activity $activity): View
    {
        $student = Auth::user();

        abort_unless(
            Activity::query()->published()->forStudent($student)->whereKey($activity->id)->exists(),
            403
        );

        $submission = ActivitySubmission::query()
            ->where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        abort_unless($submission->status !== SubmissionStatus::Draft, 404);

        $activity->load(['competency.subject', 'sections.questions']);

        if ($activity->type === ActivityType::Pdf) {
            return view('student.activities.worksheet-result', compact('activity', 'submission'));
        }

        return view('student.activities.result', compact('activity', 'submission'));
    }

    private function authorizeWorksheet(Activity $activity, $student): void
    {
        abort_unless($activity->is_published, 404);
        abort_unless($activity->type === ActivityType::Pdf, 404);
        abort_unless(
            Activity::query()->published()->forStudent($student)->whereKey($activity->id)->exists(),
            403
        );
    }

    private function findSubmission(Activity $activity, $student): ActivitySubmission
    {
        return ActivitySubmission::query()
            ->where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->firstOrFail();
    }
}
