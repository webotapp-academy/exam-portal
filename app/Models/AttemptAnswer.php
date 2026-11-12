<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'selected_option_ids',
        'is_correct',
        'marks_awarded',
        'is_marked_for_review',
        'instant_revealed',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'selected_option_ids' => 'array',
            'is_correct' => 'boolean',
            'marks_awarded' => 'decimal:2',
            'is_marked_for_review' => 'boolean',
            'instant_revealed' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

