<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'created_by',
        'title',
        'slug',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'pass_percentage',
        'mode',
        'lock_after_instant_reveal',
        'allow_negative_marking',
        'shuffle_questions',
        'shuffle_options',
        'max_attempts',
        'show_leaderboard',
        'starts_at',
        'ends_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'total_marks' => 'decimal:2',
            'pass_percentage' => 'decimal:2',
            'lock_after_instant_reveal' => 'boolean',
            'allow_negative_marking' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'max_attempts' => 'integer',
            'show_leaderboard' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Exam $exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title) . '-' . Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')
            ->withPivot('sort_order')
            ->orderBy('exam_question.sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isInstantAnswerMode(): bool
    {
        return $this->mode === 'instant_answer';
    }

    public function isCurrentlyAvailable(): bool
    {
        if (! $this->is_published) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        return true;
    }

    public function recalculateTotalMarks(): void
    {
        $total = $this->questions()->sum('questions.marks');
        $this->update(['total_marks' => $total]);
    }
}

