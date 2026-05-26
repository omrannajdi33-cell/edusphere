<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointBehavior;
use App\Models\PointTransaction;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PointController extends Controller
{
    public function index(): View
    {
        $students = User::query()
            ->where('role', UserRole::Student)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $positiveBehaviors = PointBehavior::query()->active()->positive()->get();
        $negativeBehaviors = PointBehavior::query()->active()->negative()->get();

        $recent = PointTransaction::query()
            ->with(['student', 'behavior', 'givenBy'])
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.points.index', compact('students', 'positiveBehaviors', 'negativeBehaviors', 'recent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'point_behavior_id' => ['required', 'exists:point_behaviors,id'],
        ]);

        $student = User::query()->where('id', $data['student_id'])->where('role', UserRole::Student)->firstOrFail();
        $behavior = PointBehavior::query()->where('id', $data['point_behavior_id'])->where('is_active', true)->firstOrFail();

        $points = $behavior->signedPoints();

        PointTransaction::query()->create([
            'student_id' => $student->id,
            'point_behavior_id' => $behavior->id,
            'points' => $points,
            'reason' => $behavior->label,
            'given_by_id' => Auth::id(),
        ]);

        $student->increment('points_total', $points);

        return back()->with('success', "{$behavior->label} : ".($points > 0 ? '+' : '')."{$points} pour {$student->name}");
    }

    public function storeBehavior(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'points' => ['required', 'integer', 'min:1', 'max:20'],
            'type' => ['required', 'in:positive,negative'],
            'icon' => ['nullable', 'string', 'max:10'],
        ]);

        PointBehavior::query()->create([
            'label' => $data['label'],
            'points' => (int) $data['points'],
            'type' => $data['type'],
            'icon' => $data['icon'] ?? ($data['type'] === 'positive' ? '👍' : '👎'),
            'sort_order' => PointBehavior::query()->where('type', $data['type'])->count() + 1,
        ]);

        return back()->with('success', 'Action ajoutée.');
    }

    public function destroyBehavior(PointBehavior $behavior): RedirectResponse
    {
        $behavior->update(['is_active' => false]);

        return back()->with('success', 'Action désactivée.');
    }
}
