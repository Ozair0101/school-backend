<?php

namespace App\Http\Controllers;

use App\Models\MonthlyExam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MonthlyExamController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $monthlyExams = MonthlyExam::with(['school', 'grade', 'section'])->get();
            return $this->success($monthlyExams);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch monthly exams: ' . $e->getMessage());
            return $this->error('Failed to fetch exams. Please ensure the database is running.', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'exam_date' => 'required|date',
            'description' => 'nullable|string',
            'online_enabled' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_choices' => 'boolean',
            'negative_marking' => 'numeric|min:0',
            'passing_percentage' => 'numeric|min:0|max:100',
            'access_code' => 'nullable|string|max:50',
            'random_pool' => 'boolean',
            'show_answers_after' => 'boolean',
            'auto_publish_results' => 'boolean',
        ]);

        // Check if exam already exists for this grade, section, month, and year
        $existing = MonthlyExam::where('grade_id', $validated['grade_id'])
            ->where('section_id', $validated['section_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existing) {
            return $this->error('An exam already exists for this grade, section, month, and year', 409);
        }

        $monthlyExam = MonthlyExam::create($validated);

        return $this->success($monthlyExam, 'Monthly exam created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyExam $monthlyExam): JsonResponse
    {
        $monthlyExam->load(['school', 'grade', 'section']);
        return $this->success($monthlyExam);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyExam $monthlyExam): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'grade_id' => 'sometimes|required|exists:grades,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'month' => 'sometimes|required|integer|min:1|max:12',
            'year' => 'sometimes|required|integer',
            'exam_date' => 'sometimes|required|date',
            'description' => 'nullable|string',
            'online_enabled' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_choices' => 'boolean',
            'negative_marking' => 'numeric|min:0',
            'passing_percentage' => 'numeric|min:0|max:100',
            'access_code' => 'nullable|string|max:50',
            'random_pool' => 'boolean',
            'show_answers_after' => 'boolean',
            'auto_publish_results' => 'boolean',
        ]);

        // Check if exam already exists for this grade, section, month, and year (excluding current)
        if (isset($validated['grade_id']) && isset($validated['section_id']) &&
            isset($validated['month']) && isset($validated['year'])) {
            $existing = MonthlyExam::where('grade_id', $validated['grade_id'])
                ->where('section_id', $validated['section_id'])
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->where('id', '!=', $monthlyExam->id)
                ->first();

            if ($existing) {
                return $this->error('An exam already exists for this grade, section, month, and year', 409);
            }
        }

        $monthlyExam->update($validated);

        return $this->success($monthlyExam, 'Monthly exam updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyExam $monthlyExam): JsonResponse
    {
        $monthlyExam->delete();

        return $this->success(null, 'Monthly exam deleted successfully');
    }
}
