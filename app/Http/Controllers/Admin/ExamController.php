<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with('category')
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(12);

        return view('admin.exams.index', compact('exams'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.exams.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateExam($request);

        $exam = Exam::create([
            'category_id' => $data['category_id'],
            'created_by' => $request->user()->id,
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::random(5),
            'description' => $data['description'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'pass_percentage' => $data['pass_percentage'],
            'mode' => $data['mode'],
            'lock_after_instant_reveal' => $request->boolean('lock_after_instant_reveal', true),
            'allow_negative_marking' => $request->boolean('allow_negative_marking'),
            'shuffle_questions' => $request->boolean('shuffle_questions', true),
            'shuffle_options' => $request->boolean('shuffle_options', true),
            'max_attempts' => $data['max_attempts'] ?? null,
            'show_leaderboard' => $request->boolean('show_leaderboard', true),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.exams.questions', $exam)
            ->with('success', 'Exam created! Now assign questions from the Question Bank.');
    }

    public function edit(Exam $exam): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.exams.edit', compact('exam', 'categories'));
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $data = $this->validateExam($request);

        $exam->update([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'pass_percentage' => $data['pass_percentage'],
            'mode' => $data['mode'],
            'lock_after_instant_reveal' => $request->boolean('lock_after_instant_reveal'),
            'allow_negative_marking' => $request->boolean('allow_negative_marking'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'max_attempts' => $data['max_attempts'] ?? null,
            'show_leaderboard' => $request->boolean('show_leaderboard'),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $exam->delete();
        return back()->with('success', 'Exam deleted.');
    }

    public function manageQuestions(Request $request, Exam $exam): View
    {
        $exam->load('questions.category');
        $attachedIds = $exam->questions->pluck('id')->all();

        $availableQuery = Question::with('category')->where('is_active', true);
        if ($request->filled('category_id')) {
            $availableQuery->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('search')) {
            $availableQuery->where('question_text', 'like', '%' . $request->string('search')->trim() . '%');
        }

        $availableQuestions = $availableQuery->latest()->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.exams.questions', compact('exam', 'attachedIds', 'availableQuestions', 'categories'));
    }

    public function syncQuestions(Request $request, Exam $exam): RedirectResponse
    {
        $data = $request->validate([
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['exists:questions,id'],
        ]);

        $syncData = [];
        foreach (array_values($data['question_ids'] ?? []) as $idx => $qId) {
            $syncData[$qId] = ['sort_order' => $idx + 1];
        }

        $exam->questions()->sync($syncData);
        $exam->recalculateTotalMarks();

        return back()->with('success', 'Exam questions updated (' . count($syncData) . ' questions, Total Marks: ' . $exam->total_marks . ').');
    }

    public function results(Exam $exam): View
    {
        $attempts = ExamAttempt::with('user')
            ->where('exam_id', $exam->id)
            ->latest('id')
            ->paginate(20);

        return view('admin.exams.results', compact('exam', 'attempts'));
    }

    public function exportResults(Exam $exam): StreamedResponse
    {
        $attempts = ExamAttempt::with('user')
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['completed', 'timed_out'])
            ->orderByDesc('score')
            ->get();

        return response()->streamDownload(function () use ($attempts) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Attempt ID', 'Student', 'Email', 'Score', 'Total Marks', 'Percentage', 'Passed', 'Correct', 'Wrong', 'Unanswered', 'Tab Switches', 'Submitted At']);
            foreach ($attempts as $a) {
                fputcsv($out, [
                    $a->id,
                    $a->user->name,
                    $a->user->email,
                    $a->score,
                    $a->total_marks,
                    $a->percentage . '%',
                    $a->is_passed ? 'PASS' : 'FAIL',
                    $a->correct_count,
                    $a->wrong_count,
                    $a->unanswered_count,
                    $a->tab_switch_count,
                    $a->submitted_at?->toDateTimeString(),
                ]);
            }
            fclose($out);
        }, "exam-{$exam->slug}-results.csv");
    }

    private function validateExam(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:600'],
            'pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'mode' => ['required', 'in:instant_answer,standard'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }
}

