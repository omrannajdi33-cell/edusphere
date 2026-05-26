<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CalendarEvent;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $student = Auth::user();

        return view('student.dashboard', [
            'student' => $student,
            'subjects' => Subject::query()->orderBy('sort_order')->get(),
            'announcements' => Announcement::query()->published()->latest('published_at')->limit(3)->get(),
            'upcomingEvents' => CalendarEvent::query()
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
            'recentPoints' => $student->pointTransactions()->latest()->limit(5)->get(),
        ]);
    }
}
