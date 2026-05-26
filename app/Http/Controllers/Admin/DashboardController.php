<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CalendarEvent;
use App\Models\Subject;
use App\Models\User;
use App\UserRole;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'studentsCount' => User::query()->where('role', UserRole::Student)->count(),
            'subjectsCount' => Subject::query()->count(),
            'announcementsCount' => Announcement::query()->count(),
            'upcomingEvents' => CalendarEvent::query()
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
            'subjects' => Subject::query()->withCount('competencies')->orderBy('sort_order')->get(),
        ]);
    }
}
