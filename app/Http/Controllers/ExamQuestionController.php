<?php

namespace App\Http\Controllers;

use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamQuestionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamQuestion::with(['monthlyExam', 'question.choices']);
        
        // Filter by monthly_exam_id if provided
        if ($request->has('monthly_exam_id')) {
            $query->where('monthly_exam_id', $request->monthly_exam_id);
        }
        
        $examQuestions = $query->orderBy('sequence')->orderBy('id')->get();
        return $this->success($examQuestions);
    }

    /**
     * Store multiple exam questions at once (batch)
     */
    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'required|exists:monthly_exams,id',
            'questions' => 'required|array|min:1',
            'questions.*.question_id' => 'required|exists:questions,id',
            'questions.*.marks' => 'nullable|numeric|min:0',
            'questions.*.sequence' => 'nullable|integer',
            'questions.*.pool_tag' => 'nullable|string|max:255',
        ]);

        $examQuestions = [];
        foreach ($validated['questions'] as $questionData) {
            $examQuestions[] = ExamQuestion::create([
                'monthly_exam_id' => $validated['monthly_exam_id'],
                ...$questionData,
            ]);
        }

        return $this->success($examQuestions, 'Exam questions created successfully', 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'required|exists:monthly_exams,id',
            'question_id' => 'required|exists:questions,id',
            'marks' => 'nullable|numeric|min:0',
            'sequence' => 'nullable|integer',
            'pool_tag' => 'nullable|string|max:255',
        ]);

        $examQuestion = ExamQuestion::create($validated);

        return $this->success($examQuestion, 'Exam question created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamQuestion $examQuestion): JsonResponse
    {
        $examQuestion->load(['monthlyExam', 'question']);
        return $this->success($examQuestion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamQuestion $examQuestion): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'sometimes|required|exists:monthly_exams,id',
            'question_id' => 'sometimes|required|exists:questions,id',
            'marks' => 'nullable|numeric|min:0',
            'sequence' => 'nullable|integer',
            'pool_tag' => 'nullable|string|max:255',
        ]);

        $examQuestion->update($validated);

        return $this->success($examQuestion, 'Exam question updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamQuestion $examQuestion): JsonResponse
    {
        $examQuestion->delete();

        return $this->success(null, 'Exam question deleted successfully');
    }
}
