<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPortalTest extends TestCase
{
    use RefreshDatabase;

    private function createExamWithQuestions(string $mode = 'instant_answer', bool $negative = false): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'PHP', 'slug' => 'php']);

        $exam = Exam::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Sample MCQ Exam',
            'slug' => 'sample-mcq-' . uniqid(),
            'duration_minutes' => 15,
            'pass_percentage' => 50,
            'mode' => $mode,
            'lock_after_instant_reveal' => true,
            'allow_negative_marking' => $negative,
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'is_published' => true,
        ]);

        $q1 = Question::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'question_text' => 'What is 2 + 2?',
            'type' => 'single_choice',
            'difficulty' => 'easy',
            'marks' => 2,
            'negative_marks' => 0.5,
            'explanation' => 'Basic arithmetic: 2 + 2 = 4.',
        ]);
        $q1Correct = Option::create(['question_id' => $q1->id, 'option_text' => '4', 'is_correct' => true, 'sort_order' => 1]);
        $q1Wrong = Option::create(['question_id' => $q1->id, 'option_text' => '5', 'is_correct' => false, 'sort_order' => 2]);

        $q2 = Question::create([
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'question_text' => 'Select even numbers.',
            'type' => 'multiple_choice',
            'difficulty' => 'medium',
            'marks' => 2,
            'negative_marks' => 0.5,
            'explanation' => '2 and 4 are even.',
        ]);
        $q2Opt1 = Option::create(['question_id' => $q2->id, 'option_text' => '2', 'is_correct' => true, 'sort_order' => 1]);
        $q2Opt2 = Option::create(['question_id' => $q2->id, 'option_text' => '3', 'is_correct' => false, 'sort_order' => 2]);
        $q2Opt3 = Option::create(['question_id' => $q2->id, 'option_text' => '4', 'is_correct' => true, 'sort_order' => 3]);

        $exam->questions()->attach([$q1->id => ['sort_order' => 1], $q2->id => ['sort_order' => 2]]);
        $exam->recalculateTotalMarks();

        return compact('admin', 'exam', 'q1', 'q1Correct', 'q1Wrong', 'q2', 'q2Opt1', 'q2Opt2', 'q2Opt3');
    }

    public function test_student_gets_instant_answer_and_explanation_and_is_locked_after_reveal(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $data = $this->createExamWithQuestions('instant_answer');

        $this->actingAs($student)
            ->post(route('exams.start', $data['exam']))
            ->assertRedirect();

        $attempt = ExamAttempt::firstOrFail();

        // Check wrong answer first -> receives instant feedback & explanation
        $response = $this->actingAs($student)->postJson(route('attempts.answer', $attempt), [
            'question_id' => $data['q1']->id,
            'selected_option_ids' => [$data['q1Wrong']->id],
            'check_instant' => true,
        ]);

        $response->assertOk()
            ->assertJson([
                'mode' => 'instant_answer',
                'is_correct' => false,
                'correct_option_ids' => [$data['q1Correct']->id],
                'explanation' => 'Basic arithmetic: 2 + 2 = 4.',
                'locked' => true,
            ]);

        // Attempt to change answer after reveal -> remains locked with original wrong state
        $retry = $this->actingAs($student)->postJson(route('attempts.answer', $attempt), [
            'question_id' => $data['q1']->id,
            'selected_option_ids' => [$data['q1Correct']->id],
            'check_instant' => true,
        ]);

        $retry->assertOk()
            ->assertJson([
                'locked' => true,
                'is_correct' => false,
            ]);
    }

    public function test_standard_exam_hides_correct_answer_until_submit_and_applies_negative_marking(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $data = $this->createExamWithQuestions('standard', negative: true);

        $this->actingAs($student)->post(route('exams.start', $data['exam']));
        $attempt = ExamAttempt::firstOrFail();

        // Wrong answer on Q1 (-0.5)
        $res1 = $this->actingAs($student)->postJson(route('attempts.answer', $attempt), [
            'question_id' => $data['q1']->id,
            'selected_option_ids' => [$data['q1Wrong']->id],
            'check_instant' => true,
        ]);

        $res1->assertOk()
            ->assertJson(['mode' => 'standard', 'saved' => true])
            ->assertJsonMissing(['correct_option_ids', 'explanation']);

        // Correct multi-choice answer on Q2 (+2.0)
        $this->actingAs($student)->postJson(route('attempts.answer', $attempt), [
            'question_id' => $data['q2']->id,
            'selected_option_ids' => [$data['q2Opt1']->id, $data['q2Opt3']->id],
        ])->assertOk();

        // Submit exam
        $this->actingAs($student)
            ->post(route('attempts.submit', $attempt))
            ->assertRedirect(route('attempts.result', $attempt));

        $attempt->refresh();
        $this->assertEquals('completed', $attempt->status);
        $this->assertEquals(1.50, (float) $attempt->score);
        $this->assertEquals(4.00, (float) $attempt->total_marks);
        $this->assertEquals(37.50, (float) $attempt->percentage);
        $this->assertFalse($attempt->is_passed);

        // PDF scorecard & result page load cleanly
        $this->actingAs($student)->get(route('attempts.result', $attempt))->assertOk();
        $this->actingAs($student)->get(route('attempts.certificate', $attempt))->assertOk();
    }

    public function test_role_based_access_control_on_admin_routes(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_student_can_toggle_bookmark(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $data = $this->createExamWithQuestions();

        $this->actingAs($student)
            ->postJson(route('questions.bookmark', $data['q1']))
            ->assertOk()
            ->assertJson(['bookmarked' => true]);

        $this->actingAs($student)
            ->postJson(route('questions.bookmark', $data['q1']))
            ->assertOk()
            ->assertJson(['bookmarked' => false]);
    }
}

