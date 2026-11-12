<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->json('question_order');
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'timed_out'])->default('in_progress');
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('is_passed')->default(false);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('unanswered_count')->default(0);
            $table->unsignedInteger('tab_switch_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};

