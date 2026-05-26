<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PointController extends Controller
{
    public function __invoke(): View
    {
        $student = Auth::user();

        $history = $student->pointTransactions()
            ->with('behavior')
            ->latest()
            ->limit(50)
            ->get();

        $classmates = User::query()
            ->where('role', UserRole::Student)
            ->where('is_active', true)
            ->orderByDesc('points_total')
            ->get();

        $rank = $classmates->search(fn ($u) => $u->id === $student->id);

        return view('student.points.index', [
            'student' => $student,
            'history' => $history,
            'classmates' => $classmates,
            'rank' => $rank !== false ? $rank + 1 : null,
        ]);
    }
}
