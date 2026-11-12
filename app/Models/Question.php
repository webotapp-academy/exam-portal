<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'created_by',
        'question_text',
        'question_image',
        'type',
        'difficulty',
        'marks',
        'negative_marks',
        'explanation',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'negative_marks' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('sort_order');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question');
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(QuestionReport::class);
    }

    public function correctOptionIds(): array
    {
        return $this->options()
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();
    }

    public function evaluateSelection(array $selectedOptionIds, bool $allowNegativeMarking = false): array
    {
        $selected = collect($selectedOptionIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $correctIds = $this->correctOptionIds();

        if (empty($selected)) {
            return [
                'is_correct' => null,
                'marks_awarded' => 0.00,
                'correct_option_ids' => $correctIds,
            ];
        }

        $isCorrect = $selected === $correctIds;
        $marks = $isCorrect
            ? (float) $this->marks
            : ($allowNegativeMarking ? -(float) $this->negative_marks : 0.00);

        return [
            'is_correct' => $isCorrect,
            'marks_awarded' => round($marks, 2),
            'correct_option_ids' => $correctIds,
        ];
    }
}

