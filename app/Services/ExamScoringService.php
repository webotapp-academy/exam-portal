<?php

namespace App\Services;

use App\Models\AttemptAnswer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExamScoringService
{
    public function startOrResumeAttempt(Exam $exam, User $user, ?string $ipAddress = null): ExamAttempt
    {
        $existing = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        if ($existing) {
            if ($existing->isExpired()) {
                $this->finalizeAttempt($existing, timedOut: true);
            } else {
                return $existing;
            }
        }

        $completedCount = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->count();

        $questionIds = $exam->questions()
            ->where('questions.is_active', true)
            ->pluck('questions.id')
            ->all();

        if ($exam->shuffle_questions) {
            shuffle($questionIds);
        }

        $totalMarks = (float) Question::whereIn('id', $questionIds)->sum('marks');
        $expiresAt = $exam->duration_minutes > 0
            ? now()->addMinutes($exam->duration_minutes)
            : null;

        return ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $user->id,
            'attempt_number' => $completedCount + 1,
            'question_order' => $questionIds,
            'started_at' => now(),
            'expires_at' => $expiresAt,
            'status' => 'in_progress',
            'total_marks' => $totalMarks,
            'unanswered_count' => count($questionIds),
            'ip_address' => $ipAddress,
        ]);
    }

    public function saveAnswer(
        ExamAttempt $attempt,
        Question $question,
        array $selectedOptionIds,
        bool $checkInstant = false
    ): array {
        $exam = $attempt->exam;

        $validOptionIds = $question->options()->pluck('id')->all();
        $cleanSelected = collect($selectedOptionIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $validOptionIds, true))
            ->unique()
            ->values()
            ->all();

        if ($question->type !== 'multiple_choice' && count($cleanSelected) > 1) {
            $cleanSelected = [reset($cleanSelected)];
        }

        $answer = AttemptAnswer::firstOrNew([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $question->id,
        ]);

        if ($exam->isInstantAnswerMode() && $exam->lock_after_instant_reveal && $answer->instant_revealed) {
            $eval = $question->evaluateSelection($answer->selected_option_ids ?? [], $exam->allow_negative_marking);

            return [
                'locked' => true,
                'mode' => 'instant_answer',
                'is_correct' => $answer->is_correct,
                'correct_option_ids' => $eval['correct_option_ids'],
                'explanation' => $question->explanation,
                'marks_awarded' => (float) $answer->marks_awarded,
                'selected_option_ids' => $answer->selected_option_ids ?? [],
            ];
        }

        $answer->selected_option_ids = $cleanSelected;
        $answer->answered_at = empty($cleanSelected) ? null : now();

        if ($exam->isInstantAnswerMode() && $checkInstant && ! empty($cleanSelected)) {
            $eval = $question->evaluateSelection($cleanSelected, $exam->allow_negative_marking);
            $answer->is_correct = $eval['is_correct'];
            $answer->marks_awarded = $eval['marks_awarded'];
            $answer->instant_revealed = true;
            $answer->save();

            return [
                'locked' => $exam->lock_after_instant_reveal,
                'mode' => 'instant_answer',
                'is_correct' => $eval['is_correct'],
                'correct_option_ids' => $eval['correct_option_ids'],
                'explanation' => $question->explanation,
                'marks_awarded' => $eval['marks_awarded'],
                'selected_option_ids' => $cleanSelected,
            ];
        }

        $answer->save();

        return [
            'locked' => false,
            'mode' => $exam->mode,
            'saved' => true,
            'selected_option_ids' => $cleanSelected,
        ];
    }

    public function finalizeAttempt(ExamAttempt $attempt, bool $timedOut = false): ExamAttempt
    {
        if ($attempt->status !== 'in_progress') {
            return $attempt;
        }

        return DB::transaction(function () use ($attempt, $timedOut) {
            $exam = $attempt->exam;
            $questionIds = $attempt->question_order ?? [];
            $questions = Question::with('options')->whereIn('id', $questionIds)->get()->keyBy('id');
            $existingAnswers = $attempt->answers()->get()->keyBy('question_id');

            $score = 0.0;
            $totalMarks = 0.0;
            $correctCount = 0;
            $wrongCount = 0;
            $unansweredCount = 0;

            foreach ($questionIds as $qId) {
                $question = $questions->get($qId);
                if (! $question) {
                    continue;
                }

                $totalMarks += (float) $question->marks;
                $answer = $existingAnswers->get($qId);
                $selected = $answer?->selected_option_ids ?? [];

                if (empty($selected)) {
                    $unansweredCount++;
                    if ($answer) {
                        $answer->update(['is_correct' => null, 'marks_awarded' => 0]);
                    }
                    continue;
                }

                $eval = $question->evaluateSelection($selected, $exam->allow_negative_marking);
                $score += $eval['marks_awarded'];

                if ($eval['is_correct']) {
                    $correctCount++;
                } else {
                    $wrongCount++;
                }

                $answer->update([
                    'is_correct' => $eval['is_correct'],
                    'marks_awarded' => $eval['marks_awarded'],
                ]);
            }

            $score = max(0.0, round($score, 2));
            $percentage = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0.00;
            $isPassed = $percentage >= (float) $exam->pass_percentage;

            $attempt->update([
                'status' => $timedOut ? 'timed_out' : 'completed',
                'submitted_at' => $timedOut && $attempt->expires_at ? $attempt->expires_at : now(),
                'score' => $score,
                'total_marks' => round($totalMarks, 2),
                'percentage' => $percentage,
                'is_passed' => $isPassed,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
                'unanswered_count' => $unansweredCount,
            ]);

            return $attempt->fresh();
        });
    }
}

