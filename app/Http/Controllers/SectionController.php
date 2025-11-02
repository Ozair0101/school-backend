<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Section::with('grade');
        
        // Filter by grade_id if provided
        if ($request->has('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }
        
        $sections = $query->get();
        return $this->success($sections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'name' => 'required|string|max:255',
        ]);

        $section = Section::create($validated);

        return $this->success($section, 'Section created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section): JsonResponse
    {
        $section->load('grade');
        return $this->success($section);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section): JsonResponse
    {
        $validated = $request->validate([
            'grade_id' => 'sometimes|required|exists:grades,id',
            'name' => 'sometimes|required|string|max:255',
        ]);

        $section->update($validated);

        return $this->success($section, 'Section updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section): JsonResponse
    {
        $section->delete();

        return $this->success(null, 'Section deleted successfully');
    }
}
