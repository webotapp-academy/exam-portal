<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Question::with(['category', 'options']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where('question_text', 'like', "%{$search}%");
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.questions.index', compact('questions', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.questions.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        DB::transaction(function () use ($request, $data) {
            $imagePath = $request->hasFile('question_image')
                ? $request->file('question_image')->store('questions', 'public')
                : null;

            $question = Question::create([
                'category_id' => $data['category_id'] ?? null,
                'created_by' => $request->user()->id,
                'question_text' => $data['question_text'],
                'question_image' => $imagePath,
                'type' => $data['type'],
                'difficulty' => $data['difficulty'],
                'marks' => $data['marks'],
                'negative_marks' => $data['negative_marks'] ?? 0,
                'explanation' => $data['explanation'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $this->syncOptions($question, $data['options'], $data['correct_options']);
        });

        return redirect()->route('admin.questions.index')->with('success', 'Question added to question bank.');
    }

    public function edit(Question $question): View
    {
        $question->load('options');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $data = $this->validateQuestion($request);

        DB::transaction(function () use ($request, $question, $data) {
            $imagePath = $question->question_image;
            if ($request->hasFile('question_image')) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            $question->update([
                'category_id' => $data['category_id'] ?? null,
                'question_text' => $data['question_text'],
                'question_image' => $imagePath,
                'type' => $data['type'],
                'difficulty' => $data['difficulty'],
                'marks' => $data['marks'],
                'negative_marks' => $data['negative_marks'] ?? 0,
                'explanation' => $data['explanation'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $question->options()->delete();
            $this->syncOptions($question, $data['options'], $data['correct_options']);

            foreach ($question->exams as $exam) {
                $exam->recalculateTotalMarks();
            }
        });

        return redirect()->route('admin.questions.index')->with('success', 'Question updated.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }
        $exams = $question->exams;
        $question->delete();
        foreach ($exams as $exam) {
            $exam->recalculateTotalMarks();
        }
        return back()->with('success', 'Question deleted.');
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('csv_file')->getRealPath(), 'r');
        if (! $handle) {
            return back()->with('error', 'Unable to read CSV file.');
        }

        $header = fgetcsv($handle);
        if (! $header || count($header) < 11) {
            fclose($handle);
            return back()->with('error', 'Invalid CSV format. Download the sample template first.');
        }

        $imported = 0;
        DB::transaction(function () use ($handle, $request, &$imported) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 11 || empty(trim($row[1] ?? ''))) {
                    continue;
                }

                $categoryName = trim($row[0]);
                $category = $categoryName !== ''
                    ? Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['slug' => \Illuminate\Support\Str::slug($categoryName)]
                    )
                    : null;

                $type = in_array(trim($row[2]), ['single_choice', 'multiple_choice', 'true_false'], true)
                    ? trim($row[2])
                    : 'single_choice';

                $difficulty = in_array(trim($row[3]), ['easy', 'medium', 'hard'], true)
                    ? trim($row[3])
                    : 'medium';

                $question = Question::create([
                    'category_id' => $category?->id,
                    'created_by' => $request->user()->id,
                    'question_text' => trim($row[1]),
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'marks' => is_numeric($row[4]) ? (float) $row[4] : 1.00,
                    'negative_marks' => is_numeric($row[5]) ? (float) $row[5] : 0.00,
                    'explanation' => trim($row[11] ?? ''),
                    'is_active' => true,
                ]);

                $correctIndexes = array_map('intval', explode(',', trim($row[10])));
                for ($i = 1; $i <= 4; $i++) {
                    $optText = trim($row[5 + $i] ?? '');
                    if ($optText === '') {
                        continue;
                    }
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $optText,
                        'is_correct' => in_array($i, $correctIndexes, true),
                        'sort_order' => $i,
                    ]);
                }

                $imported++;
            }
        });

        fclose($handle);

        return back()->with('success', "Successfully imported {$imported} questions from CSV.");
    }

    public function downloadSampleCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'category', 'question_text', 'type', 'difficulty', 'marks', 'negative_marks',
                'option_1', 'option_2', 'option_3', 'option_4', 'correct_options', 'explanation',
            ]);
            fputcsv($out, [
                'Laravel & PHP',
                'What does PDO stand for in PHP?',
                'single_choice',
                'easy',
                '1',
                '0.25',
                'PHP Data Objects',
                'Private Data Output',
                'Public Database Object',
                'Persistent Data Option',
                '1',
                'PDO stands for PHP Data Objects, a lightweight interface for accessing databases in PHP.',
            ]);
            fclose($out);
        }, 'sample-questions-import.csv');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'question_text' => ['required', 'string'],
            'question_image' => ['nullable', 'image', 'max:2048'],
            'type' => ['required', 'in:single_choice,multiple_choice,true_false'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'marks' => ['required', 'numeric', 'min:0.25', 'max:100'],
            'negative_marks' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'explanation' => ['nullable', 'string'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:1000'],
            'correct_options' => ['required', 'array', 'min:1'],
            'correct_options.*' => ['integer'],
        ]);
    }

    private function syncOptions(Question $question, array $options, array $correctIndexes): void
    {
        $correctInt = array_map('intval', $correctIndexes);
        $order = 1;
        foreach ($options as $idx => $text) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => in_array((int) $idx, $correctInt, true),
                'sort_order' => $order++,
            ]);
        }
    }
}

