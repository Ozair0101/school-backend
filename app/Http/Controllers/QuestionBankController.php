<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionBankController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $questionBanks = QuestionBank::with(['school', 'creator'])->get();
        return $this->success($questionBanks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'created_by' => 'required|exists:teachers,id',
        ]);

        $questionBank = QuestionBank::create($validated);

        return $this->success($questionBank, 'Question bank created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionBank $questionBank): JsonResponse
    {
        $questionBank->load(['school', 'creator']);
        return $this->success($questionBank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionBank $questionBank): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'name' => 'sometimes|required|string|max:255',
            'created_by' => 'sometimes|required|exists:teachers,id',
        ]);

        $questionBank->update($validated);

        return $this->success($questionBank, 'Question bank updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionBank $questionBank): JsonResponse
    {
        $questionBank->delete();

        return $this->success(null, 'Question bank deleted successfully');
    }
}
