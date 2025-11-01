<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttemptAnswerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $attemptAnswers = AttemptAnswer::with(['attempt', 'question', 'choice', 'gradedBy'])->get();
        return $this->success($attemptAnswers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:student_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'choice_id' => 'nullable|exists:choices,id',
            'answer_text' => 'nullable|string',
            'uploaded_file' => 'nullable|string|max:255',
            'marks_awarded' => 'nullable|numeric|min:0',
            'auto_graded' => 'boolean',
            'graded_by' => 'nullable|exists:teachers,id',
            'graded_at' => 'nullable|date',
            'saved_at' => 'nullable|date',
        ]);

        // Check if answer already exists for this attempt and question
        $existing = AttemptAnswer::where('attempt_id', $validated['attempt_id'])
            ->where('question_id', $validated['question_id'])
            ->first();

        if ($existing) {
            return $this->error('An answer already exists for this attempt and question', 409);
        }

        $attemptAnswer = AttemptAnswer::create($validated);

        return $this->success($attemptAnswer, 'Attempt answer created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttemptAnswer $attemptAnswer): JsonResponse
    {
        $attemptAnswer->load(['attempt', 'question', 'choice', 'gradedBy']);
        return $this->success($attemptAnswer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttemptAnswer $attemptAnswer): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'sometimes|required|exists:student_attempts,id',
            'question_id' => 'sometimes|required|exists:questions,id',
            'choice_id' => 'nullable|exists:choices,id',
            'answer_text' => 'nullable|string',
            'uploaded_file' => 'nullable|string|max:255',
            'marks_awarded' => 'nullable|numeric|min:0',
            'auto_graded' => 'boolean',
            'graded_by' => 'nullable|exists:teachers,id',
            'graded_at' => 'nullable|date',
            'saved_at' => 'nullable|date',
        ]);

        // Check if answer already exists for this attempt and question (excluding current)
        if (isset($validated['attempt_id']) && isset($validated['question_id'])) {
            $existing = AttemptAnswer::where('attempt_id', $validated['attempt_id'])
                ->where('question_id', $validated['question_id'])
                ->where('id', '!=', $attemptAnswer->id)
                ->first();

            if ($existing) {
                return $this->error('An answer already exists for this attempt and question', 409);
            }
        }

        $attemptAnswer->update($validated);

        return $this->success($attemptAnswer, 'Attempt answer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttemptAnswer $attemptAnswer): JsonResponse
    {
        $attemptAnswer->delete();

        return $this->success(null, 'Attempt answer deleted successfully');
    }
}
