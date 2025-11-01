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
        Schema::create('proctoring_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('student_attempts')->onDelete('cascade');
            $table->string('event_type'); // tab_switch, face_lost, camera_off, screenshot, high_noise, etc.
            $table->timestamp('event_time');
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proctoring_events');
    }
};
