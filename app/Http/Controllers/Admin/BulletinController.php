<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BulletinService;
use App\UserRole;
use Illuminate\View\View;

class BulletinController extends Controller
{
    public function index(BulletinService $bulletins): View
    {
        $students = $bulletins->studentsWithBulletins();

        return view('admin.bulletins.index', compact('students'));
    }

    public function show(User $student, BulletinService $bulletins): View
    {
        abort_unless($student->role === UserRole::Student, 404);

        $rows = $bulletins->forStudent($student);
        $generalAverage = $bulletins->generalExamAverage($student);

        return view('admin.bulletins.show', compact('student', 'rows', 'generalAverage'));
    }
}
