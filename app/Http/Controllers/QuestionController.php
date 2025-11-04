<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with(['bank', 'author', 'choices']);
        
        // Filter by bank_id if provided
        if ($request->has('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        
        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        $questions = $query->get();
        return $this->success($questions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:question_banks,id',
            'author_id' => 'required|exists:teachers,id',
            'type' => 'required|in:mcq,tf,numeric,short,essay,file',
            'prompt' => 'required|string',
            'default_marks' => 'required|numeric|min:0',
            'metadata' => 'nullable|array',
            'choices' => 'nullable|array',
            'choices.*.choice_text' => 'required|string',
            'choices.*.is_correct' => 'boolean',
            'choices.*.position' => 'nullable|integer',
        ]);

        // Validate choices based on question type
        if (in_array($validated['type'], ['mcq', 'tf']) && empty($validated['choices'])) {
            return $this->error('Choices are required for MCQ and True/False questions', 422);
        }

        // Validate at least one correct answer for MCQ
        if ($validated['type'] === 'mcq' && isset($validated['choices'])) {
            $hasCorrect = collect($validated['choices'])->contains('is_correct', true);
            if (!$hasCorrect) {
                return $this->error('At least one correct answer is required for MCQ questions', 422);
            }
        }

        // Validate exactly one correct answer for True/False
        if ($validated['type'] === 'tf' && isset($validated['choices'])) {
            $correctCount = collect($validated['choices'])->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                return $this->error('Exactly one correct answer is required for True/False questions', 422);
            }
        }

        // Extract choices from validated data
        $choices = $validated['choices'] ?? [];
        unset($validated['choices']);

        $question = Question::create($validated);

        // Create choices if provided
        if (!empty($choices)) {
            foreach ($choices as $index => $choice) {
                $question->choices()->create([
                    'choice_text' => $choice['choice_text'],
                    'is_correct' => $choice['is_correct'] ?? false,
                    'position' => $choice['position'] ?? ($index + 1),
                ]);
            }
        }

        $question->load(['bank', 'author', 'choices']);

        return $this->success($question, 'Question created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question): JsonResponse
    {
        $question->load(['bank', 'author', 'choices']);
        return $this->success($question);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question): JsonResponse
    {
        $validated = $request->validate([
            'bank_id' => 'sometimes|required|exists:question_banks,id',
            'author_id' => 'sometimes|required|exists:teachers,id',
            'type' => 'sometimes|required|in:mcq,tf,numeric,short,essay,file',
            'prompt' => 'sometimes|required|string',
            'default_marks' => 'sometimes|required|numeric|min:0',
            'metadata' => 'nullable|array',
            'choices' => 'nullable|array',
            'choices.*.id' => 'nullable|exists:choices,id',
            'choices.*.choice_text' => 'required|string',
            'choices.*.is_correct' => 'boolean',
            'choices.*.position' => 'nullable|integer',
        ]);

        // Validate choices based on question type
        $questionType = $validated['type'] ?? $question->type;
        
        if (in_array($questionType, ['mcq', 'tf']) && empty($validated['choices']) && $question->choices->isEmpty()) {
            return $this->error('Choices are required for MCQ and True/False questions', 422);
        }

        // Validate at least one correct answer for MCQ
        if ($questionType === 'mcq' && isset($validated['choices'])) {
            $hasCorrect = collect($validated['choices'])->contains('is_correct', true);
            if (!$hasCorrect) {
                return $this->error('At least one correct answer is required for MCQ questions', 422);
            }
        }

        // Validate exactly one correct answer for True/False
        if ($questionType === 'tf' && isset($validated['choices'])) {
            $correctCount = collect($validated['choices'])->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                return $this->error('Exactly one correct answer is required for True/False questions', 422);
            }
        }

        // Extract choices from validated data
        $choices = $validated['choices'] ?? null;
        unset($validated['choices']);

        $question->update($validated);

        // Update choices if provided
        if ($choices !== null) {
            // Get existing choice IDs
            $existingChoiceIds = collect($choices)->pluck('id')->filter()->toArray();
            
            // Delete choices not in the request
            $question->choices()->whereNotIn('id', $existingChoiceIds)->delete();
            
            // Update or create choices
            foreach ($choices as $index => $choiceData) {
                if (isset($choiceData['id'])) {
                    // Update existing choice
                    $question->choices()->where('id', $choiceData['id'])->update([
                        'choice_text' => $choiceData['choice_text'],
                        'is_correct' => $choiceData['is_correct'] ?? false,
                        'position' => $choiceData['position'] ?? ($index + 1),
                    ]);
                } else {
                    // Create new choice
                    $question->choices()->create([
                        'choice_text' => $choiceData['choice_text'],
                        'is_correct' => $choiceData['is_correct'] ?? false,
                        'position' => $choiceData['position'] ?? ($index + 1),
                    ]);
                }
            }
        }

        $question->load(['bank', 'author', 'choices']);

        return $this->success($question, 'Question updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question): JsonResponse
    {
        $question->delete();

        return $this->success(null, 'Question deleted successfully');
    }
}
