<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->longText('question_text');
            $table->string('question_image')->nullable();
            $table->enum('type', ['single_choice', 'multiple_choice', 'true_false'])->default('single_choice');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->decimal('marks', 6, 2)->default(1.00);
            $table->decimal('negative_marks', 6, 2)->default(0.00);
            $table->longText('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

