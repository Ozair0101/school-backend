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
        Schema::create('student_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_exam_id')->constrained('monthly_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'grading', 'graded', 'abandoned'])->default('in_progress');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('percent', 5, 2)->nullable();
            $table->string('ip_address')->nullable();
            $table->text('device_info')->nullable();
            $table->string('attempt_token')->unique();
            $table->timestamps();

            // Indexes for monitoring
            $table->index(['student_id']);
            $table->index(['monthly_exam_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attempts');
    }
};
