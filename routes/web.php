<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\BookmarkController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Models\Exam;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredExams = Exam::with('category')
        ->withCount('questions')
        ->where('is_published', true)
        ->latest()
        ->take(6)
        ->get();

    return view('welcome', compact('featuredExams'));
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student Exam Portal
    Route::get('/exams/{exam:slug}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam:slug}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('/exams/{exam:slug}/leaderboard', [StudentExamController::class, 'leaderboard'])->name('exams.leaderboard');

    Route::get('/attempts/{attempt}', [StudentExamController::class, 'take'])->name('attempts.take');
    Route::post('/attempts/{attempt}/answer', [StudentExamController::class, 'saveAnswer'])
        ->middleware('throttle:120,1')
        ->name('attempts.answer');
    Route::post('/attempts/{attempt}/flag', [StudentExamController::class, 'toggleFlag'])->name('attempts.flag');
    Route::post('/attempts/{attempt}/tab-switch', [StudentExamController::class, 'logTabSwitch'])->name('attempts.tab-switch');
    Route::post('/attempts/{attempt}/submit', [StudentExamController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/result', [StudentExamController::class, 'result'])->name('attempts.result');
    Route::get('/attempts/{attempt}/certificate', [StudentExamController::class, 'certificate'])->name('attempts.certificate');

    // Bookmarks & Question Reporting
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/questions/{question}/bookmark', [BookmarkController::class, 'toggle'])->name('questions.bookmark');
    Route::post('/questions/{question}/report', [BookmarkController::class, 'report'])->name('questions.report');

    // Admin & Examiner Panel
    Route::middleware(['role:admin,examiner'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);

            Route::get('questions/sample-csv', [AdminQuestionController::class, 'downloadSampleCsv'])->name('questions.sample-csv');
            Route::post('questions/import', [AdminQuestionController::class, 'importCsv'])->name('questions.import');
            Route::resource('questions', AdminQuestionController::class)->except(['show']);

            Route::resource('exams', AdminExamController::class)->except(['show']);
            Route::get('exams/{exam}/questions', [AdminExamController::class, 'manageQuestions'])->name('exams.questions');
            Route::post('exams/{exam}/questions', [AdminExamController::class, 'syncQuestions'])->name('exams.questions.sync');
            Route::get('exams/{exam}/results', [AdminExamController::class, 'results'])->name('exams.results');
            Route::get('exams/{exam}/results/export', [AdminExamController::class, 'exportResults'])->name('exams.results.export');

            Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
            Route::patch('reports/{report}', [AdminReportController::class, 'updateStatus'])->name('reports.update');

            Route::middleware('role:admin')->group(function () {
                Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
                Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            });
        });
});

require __DIR__.'/auth.php';
