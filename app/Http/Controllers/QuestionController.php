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
        ]);

        $question = Question::create($validated);

        return $this->success($question, 'Question created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question): JsonResponse
    {
        $question->load(['bank', 'author']);
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
        ]);

        $question->update($validated);

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
