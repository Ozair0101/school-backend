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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('grade_id')->constrained('grades')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('academic_year');
            $table->string('roll_no');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['student_id', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
