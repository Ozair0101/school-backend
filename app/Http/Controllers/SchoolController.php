<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SchoolController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $schools = School::all();
        return $this->success($schools);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $school = School::create($validated);

        return $this->success($school, 'School created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school): JsonResponse
    {
        return $this->success($school);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $school->update($validated);

        return $this->success($school, 'School updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school): JsonResponse
    {
        $school->delete();

        return $this->success(null, 'School deleted successfully');
    }
}
