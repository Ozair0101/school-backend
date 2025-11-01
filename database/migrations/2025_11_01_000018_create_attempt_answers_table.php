<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('student_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('choice_id')->nullable()->constrained('choices')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->string('uploaded_file')->nullable();
            $table->decimal('marks_awarded', 8, 2)->nullable();
            $table->boolean('auto_graded')->default(false);
            $table->foreignId('graded_by')->nullable()->constrained('teachers')->onDelete('cascade');
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);

            // Index for performance
            $table->index(['attempt_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};
