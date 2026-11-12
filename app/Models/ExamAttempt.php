<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'attempt_number',
        'question_order',
        'started_at',
        'expires_at',
        'submitted_at',
        'status',
        'score',
        'total_marks',
        'percentage',
        'is_passed',
        'correct_count',
        'wrong_count',
        'unanswered_count',
        'tab_switch_count',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'question_order' => 'array',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'is_passed' => 'boolean',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    public function remainingSeconds(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }
        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }

    public function durationFormatted(): string
    {
        $end = $this->submitted_at ?? now();
        $seconds = max(0, (int) $this->started_at->diffInSeconds($end));
        $mins = intdiv($seconds, 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d', $mins, $secs);
    }

    public function verificationCode(): string
    {
        return 'EXM-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($this->id . '|' . $this->user_id . '|' . $this->started_at), 0, 6));
    }
}

