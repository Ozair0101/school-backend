<?php

namespace App\Http\Controllers;

use App\Models\StudentAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentAttemptController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $studentAttempts = StudentAttempt::with(['monthlyExam', 'student'])->get();
        return $this->success($studentAttempts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'required|exists:monthly_exams,id',
            'student_id' => 'required|exists:students,id',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'status' => 'in:in_progress,submitted,grading,graded,abandoned',
            'total_score' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'ip_address' => 'nullable|string|max:45',
            'device_info' => 'nullable|string',
            'attempt_token' => 'required|string|unique:student_attempts,attempt_token',
        ]);

        $studentAttempt = StudentAttempt::create($validated);

        return $this->success($studentAttempt, 'Student attempt created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAttempt $studentAttempt): JsonResponse
    {
        $studentAttempt->load(['monthlyExam', 'student']);
        return $this->success($studentAttempt);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentAttempt $studentAttempt): JsonResponse
    {
        $validated = $request->validate([
            'monthly_exam_id' => 'sometimes|required|exists:monthly_exams,id',
            'student_id' => 'sometimes|required|exists:students,id',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'status' => 'in:in_progress,submitted,grading,graded,abandoned',
            'total_score' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'ip_address' => 'nullable|string|max:45',
            'device_info' => 'nullable|string',
            'attempt_token' => 'sometimes|required|string|unique:student_attempts,attempt_token,' . $studentAttempt->id,
        ]);

        $studentAttempt->update($validated);

        return $this->success($studentAttempt, 'Student attempt updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAttempt $studentAttempt): JsonResponse
    {
        $studentAttempt->delete();

        return $this->success(null, 'Student attempt deleted successfully');
    }
}
