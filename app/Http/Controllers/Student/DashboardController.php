<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $categoryId = $request->integer('category');
        $mode = $request->string('mode')->toString();
        $search = $request->string('search')->trim()->toString();

        $examsQuery = Exam::with(['category'])
            ->withCount('questions')
            ->where('is_published', true);

        if ($categoryId) {
            $examsQuery->where('category_id', $categoryId);
        }
        if (in_array($mode, ['instant_answer', 'standard'], true)) {
            $examsQuery->where('mode', $mode);
        }
        if ($search !== '') {
            $examsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exams = $examsQuery->latest()->paginate(9)->withQueryString();

        $inProgressAttempts = ExamAttempt::with('exam.category')
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->get();

        $recentAttempts = ExamAttempt::with('exam')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'timed_out'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $stats = [
            'completed' => ExamAttempt::where('user_id', $user->id)->whereIn('status', ['completed', 'timed_out'])->count(),
            'passed' => ExamAttempt::where('user_id', $user->id)->where('is_passed', true)->count(),
            'avg_percentage' => round((float) ExamAttempt::where('user_id', $user->id)->whereIn('status', ['completed', 'timed_out'])->avg('percentage'), 1),
            'bookmarks' => $user->bookmarks()->count(),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('student.dashboard', compact(
            'exams',
            'inProgressAttempts',
            'recentAttempts',
            'stats',
            'categories',
            'categoryId',
            'mode',
            'search'
        ));
    }
}

