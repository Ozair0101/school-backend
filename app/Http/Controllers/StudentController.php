<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $students = Student::with('school')->get();
        return $this->success($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'admission_no' => 'required|string|unique:students,admission_no',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact' => 'nullable|array',
        ]);

        $student = Student::create($validated);

        return $this->success($student, 'Student created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load('school');
        return $this->success($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'admission_no' => 'sometimes|required|string|unique:students,admission_no,' . $student->id,
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact' => 'nullable|array',
        ]);

        $student->update($validated);

        return $this->success($student, 'Student updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return $this->success(null, 'Student deleted successfully');
    }
}
