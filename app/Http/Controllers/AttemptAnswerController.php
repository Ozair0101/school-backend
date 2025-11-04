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
    public function index(Request $request): JsonResponse
    {
        $query = AttemptAnswer::with(['attempt', 'question', 'choice', 'gradedBy']);
        
        // Filter by attempt_id if provided
        if ($request->has('attempt_id')) {
            $query->where('attempt_id', $request->attempt_id);
        }
        
        // Filter by question_id if provided
        if ($request->has('question_id')) {
            $query->where('question_id', $request->question_id);
        }
        
        // Filter by graded status
        if ($request->has('graded')) {
            $graded = filter_var($request->graded, FILTER_VALIDATE_BOOLEAN);
            if ($graded) {
                $query->whereNotNull('marks_awarded');
            } else {
                $query->whereNull('marks_awarded');
            }
        }
        
        // Filter by auto_graded
        if ($request->has('auto_graded')) {
            $autoGraded = filter_var($request->auto_graded, FILTER_VALIDATE_BOOLEAN);
            $query->where('auto_graded', $autoGraded);
        }
        
        // Order and get results
        $attemptAnswers = $query->orderBy('saved_at', 'desc')->orderBy('created_at', 'desc')->get();
        
        // Transform to include graded_by_teacher
        $attemptAnswers = $attemptAnswers->map(function ($answer) {
            $answer->setAttribute('graded_by_teacher', $answer->gradedBy);
            return $answer;
        });
        
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
            'auto_graded' => 'nullable|boolean',
            'graded_by' => 'nullable|exists:teachers,id',
            'graded_at' => 'nullable|date',
            'saved_at' => 'nullable|date',
        ]);

        // Check if answer already exists for this attempt and question
        $existing = AttemptAnswer::where('attempt_id', $validated['attempt_id'])
            ->where('question_id', $validated['question_id'])
            ->first();

        if ($existing) {
            // Update existing instead of creating new (upsert behavior)
            $existing->update(array_merge($validated, [
                'saved_at' => $validated['saved_at'] ?? now(),
            ]));
            $existing->load(['attempt', 'question', 'choice', 'gradedBy']);
            $existing->setAttribute('graded_by_teacher', $existing->gradedBy);
            return $this->success($existing, 'Attempt answer updated successfully');
        }

        // Set defaults
        if (!isset($validated['auto_graded'])) {
            $validated['auto_graded'] = false;
        }
        if (!isset($validated['saved_at'])) {
            $validated['saved_at'] = now();
        }

        $attemptAnswer = AttemptAnswer::create($validated);
        $attemptAnswer->load(['attempt', 'question', 'choice', 'gradedBy']);
        $attemptAnswer->setAttribute('graded_by_teacher', $attemptAnswer->gradedBy);

        return $this->success($attemptAnswer, 'Attempt answer created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttemptAnswer $attemptAnswer): JsonResponse
    {
        $attemptAnswer->load(['attempt', 'question', 'choice', 'gradedBy']);
        $attemptAnswer->setAttribute('graded_by_teacher', $attemptAnswer->gradedBy);
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
            'auto_graded' => 'nullable|boolean',
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

        // Set graded_at if marks_awarded is being set and graded_at is not provided
        if (isset($validated['marks_awarded']) && !isset($validated['graded_at']) && !$attemptAnswer->graded_at) {
            $validated['graded_at'] = now();
        }

        // Update saved_at if answer content is being updated
        if (isset($validated['answer_text']) || isset($validated['choice_id']) || isset($validated['uploaded_file'])) {
            if (!isset($validated['saved_at'])) {
                $validated['saved_at'] = now();
            }
        }

        $attemptAnswer->update($validated);
        $attemptAnswer->load(['attempt', 'question', 'choice', 'gradedBy']);
        $attemptAnswer->setAttribute('graded_by_teacher', $attemptAnswer->gradedBy);

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
