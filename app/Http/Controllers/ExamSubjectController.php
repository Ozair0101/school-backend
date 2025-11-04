<?php

namespace App\Http\Controllers;

use App\Models\ExamSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamSubjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExamSubject::with(['monthlyExam', 'subject']);
        
        // Filter by monthly_exam_id if provided
        if ($request->has('monthly_exam_id')) {
            $query->where('monthly_exam_id', $request->monthly_exam_id);
        }
        
        $examSubjects = $query->get();
        return $this->success($examSubjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'required|exists:monthly_exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'max_marks' => 'required|integer|min:1',
            'pass_marks' => 'required|integer|min:0',
        ]);

        // Validate that pass_marks does not exceed max_marks
        if ($validated['pass_marks'] > $validated['max_marks']) {
            return $this->error('Pass marks cannot exceed max marks', 422);
        }

        // Check if this combination already exists
        $existing = ExamSubject::where('monthly_exam_id', $validated['monthly_exam_id'])
            ->where('subject_id', $validated['subject_id'])
            ->first();

        if ($existing) {
            return $this->error('This exam-subject combination already exists', 409);
        }

        $examSubject = ExamSubject::create($validated);
        $examSubject->load(['monthlyExam', 'subject']);

        return $this->success($examSubject, 'Exam subject created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamSubject $examSubject): JsonResponse
    {
        $examSubject->load(['monthlyExam', 'subject']);
        return $this->success($examSubject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSubject $examSubject): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'sometimes|required|exists:monthly_exams,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'max_marks' => 'sometimes|required|integer|min:1',
            'pass_marks' => 'sometimes|required|integer|min:0',
        ]);

        // Merge with existing values to get complete state for validation
        $mergedData = array_merge($examSubject->toArray(), $validated);
        
        // Validate that pass_marks does not exceed max_marks
        $maxMarks = $mergedData['max_marks'] ?? $examSubject->max_marks;
        $passMarks = $mergedData['pass_marks'] ?? $examSubject->pass_marks;
        
        if ($passMarks > $maxMarks) {
            return $this->error('Pass marks cannot exceed max marks', 422);
        }

        // Check if this combination already exists (excluding current)
        if (isset($validated['monthly_exam_id']) && isset($validated['subject_id'])) {
            $existing = ExamSubject::where('monthly_exam_id', $validated['monthly_exam_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('id', '!=', $examSubject->id)
                ->first();

            if ($existing) {
                return $this->error('This exam-subject combination already exists', 409);
            }
        }

        $examSubject->update($validated);
        $examSubject->load(['monthlyExam', 'subject']);

        return $this->success($examSubject, 'Exam subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSubject $examSubject): JsonResponse
    {
        $examSubject->delete();

        return $this->success(null, 'Exam subject deleted successfully');
    }
}
