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

        if (! $submission->canAccessActivity()) {
            return redirect()
                ->route('student.activites.resultat', $activity)
                ->with('info', 'Cette activité est déjà terminée.');
        }

        if ($activity->isExam() && $submission->status === SubmissionStatus::Draft && ! $submission->exam_started_at) {
            $submission->update(['exam_started_at' => now()]);
            $submission->refresh();
        }

        $module = $activity->competency->resolveModule();

        return view($module->resolveStudentView($activity), compact('activity', 'submission', 'module'));
    }

    public function saveProgress(Request $request, Activity $activity): JsonResponse
    {
        $student = Auth::user();

        abort_unless($activity->is_published, 404);
        abort_unless(
            Activity::query()->published()->forStudent($student)->whereKey($activity->id)->exists(),
            403
        );

        $submission = $this->findSubmission($activity, $student);
        abort_unless($submission->canAccessActivity(), 403);

        $answers = $submission->answers ?? [];

        if ($request->has('answers') && is_array($request->input('answers'))) {
            $answers = array_replace_recursive($answers, $request->input('answers'));
        }

        if ($request->has('module_data') && is_array($request->input('module_data'))) {
            $answers['_module'] = array_merge($answers['_module'] ?? [], $request->input('module_data'));
        }

        $submission->update(['answers' => $answers]);

        return response()->json([
            'ok' => true,
            'saved_at' => now()->toIso8601String(),
        ]);
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
            return redirect()
                ->route('student.activites.resultat', $activity)
                ->with('info', 'Cette activité est déjà terminée.');
        }

        $module = $activity->competency->resolveModule();
        $answers = $module->mergeSubmissionPayload(
            $request->input('answers', []),
            $request->input('module_data', [])
        );

        $submission = $processor->process($activity, $submission, $answers);

        if ($submission->status === SubmissionStatus::Submitted) {
            return redirect()
                ->route('student.activites.resultat', $activity)
                ->with('success', 'Exercice envoyé ! Le professeur va le corriger.');
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
        $module = $activity->competency->resolveModule();

        return view($module->resolveResultView($activity), compact('activity', 'submission', 'module'));
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
