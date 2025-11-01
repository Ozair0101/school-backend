<?php

namespace App\Http\Controllers;

use App\Models\Choice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChoiceController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $choices = Choice::with('question')->get();
        return $this->success($choices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'choice_text' => 'required|string',
            'is_correct' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        $choice = Choice::create($validated);

        return $this->success($choice, 'Choice created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Choice $choice): JsonResponse
    {
        $choice->load('question');
        return $this->success($choice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Choice $choice): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'sometimes|required|exists:questions,id',
            'choice_text' => 'sometimes|required|string',
            'is_correct' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        $choice->update($validated);

        return $this->success($choice, 'Choice updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Choice $choice): JsonResponse
    {
        $choice->delete();

        return $this->success(null, 'Choice deleted successfully');
    }
}
