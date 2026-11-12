<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttemptAnswer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Services\ExamScoringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(private readonly ExamScoringService $scoringService)
    {
    }

    public function show(Request $request, Exam $exam): View
    {
        if (! $exam->is_published && ! $request->user()->isExaminer()) {
            abort(404);
        }

        $exam->load('category')->loadCount(['questions' => fn ($q) => $q->where('is_active', true)]);

        $userAttempts = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->get();

        $activeAttempt = $userAttempts->firstWhere('status', 'in_progress');
        $attemptsUsed = $userAttempts->count();
        $canStart = $exam->isCurrentlyAvailable()
            && $exam->questions_count > 0
            && ($exam->max_attempts === null || $attemptsUsed < $exam->max_attempts || $activeAttempt !== null);

        return view('student.exams.show', compact('exam', 'userAttempts', 'activeAttempt', 'canStart'));
    }

    public function start(Request $request, Exam $exam): RedirectResponse
    {
        if (! $exam->isCurrentlyAvailable()) {
            return back()->with('error', 'This exam is not currently available.');
        }

        if ($exam->questions()->where('questions.is_active', true)->count() === 0) {
            return back()->with('error', 'This exam has no active questions yet.');
        }

        $user = $request->user();

        $active = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if (! $active && $exam->max_attempts !== null) {
            $used = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->count();
            if ($used >= $exam->max_attempts) {
                return back()->with('error', 'You have reached the maximum number of attempts for this exam.');
            }
        }

        $attempt = $this->scoringService->startOrResumeAttempt($exam, $user, $request->ip());

        return redirect()->route('attempts.take', $attempt);
    }

    public function take(Request $request, ExamAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('attempts.result', $attempt);
        }

        if ($attempt->isExpired()) {
            $this->scoringService->finalizeAttempt($attempt, timedOut: true);
            return redirect()->route('attempts.result', $attempt)
                ->with('info', 'Time expired! Your exam was automatically submitted.');
        }

        $exam = $attempt->exam()->with('category')->firstOrFail();
        $questionIds = $attempt->question_order ?? [];

        $questionsMap = Question::with(['options' => fn ($q) => $q->orderBy('sort_order')])
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $answersMap = $attempt->answers()->get()->keyBy('question_id');
        $bookmarkedIds = $request->user()->bookmarks()->pluck('question_id')->all();

        $questionsPayload = [];
        foreach ($questionIds as $index => $qId) {
            $question = $questionsMap->get($qId);
            if (! $question) {
                continue;
            }

            $options = $question->options;
            if ($exam->shuffle_options) {
                $options = $options->shuffle(crc32($attempt->id . '-' . $question->id));
            }

            $existingAnswer = $answersMap->get($question->id);
            $revealed = $exam->isInstantAnswerMode() && ($existingAnswer?->instant_revealed ?? false);

            $questionsPayload[] = [
                'index' => $index,
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_image' => $question->question_image ? asset('storage/' . $question->question_image) : null,
                'type' => $question->type,
                'difficulty' => $question->difficulty,
                'marks' => (float) $question->marks,
                'negative_marks' => $exam->allow_negative_marking ? (float) $question->negative_marks : 0,
                'selected_option_ids' => $existingAnswer?->selected_option_ids ?? [],
                'is_marked_for_review' => (bool) ($existingAnswer?->is_marked_for_review ?? false),
                'instant_revealed' => $revealed,
                'is_correct' => $revealed ? $existingAnswer?->is_correct : null,
                'marks_awarded' => $revealed ? (float) ($existingAnswer?->marks_awarded ?? 0) : null,
                'correct_option_ids' => $revealed ? $question->correctOptionIds() : [],
                'explanation' => $revealed ? $question->explanation : null,
                'is_bookmarked' => in_array($question->id, $bookmarkedIds, true),
                'options' => $options->values()->map(fn ($opt) => [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'option_image' => $opt->option_image ? asset('storage/' . $opt->option_image) : null,
                ])->all(),
            ];
        }

        $remainingSeconds = $attempt->remainingSeconds();

        return view('student.exams.take', compact('attempt', 'exam', 'questionsPayload', 'remainingSeconds'));
    }

    public function saveAnswer(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Attempt already submitted.'], 422);
        }

        if ($attempt->isExpired()) {
            $this->scoringService->finalizeAttempt($attempt, timedOut: true);
            return response()->json(['expired' => true, 'redirect' => route('attempts.result', $attempt)], 422);
        }

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'selected_option_ids' => ['nullable', 'array'],
            'selected_option_ids.*' => ['integer'],
            'check_instant' => ['boolean'],
        ]);

        if (! in_array((int) $data['question_id'], $attempt->question_order ?? [], true)) {
            return response()->json(['error' => 'Question does not belong to this attempt.'], 403);
        }

        $question = Question::with('options')->findOrFail($data['question_id']);

        $result = $this->scoringService->saveAnswer(
            $attempt,
            $question,
            $data['selected_option_ids'] ?? [],
            (bool) ($data['check_instant'] ?? false)
        );

        return response()->json($result);
    }

    public function toggleFlag(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Attempt already closed.'], 422);
        }

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
        ]);

        if (! in_array((int) $data['question_id'], $attempt->question_order ?? [], true)) {
            return response()->json(['error' => 'Invalid question.'], 403);
        }

        $answer = AttemptAnswer::firstOrNew([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $data['question_id'],
        ]);
        $answer->is_marked_for_review = ! $answer->is_marked_for_review;
        $answer->save();

        return response()->json(['is_marked_for_review' => $answer->is_marked_for_review]);
    }

    public function logTabSwitch(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status === 'in_progress') {
            $attempt->increment('tab_switch_count');
        }

        return response()->json(['tab_switch_count' => $attempt->tab_switch_count]);
    }

    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorizeAttempt($request, $attempt);

        if ($attempt->status === 'in_progress') {
            $this->scoringService->finalizeAttempt($attempt, timedOut: $attempt->isExpired());
        }

        return redirect()->route('attempts.result', $attempt)
            ->with('success', 'Exam submitted successfully!');
    }

    public function result(Request $request, ExamAttempt $attempt): View|RedirectResponse
    {
        if ($attempt->user_id !== $request->user()->id && ! $request->user()->isExaminer()) {
            abort(403);
        }

        if ($attempt->status === 'in_progress') {
            if ($attempt->isExpired()) {
                $this->scoringService->finalizeAttempt($attempt, timedOut: true);
            } else {
                return redirect()->route('attempts.take', $attempt);
            }
        }

        $attempt->load(['exam.category', 'user']);
        $questionIds = $attempt->question_order ?? [];

        $questions = Question::with(['options' => fn ($q) => $q->orderBy('sort_order')])
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        $answers = $attempt->answers()->get()->keyBy('question_id');
        $bookmarkedIds = $request->user()->bookmarks()->pluck('question_id')->all();

        $reviewItems = [];
        foreach ($questionIds as $idx => $qId) {
            $q = $questions->get($qId);
            if (! $q) {
                continue;
            }
            $ans = $answers->get($qId);
            $reviewItems[] = [
                'number' => $idx + 1,
                'question' => $q,
                'answer' => $ans,
                'selected_ids' => $ans?->selected_option_ids ?? [],
                'correct_ids' => $q->correctOptionIds(),
                'is_bookmarked' => in_array($q->id, $bookmarkedIds, true),
            ];
        }

        return view('student.exams.result', compact('attempt', 'reviewItems'));
    }

    public function certificate(Request $request, ExamAttempt $attempt): Response
    {
        if ($attempt->user_id !== $request->user()->id && ! $request->user()->isExaminer()) {
            abort(403);
        }

        if ($attempt->status === 'in_progress') {
            abort(403, 'Complete the exam first.');
        }

        $attempt->load(['exam.category', 'user']);

        $pdf = Pdf::loadView('student.exams.certificate-pdf', compact('attempt'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("scorecard-{$attempt->verificationCode()}.pdf");
    }

    public function leaderboard(Exam $exam): View
    {
        if (! $exam->show_leaderboard && ! auth()->user()->isExaminer()) {
            abort(403, 'Leaderboard is disabled for this exam.');
        }

        $topAttempts = ExamAttempt::with('user')
            ->where('exam_id', $exam->id)
            ->whereIn('status', ['completed', 'timed_out'])
            ->select('exam_attempts.*')
            ->addSelect(DB::raw('TIMESTAMPDIFF(SECOND, started_at, submitted_at) as duration_seconds'))
            ->orderByDesc('score')
            ->orderBy('duration_seconds')
            ->take(25)
            ->get();

        return view('student.exams.leaderboard', compact('exam', 'topAttempts'));
    }

    private function authorizeAttempt(Request $request, ExamAttempt $attempt): void
    {
        if ($attempt->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}

