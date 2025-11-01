<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnrollmentController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $enrollments = Enrollment::with(['student', 'grade', 'section'])->get();
        return $this->success($enrollments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grade_id' => 'required|exists:grades,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year' => 'required|string|max:255',
            'roll_no' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        // Check if enrollment already exists for this student and academic year
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('academic_year', $validated['academic_year'])
            ->first();

        if ($existing) {
            return $this->error('Enrollment already exists for this student and academic year', 409);
        }

        $enrollment = Enrollment::create($validated);

        return $this->success($enrollment, 'Enrollment created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment): JsonResponse
    {
        $enrollment->load(['student', 'grade', 'section']);
        return $this->success($enrollment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|required|exists:students,id',
            'grade_id' => 'sometimes|required|exists:grades,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'academic_year' => 'sometimes|required|string|max:255',
            'roll_no' => 'sometimes|required|string|max:255',
            'active' => 'boolean',
        ]);

        // Check if enrollment already exists for this student and academic year (excluding current)
        if (isset($validated['student_id']) && isset($validated['academic_year'])) {
            $existing = Enrollment::where('student_id', $validated['student_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('id', '!=', $enrollment->id)
                ->first();

            if ($existing) {
                return $this->error('Enrollment already exists for this student and academic year', 409);
            }
        }

        $enrollment->update($validated);

        return $this->success($enrollment, 'Enrollment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();

        return $this->success(null, 'Enrollment deleted successfully');
    }
}
