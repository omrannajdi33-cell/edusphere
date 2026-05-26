<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BulletinController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PointController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController as AdminSubjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\ActivityController as StudentActivityController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PointController as StudentPointController;
use App\Http\Controllers\Student\SubjectController as StudentSubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'show'])->name('login');
    Route::post('/connexion', [LoginController::class, 'login']);
});

Route::post('/deconnexion', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('eleves', StudentController::class)->parameters(['eleves' => 'student']);
    Route::resource('annonces', AnnouncementController::class)
        ->parameters(['annonces' => 'annonce'])
        ->except(['show']);
    Route::post('eleves/{student}/reinitialiser-mot-de-passe', [StudentController::class, 'resetPassword'])
        ->name('students.reset-password');

    Route::get('matieres', [AdminSubjectController::class, 'index'])->name('matieres.index');
    Route::get('matieres/{subject}', [AdminSubjectController::class, 'show'])->name('matieres.show');

    Route::get('competences/{competency}/activites/create', [AdminActivityController::class, 'create'])->name('competences.activites.create');
    Route::post('competences/{competency}/activites', [AdminActivityController::class, 'store'])->name('competences.activites.store');
    Route::get('competences/{competency}/activites/{activity}/edit', [AdminActivityController::class, 'edit'])->name('competences.activites.edit');
    Route::put('competences/{competency}/activites/{activity}', [AdminActivityController::class, 'update'])->name('competences.activites.update');
    Route::delete('competences/{competency}/activites/{activity}', [AdminActivityController::class, 'destroy'])->name('competences.activites.destroy');

    Route::get('points', [PointController::class, 'index'])->name('points.index');
    Route::post('points', [PointController::class, 'store'])->name('points.store');
    Route::post('points/actions', [PointController::class, 'storeBehavior'])->name('points.behaviors.store');
    Route::delete('points/actions/{behavior}', [PointController::class, 'destroyBehavior'])->name('points.behaviors.destroy');

    Route::get('corrections', [SubmissionController::class, 'index'])->name('corrections.index');
    Route::get('corrections/{submission}', [SubmissionController::class, 'edit'])->name('corrections.edit');
    Route::put('corrections/{submission}', [SubmissionController::class, 'update'])->name('corrections.update');

    Route::get('bulletins', [BulletinController::class, 'index'])->name('bulletins.index');
    Route::get('bulletins/{student}', [BulletinController::class, 'show'])->name('bulletins.show');
});

Route::middleware(['auth', 'student'])->prefix('eleve')->name('student.')->group(function () {
    Route::get('/', StudentDashboardController::class)->name('dashboard');
    Route::get('mes-points', StudentPointController::class)->name('points');
    Route::get('matieres', [StudentSubjectController::class, 'index'])->name('matieres.index');
    Route::get('matieres/{subject}', [StudentSubjectController::class, 'show'])->name('matieres.show');
    Route::get('activites/{activity}', [StudentActivityController::class, 'show'])->name('activites.show');
    Route::post('activites/{activity}/annotations', [StudentActivityController::class, 'saveAnnotations'])->name('activites.annotations');
    Route::post('activites/{activity}/soumettre', [StudentActivityController::class, 'submit'])->name('activites.submit');
    Route::get('activites/{activity}/resultat', [StudentActivityController::class, 'result'])->name('activites.resultat');
});
