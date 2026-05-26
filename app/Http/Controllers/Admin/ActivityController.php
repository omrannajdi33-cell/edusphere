<?php

namespace App\Http\Controllers\Admin;

use App\ActivityPurpose;
use App\ActivityType;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySection;
use App\Models\Competency;
use App\Models\Question;
use App\Models\User;
use App\QuestionType;
use App\StudentLevel;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function create(Competency $competency): View
    {
        return view('admin.activities.create', $this->formData($competency));
    }

    public function store(Request $request, Competency $competency): RedirectResponse
    {
        $data = $this->validateActivity($request);
        $reading = $this->resolveReadingContent($request);

        $activity = $competency->activities()->create([
            'title' => $data['title'],
            'slug' => Str::slug($data['slug'] ?? $data['title']),
            'type' => $data['type'],
            'purpose' => ActivityPurpose::from($data['purpose']),
            'description' => $data['description'] ?? null,
            'reading_text' => $reading['text'],
            'reading_pdf_path' => $reading['pdf_path'],
            'level' => $this->resolveActivityLevel($request),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $data['sort_order'] ?? 0,
            'pdf_path' => $this->storeWorksheetPdf($request),
        ]);

        $this->syncAssignment($activity, $request);

        if ($activity->type === ActivityType::Dynamic) {
            $section = $activity->sections()->create([
                'title' => $request->input('section_title', 'Questions'),
                'sort_order' => 1,
            ]);

            $this->syncQuestions($section, $request->input('questions', []));
        }

        return redirect()
            ->route('admin.competences.activites.edit', [$competency, $activity])
            ->with('success', 'Activité créée.');
    }

    public function edit(Competency $competency, Activity $activity): View
    {
        abort_unless($activity->competency_id === $competency->id, 404);

        return view('admin.activities.edit', $this->formData($competency, $activity));
    }

    public function update(Request $request, Competency $competency, Activity $activity): RedirectResponse
    {
        abort_unless($activity->competency_id === $competency->id, 404);

        $data = $this->validateActivity($request);
        $reading = $this->resolveReadingContent($request, $activity);

        $activity->update([
            'title' => $data['title'],
            'slug' => Str::slug($data['slug'] ?? $data['title']),
            'type' => $data['type'],
            'purpose' => ActivityPurpose::from($data['purpose']),
            'description' => $data['description'] ?? null,
            'reading_text' => $reading['text'],
            'reading_pdf_path' => $reading['pdf_path'],
            'level' => $this->resolveActivityLevel($request),
            'is_published' => $request->boolean('is_published'),
            'sort_order' => $data['sort_order'] ?? 0,
            'pdf_path' => $this->storeWorksheetPdf($request) ?? $activity->pdf_path,
        ]);

        $this->syncAssignment($activity, $request);

        if ($activity->type === ActivityType::Dynamic) {
            $section = $activity->sections()->first();

            if (! $section) {
                $section = $activity->sections()->create(['title' => 'Questions', 'sort_order' => 1]);
            }

            $section->update(['title' => $request->input('section_title', $section->title)]);
            $activity->sections()->where('id', '!=', $section->id)->delete();
            $section->questions()->delete();
            $this->syncQuestions($section, $request->input('questions', []));
        }

        return back()->with('success', 'Activité mise à jour.');
    }

    public function destroy(Competency $competency, Activity $activity): RedirectResponse
    {
        abort_unless($activity->competency_id === $competency->id, 404);

        if ($activity->pdf_path) {
            Storage::disk('public')->delete($activity->pdf_path);
        }

        if ($activity->reading_pdf_path) {
            Storage::disk('public')->delete($activity->reading_pdf_path);
        }

        $subject = $competency->subject;
        $activity->delete();

        return redirect()
            ->route('admin.matieres.show', ['subject' => $subject, 'niveau' => request('niveau')])
            ->with('success', 'Activité supprimée.');
    }

    private function formData(Competency $competency, ?Activity $activity = null): array
    {
        if ($activity) {
            $activity->load(['sections.questions', 'assignedStudents']);
        }

        $section = $activity?->sections->first();

        $readingMode = old('reading_mode');
        if (! $readingMode && $activity) {
            $readingMode = $activity->hasReadingPdf() ? 'pdf' : ($activity->hasReadingText() ? 'text' : 'none');
        }
        $readingMode ??= 'none';

        return [
            'competency' => $competency->load('subject'),
            'activity' => $activity,
            'types' => ActivityType::cases(),
            'purposes' => ActivityPurpose::cases(),
            'questionTypes' => QuestionType::cases(),
            'levels' => StudentLevel::cases(),
            'students' => User::query()->where('role', UserRole::Student)->orderBy('name')->get(),
            'assignedStudentIds' => $activity
                ? $activity->assignedStudents->pluck('id')->all()
                : [],
            'questionsJson' => $this->serializeQuestionsForForm($section?->questions ?? collect()),
            'defaultType' => old('type', $activity?->type?->value ?? 'dynamic'),
            'defaultPurpose' => old('purpose', $activity?->purpose?->value ?? 'exercise'),
            'readingMode' => $readingMode,
        ];
    }

    private function serializeQuestionsForForm(Collection $questions): array
    {
        return $questions->map(function (Question $q) {
            return [
                'type' => $q->type->value,
                'prompt' => $q->prompt,
                'points' => $q->points,
                'options' => $q->config['options'] ?? [
                    ['text' => '', 'correct' => false],
                    ['text' => '', 'correct' => false],
                ],
                'correct' => ($q->config['correct'] ?? true) ? 'true' : 'false',
                'acceptable' => implode(', ', $q->config['acceptable'] ?? []),
                'numeric_answer' => $q->config['answer'] ?? 0,
                'tolerance' => $q->config['tolerance'] ?? 0,
                'items' => implode(' | ', $q->config['items'] ?? []),
                'correct_order' => implode(',', $q->config['correct_order'] ?? []),
                'blank_text' => $q->config['text'] ?? '',
                'blanks' => implode(', ', $q->config['blanks'] ?? []),
            ];
        })->values()->all();
    }

    private function validateActivity(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'purpose' => ['required', 'in:exercise,exam'],
            'type' => ['required', 'in:pdf,dynamic'],
            'description' => ['nullable', 'string'],
            'reading_mode' => ['required', 'in:none,text,pdf'],
            'reading_text' => ['nullable', 'string'],
            'reading_pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'level' => ['nullable', 'integer', 'in:1,2,3'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:users,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);
    }

    /**
     * @return array{text: ?string, pdf_path: ?string}
     */
    private function resolveReadingContent(Request $request, ?Activity $activity = null): array
    {
        $mode = $request->input('reading_mode', 'none');

        if ($mode === 'text') {
            if ($activity?->reading_pdf_path) {
                Storage::disk('public')->delete($activity->reading_pdf_path);
            }

            return [
                'text' => $request->input('reading_text'),
                'pdf_path' => null,
            ];
        }

        if ($mode === 'pdf') {
            $path = $this->storeReadingPdf($request) ?? $activity?->reading_pdf_path;

            return [
                'text' => null,
                'pdf_path' => $path,
            ];
        }

        if ($activity?->reading_pdf_path) {
            Storage::disk('public')->delete($activity->reading_pdf_path);
        }

        return ['text' => null, 'pdf_path' => null];
    }

    private function resolveActivityLevel(Request $request): ?StudentLevel
    {
        if ($request->filled('student_ids')) {
            return null;
        }

        $level = $request->input('level');

        return $level ? StudentLevel::from((int) $level) : null;
    }

    private function syncAssignment(Activity $activity, Request $request): void
    {
        $studentIds = $request->input('student_ids', []);

        if (! empty($studentIds)) {
            $validIds = User::query()
                ->where('role', UserRole::Student)
                ->whereIn('id', $studentIds)
                ->pluck('id');

            $activity->assignedStudents()->sync($validIds);
        } else {
            $activity->assignedStudents()->detach();
        }
    }

    private function storeWorksheetPdf(Request $request): ?string
    {
        if (! $request->hasFile('pdf_file')) {
            return null;
        }

        return $request->file('pdf_file')->store('activities/worksheets', 'public');
    }

    private function storeReadingPdf(Request $request): ?string
    {
        if (! $request->hasFile('reading_pdf_file')) {
            return null;
        }

        return $request->file('reading_pdf_file')->store('activities/reading', 'public');
    }

    private function syncQuestions(ActivitySection $section, array $questions): void
    {
        foreach ($questions as $index => $row) {
            if (empty($row['prompt'])) {
                continue;
            }

            $type = QuestionType::tryFrom($row['type'] ?? '') ?? QuestionType::MultipleChoice;

            Question::query()->create([
                'activity_section_id' => $section->id,
                'type' => $type,
                'prompt' => $row['prompt'],
                'points' => (int) ($row['points'] ?? 1),
                'sort_order' => $index + 1,
                'config' => $this->buildConfig($type, $row),
            ]);
        }
    }

    private function buildConfig(QuestionType $type, array $row): array
    {
        return match ($type) {
            QuestionType::MultipleChoice, QuestionType::MultipleSelect, QuestionType::Checkbox => [
                'options' => collect($row['options'] ?? [])
                    ->filter(fn ($o) => ! empty($o['text']))
                    ->values()
                    ->map(fn ($o, $i) => [
                        'id' => $o['id'] ?? 'opt_'.$i,
                        'text' => $o['text'],
                        'correct' => isset($o['correct']),
                    ])
                    ->all(),
            ],
            QuestionType::TrueFalse => [
                'correct' => ($row['correct'] ?? 'true') === 'true',
            ],
            QuestionType::ShortAnswer => [
                'acceptable' => array_filter(array_map('trim', explode(',', $row['acceptable'] ?? ''))),
            ],
            QuestionType::Numeric => [
                'answer' => (float) ($row['numeric_answer'] ?? 0),
                'tolerance' => (float) ($row['tolerance'] ?? 0),
            ],
            QuestionType::Ordering => [
                'items' => array_values(array_filter(array_map('trim', explode('|', $row['items'] ?? '')))),
                'correct_order' => array_map('intval', array_filter(explode(',', $row['correct_order'] ?? ''))),
            ],
            QuestionType::FillBlank => [
                'text' => $row['blank_text'] ?? '',
                'blanks' => array_filter(array_map('trim', explode(',', $row['blanks'] ?? ''))),
            ],
            default => [],
        };
    }
}
