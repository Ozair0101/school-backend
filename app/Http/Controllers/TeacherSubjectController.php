<?php

namespace App\Http\Controllers;

use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherSubjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $teacherSubjects = TeacherSubject::with(['teacher', 'subject', 'grade'])->get();
        return $this->success($teacherSubjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade_id' => 'required|exists:grades,id',
        ]);

        // Check if this combination already exists
        $existing = TeacherSubject::where('teacher_id', $validated['teacher_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('grade_id', $validated['grade_id'])
            ->first();

        if ($existing) {
            return $this->error('This teacher-subject-grade combination already exists', 409);
        }

        $teacherSubject = TeacherSubject::create($validated);

        return $this->success($teacherSubject, 'Teacher subject created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherSubject $teacherSubject): JsonResponse
    {
        $teacherSubject->load(['teacher', 'subject', 'grade']);
        return $this->success($teacherSubject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherSubject $teacherSubject): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => 'sometimes|required|exists:teachers,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'grade_id' => 'sometimes|required|exists:grades,id',
        ]);

        // Check if this combination already exists (excluding current)
        if (isset($validated['teacher_id']) && isset($validated['subject_id']) && isset($validated['grade_id'])) {
            $existing = TeacherSubject::where('teacher_id', $validated['teacher_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('grade_id', $validated['grade_id'])
                ->where('id', '!=', $teacherSubject->id)
                ->first();

            if ($existing) {
                return $this->error('This teacher-subject-grade combination already exists', 409);
            }
        }

        $teacherSubject->update($validated);

        return $this->success($teacherSubject, 'Teacher subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherSubject $teacherSubject): JsonResponse
    {
        $teacherSubject->delete();

        return $this->success(null, 'Teacher subject deleted successfully');
    }
}
