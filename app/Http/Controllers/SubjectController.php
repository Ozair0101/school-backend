<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with('school');
        
        // Filter by school_id if provided
        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }
        
        $subjects = $query->get();
        return $this->success($subjects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'default_max_marks' => 'required|numeric|min:0',
            'pass_marks' => 'required|numeric|min:0',
        ]);

        $subject = Subject::create($validated);

        return $this->success($subject, 'Subject created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): JsonResponse
    {
        $subject->load('school');
        return $this->success($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50',
            'default_max_marks' => 'sometimes|required|numeric|min:0',
            'pass_marks' => 'sometimes|required|numeric|min:0',
        ]);

        $subject->update($validated);

        return $this->success($subject, 'Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return $this->success(null, 'Subject deleted successfully');
    }
}
