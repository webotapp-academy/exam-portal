<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->json('selected_option_ids')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 6, 2)->default(0);
            $table->boolean('is_marked_for_review')->default(false);
            $table->boolean('instant_revealed')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};

