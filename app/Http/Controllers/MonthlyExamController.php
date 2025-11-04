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
            // Test database connection first
            \DB::connection()->getPdo();
            
            // Optimize query - only select needed fields and limit relationships
            $monthlyExams = MonthlyExam::with([
                'school:id,name',
                'grade:id,name,level',
                'section:id,name'
            ])
            ->select([
                'id',
                'school_id',
                'grade_id',
                'section_id',
                'month',
                'year',
                'exam_date',
                'description',
                'online_enabled',
                'start_time',
                'end_time',
                'duration_minutes',
                'allow_multiple_attempts',
                'max_attempts',
                'shuffle_questions',
                'shuffle_choices',
                'negative_marking',
                'passing_percentage',
                'access_code',
                'random_pool',
                'show_answers_after',
                'auto_publish_results',
                'created_at',
                'updated_at'
            ])
            ->orderBy('exam_date', 'desc')
            ->get();
            
            return $this->success($monthlyExams);
        } catch (\PDOException $e) {
            \Log::error('Database connection failed: ' . $e->getMessage());
            return $this->error('Database connection failed. Please check your database configuration and ensure MySQL is running.', 503);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch monthly exams: ' . $e->getMessage());
            return $this->error('Failed to fetch exams: ' . $e->getMessage(), 500);
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
            'year' => 'required|integer|min:2000|max:3000',
            'exam_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            
            // Online exam settings
            'online_enabled' => 'sometimes|boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1|max:1440', // Max 24 hours
            'allow_multiple_attempts' => 'sometimes|boolean',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_choices' => 'sometimes|boolean',
            'negative_marking' => 'nullable|numeric|min:0|max:100',
            'passing_percentage' => 'nullable|numeric|min:0|max:100',
            'access_code' => 'nullable|string|max:50|regex:/^[A-Z0-9]+$/',
            'random_pool' => 'sometimes|boolean',
            'show_answers_after' => 'sometimes|boolean',
            'auto_publish_results' => 'sometimes|boolean',
        ]);

        // Set defaults for online settings
        $validated['online_enabled'] = $validated['online_enabled'] ?? false;
        $validated['allow_multiple_attempts'] = $validated['allow_multiple_attempts'] ?? false;
        $validated['shuffle_questions'] = $validated['shuffle_questions'] ?? false;
        $validated['shuffle_choices'] = $validated['shuffle_choices'] ?? false;
        $validated['random_pool'] = $validated['random_pool'] ?? false;
        $validated['show_answers_after'] = $validated['show_answers_after'] ?? false;
        $validated['auto_publish_results'] = $validated['auto_publish_results'] ?? false;
        $validated['negative_marking'] = $validated['negative_marking'] ?? 0;
        $validated['passing_percentage'] = $validated['passing_percentage'] ?? 0;
        $validated['max_attempts'] = $validated['max_attempts'] ?? ($validated['allow_multiple_attempts'] ? 3 : 1);

        // Validate online settings if online_enabled is true
        if ($validated['online_enabled']) {
            if (empty($validated['start_time']) || empty($validated['end_time'])) {
                return $this->error('Start time and end time are required when online_enabled is true', 422);
            }
            
            if (empty($validated['duration_minutes'])) {
                return $this->error('Duration is required when online_enabled is true', 422);
            }

            // Validate that end_time is after start_time
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            
            if ($endTime <= $startTime) {
                return $this->error('End time must be after start time', 422);
            }

            // Validate duration matches time range
            $calculatedDuration = $startTime->diffInMinutes($endTime);
            if ($validated['duration_minutes'] > $calculatedDuration) {
                return $this->error('Duration cannot exceed the time between start and end time', 422);
            }
        }

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
            'year' => 'sometimes|required|integer|min:2000|max:3000',
            'exam_date' => 'sometimes|required|date',
            'description' => 'nullable|string|max:1000',
            
            // Online exam settings
            'online_enabled' => 'sometimes|boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1|max:1440',
            'allow_multiple_attempts' => 'sometimes|boolean',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'shuffle_questions' => 'sometimes|boolean',
            'shuffle_choices' => 'sometimes|boolean',
            'negative_marking' => 'nullable|numeric|min:0|max:100',
            'passing_percentage' => 'nullable|numeric|min:0|max:100',
            'access_code' => 'nullable|string|max:50|regex:/^[A-Z0-9]+$/',
            'random_pool' => 'sometimes|boolean',
            'show_answers_after' => 'sometimes|boolean',
            'auto_publish_results' => 'sometimes|boolean',
        ]);

        // Merge with existing values to get complete state
        $mergedData = array_merge($monthlyExam->toArray(), $validated);
        
        // Validate online settings if online_enabled is true
        if (isset($validated['online_enabled']) && $validated['online_enabled']) {
            $startTime = $mergedData['start_time'] ?? null;
            $endTime = $mergedData['end_time'] ?? null;
            $durationMinutes = $mergedData['duration_minutes'] ?? null;
            
            if (empty($startTime) || empty($endTime)) {
                return $this->error('Start time and end time are required when online_enabled is true', 422);
            }
            
            if (empty($durationMinutes)) {
                return $this->error('Duration is required when online_enabled is true', 422);
            }

            // Validate that end_time is after start_time
            $startTimeCarbon = \Carbon\Carbon::parse($startTime);
            $endTimeCarbon = \Carbon\Carbon::parse($endTime);
            
            if ($endTimeCarbon <= $startTimeCarbon) {
                return $this->error('End time must be after start time', 422);
            }

            // Validate duration matches time range
            $calculatedDuration = $startTimeCarbon->diffInMinutes($endTimeCarbon);
            if ($durationMinutes > $calculatedDuration) {
                return $this->error('Duration cannot exceed the time between start and end time', 422);
            }
        }

        // Ensure max_attempts is set if allow_multiple_attempts is true
        if (isset($validated['allow_multiple_attempts']) && $validated['allow_multiple_attempts']) {
            if (empty($validated['max_attempts'])) {
                $validated['max_attempts'] = $mergedData['max_attempts'] ?? 3;
            }
        } elseif (isset($validated['allow_multiple_attempts']) && !$validated['allow_multiple_attempts']) {
            $validated['max_attempts'] = 1;
        }

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

    /**
     * Get questions for a monthly exam.
     */
    public function questions(MonthlyExam $monthlyExam): JsonResponse
    {
        $examQuestions = $monthlyExam->examQuestions()
            ->with(['question.choices'])
            ->orderBy('sequence')
            ->orderBy('id')
            ->get();
        
        return $this->success($examQuestions);
    }
}
