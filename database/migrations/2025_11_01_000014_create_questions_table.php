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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('teachers')->onDelete('cascade');
            $table->enum('type', ['mcq', 'tf', 'numeric', 'short', 'essay', 'file']);
            $table->text('prompt');
            $table->decimal('default_marks', 8, 2);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
