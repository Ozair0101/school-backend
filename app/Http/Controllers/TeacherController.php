<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $teachers = Teacher::with('school')->get();
        return $this->success($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $teacher = Teacher::create($validated);

        return $this->success($teacher, 'Teacher created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher): JsonResponse
    {
        $teacher->load('school');
        return $this->success($teacher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'full_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $teacher->update($validated);

        return $this->success($teacher, 'Teacher updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher): JsonResponse
    {
        $teacher->delete();

        return $this->success(null, 'Teacher deleted successfully');
    }
}
