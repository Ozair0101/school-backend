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
        Schema::create('monthly_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('grade_id')->constrained('grades')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->tinyInteger('month'); // 1..12
            $table->integer('year');
            $table->date('exam_date');
            $table->text('description')->nullable();

            // Online exam settings
            $table->boolean('online_enabled')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('allow_multiple_attempts')->default(false);
            $table->integer('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_choices')->default(false);
            $table->decimal('negative_marking', 5, 2)->default(0);
            $table->decimal('passing_percentage', 5, 2)->default(0);
            $table->string('access_code')->nullable();
            $table->boolean('random_pool')->default(false);
            $table->boolean('show_answers_after')->default(false);
            $table->boolean('auto_publish_results')->default(false);

            $table->timestamps();

            $table->unique(['grade_id', 'section_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_exams');
    }
};
