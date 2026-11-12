<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'students' => User::where('role', 'student')->count(),
            'exams' => Exam::count(),
            'published_exams' => Exam::where('is_published', true)->count(),
            'questions' => Question::count(),
            'categories' => Category::count(),
            'attempts' => ExamAttempt::whereIn('status', ['completed', 'timed_out'])->count(),
            'pass_rate' => round((float) (ExamAttempt::whereIn('status', ['completed', 'timed_out'])->avg('is_passed') * 100), 1),
            'pending_reports' => QuestionReport::where('status', 'pending')->count(),
        ];

        $recentAttempts = ExamAttempt::with(['user', 'exam'])
            ->whereIn('status', ['completed', 'timed_out'])
            ->latest('submitted_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAttempts'));
    }
}

