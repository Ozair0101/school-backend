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
    public function index(Request $request): JsonResponse
    {
        $query = StudentAttempt::with(['monthlyExam', 'student']);
        
        // Filter by monthly_exam_id if provided
        if ($request->has('monthly_exam_id')) {
            $query->where('monthly_exam_id', $request->monthly_exam_id);
        }
        
        // Filter by student_id if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Order by created_at descending by default
        $studentAttempts = $query->orderBy('created_at', 'desc')->get();
        
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
            'status' => 'nullable|in:in_progress,submitted,grading,graded,abandoned',
            'total_score' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'ip_address' => 'nullable|string|max:45',
            'device_info' => 'nullable|string',
            'attempt_token' => 'nullable|string|unique:student_attempts,attempt_token',
        ]);

        // Generate attempt_token if not provided
        if (empty($validated['attempt_token'])) {
            $validated['attempt_token'] = bin2hex(random_bytes(32));
        }

        // Set default status if not provided
        if (empty($validated['status'])) {
            $validated['status'] = 'in_progress';
        }

        // Set started_at if not provided and status is in_progress
        if (empty($validated['started_at']) && $validated['status'] === 'in_progress') {
            $validated['started_at'] = now();
        }

        // Capture IP address if not provided
        if (empty($validated['ip_address'])) {
            $validated['ip_address'] = $request->ip();
        }

        // Capture device info if not provided
        if (empty($validated['device_info'])) {
            $validated['device_info'] = json_encode([
                'user_agent' => $request->userAgent(),
                'platform' => $request->header('Sec-Ch-Ua-Platform'),
            ]);
        }

        $studentAttempt = StudentAttempt::create($validated);
        $studentAttempt->load(['monthlyExam', 'student']);

        return $this->success($studentAttempt, 'Student attempt created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAttempt $studentAttempt): JsonResponse
    {
        $studentAttempt->load(['monthlyExam', 'student', 'attemptAnswers', 'proctoringEvents']);
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
