<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->unsignedInteger('duration_minutes')->default(30); // 0 = untimed
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('pass_percentage', 5, 2)->default(40.00);
            $table->enum('mode', ['instant_answer', 'standard'])->default('instant_answer');
            $table->boolean('lock_after_instant_reveal')->default(true);
            $table->boolean('allow_negative_marking')->default(false);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_options')->default(true);
            $table->unsignedInteger('max_attempts')->nullable();
            $table->boolean('show_leaderboard')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

