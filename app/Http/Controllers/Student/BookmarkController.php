<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Question;
use App\Models\QuestionReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(Request $request): View
    {
        $bookmarks = Bookmark::with(['question.options', 'question.category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12);

        return view('student.bookmarks.index', compact('bookmarks'));
    }

    public function toggle(Request $request, Question $question): JsonResponse|RedirectResponse
    {
        $existing = Bookmark::where('user_id', $request->user()->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => $request->user()->id,
                'question_id' => $question->id,
            ]);
            $bookmarked = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['bookmarked' => $bookmarked]);
        }

        return back()->with('success', $bookmarked ? 'Question bookmarked.' : 'Bookmark removed.');
    }

    public function report(Request $request, Question $question): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:150'],
            'details' => ['nullable', 'string', 'max:1000'],
        ]);

        QuestionReport::create([
            'user_id' => $request->user()->id,
            'question_id' => $question->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['reported' => true, 'message' => 'Thank you! The question has been flagged for review.']);
        }

        return back()->with('success', 'Question reported for review.');
    }
}

