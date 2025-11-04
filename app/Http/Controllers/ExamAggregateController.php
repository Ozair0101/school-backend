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
    public function index(Request $request): JsonResponse
    {
        $query = ExamAggregate::with(['monthlyExam', 'student']);
        
        // Filter by monthly_exam_id if provided
        if ($request->has('monthly_exam_id')) {
            $query->where('monthly_exam_id', $request->monthly_exam_id);
        }
        
        // Filter by student_id if provided
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        // Filter by published status
        if ($request->has('published')) {
            $published = filter_var($request->published, FILTER_VALIDATE_BOOLEAN);
            $query->where('published', $published);
        }
        
        // Order by rank if available, otherwise by total_marks descending
        $examAggregates = $query->orderByRaw('rank IS NULL, rank ASC')
            ->orderBy('total_marks', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
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
            'published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Check if aggregate already exists for this exam and student
        $existing = ExamAggregate::where('monthly_exam_id', $validated['monthly_exam_id'])
            ->where('student_id', $validated['student_id'])
            ->first();

        if ($existing) {
            // Update existing instead of creating new (upsert behavior)
            $existing->update($validated);
            $existing->load(['monthlyExam', 'student']);
            return $this->success($existing, 'Exam aggregate updated successfully');
        }

        // Set defaults
        if (!isset($validated['published'])) {
            $validated['published'] = false;
        }
        if (isset($validated['published']) && $validated['published'] && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $examAggregate = ExamAggregate::create($validated);
        $examAggregate->load(['monthlyExam', 'student']);

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
            'published' => 'nullable|boolean',
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

        // Set published_at if being published and not already set
        if (isset($validated['published']) && $validated['published'] && !$examAggregate->published) {
            if (!isset($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        $examAggregate->update($validated);
        $examAggregate->load(['monthlyExam', 'student']);

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
