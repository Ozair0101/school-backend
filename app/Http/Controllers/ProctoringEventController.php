<?php

namespace App\Http\Controllers;

use App\Models\ProctoringEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProctoringEventController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $proctoringEvents = ProctoringEvent::with('attempt')->get();
        return $this->success($proctoringEvents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:student_attempts,id',
            'event_type' => 'required|string|max:255',
            'event_time' => 'required|date',
            'details' => 'nullable|array',
        ]);

        $proctoringEvent = ProctoringEvent::create($validated);

        return $this->success($proctoringEvent, 'Proctoring event created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProctoringEvent $proctoringEvent): JsonResponse
    {
        $proctoringEvent->load('attempt');
        return $this->success($proctoringEvent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProctoringEvent $proctoringEvent): JsonResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'sometimes|required|exists:student_attempts,id',
            'event_type' => 'sometimes|required|string|max:255',
            'event_time' => 'sometimes|required|date',
            'details' => 'nullable|array',
        ]);

        $proctoringEvent->update($validated);

        return $this->success($proctoringEvent, 'Proctoring event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProctoringEvent $proctoringEvent): JsonResponse
    {
        $proctoringEvent->delete();

        return $this->success(null, 'Proctoring event deleted successfully');
    }
}
