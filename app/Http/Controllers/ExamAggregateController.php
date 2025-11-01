<?php

namespace App\Http\Controllers;

use App\Models\ExamAggregate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamAggregateController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $examAggregates = ExamAggregate::with(['monthlyExam', 'student'])->get();
        return $this->success($examAggregates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'required|exists:monthly_exams,id',
            'student_id' => 'required|exists:students,id',
            'total_marks' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'rank' => 'nullable|integer|min:1',
            'published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Check if aggregate already exists for this exam and student
        $existing = ExamAggregate::where('monthly_exam_id', $validated['monthly_exam_id'])
            ->where('student_id', $validated['student_id'])
            ->first();

        if ($existing) {
            return $this->error('An aggregate already exists for this exam and student', 409);
        }

        $examAggregate = ExamAggregate::create($validated);

        return $this->success($examAggregate, 'Exam aggregate created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamAggregate $examAggregate): JsonResponse
    {
        $examAggregate->load(['monthlyExam', 'student']);
        return $this->success($examAggregate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamAggregate $examAggregate): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'sometimes|required|exists:monthly_exams,id',
            'student_id' => 'sometimes|required|exists:students,id',
            'total_marks' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'rank' => 'nullable|integer|min:1',
            'published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Check if aggregate already exists for this exam and student (excluding current)
        if (isset($validated['monthly_exam_id']) && isset($validated['student_id'])) {
            $existing = ExamAggregate::where('monthly_exam_id', $validated['monthly_exam_id'])
                ->where('student_id', $validated['student_id'])
                ->where('id', '!=', $examAggregate->id)
                ->first();

            if ($existing) {
                return $this->error('An aggregate already exists for this exam and student', 409);
            }
        }

        $examAggregate->update($validated);

        return $this->success($examAggregate, 'Exam aggregate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamAggregate $examAggregate): JsonResponse
    {
        $examAggregate->delete();

        return $this->success(null, 'Exam aggregate deleted successfully');
    }
}
