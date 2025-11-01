<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GradeController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $grades = Grade::with('school')->get();
        return $this->success($grades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'level' => 'required|integer',
        ]);

        $grade = Grade::create($validated);

        return $this->success($grade, 'Grade created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade): JsonResponse
    {
        $grade->load('school');
        return $this->success($grade);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'sometimes|required|exists:schools,id',
            'name' => 'sometimes|required|string|max:255',
            'level' => 'sometimes|required|integer',
        ]);

        $grade->update($validated);

        return $this->success($grade, 'Grade updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade): JsonResponse
    {
        $grade->delete();

        return $this->success(null, 'Grade deleted successfully');
    }
}
