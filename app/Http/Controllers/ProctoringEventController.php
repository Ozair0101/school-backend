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
    public function index(Request $request): JsonResponse
    {
        $query = ProctoringEvent::with(['attempt.student', 'attempt.monthlyExam']);
        
        // Filter by attempt_id if provided
        if ($request->has('attempt_id')) {
            $query->where('attempt_id', $request->attempt_id);
        }
        
        // Filter by event_type if provided
        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        
        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('event_time', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('event_time', '<=', $request->end_date);
        }
        
        $proctoringEvents = $query->orderBy('event_time', 'desc')->orderBy('created_at', 'desc')->get();
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
            'event_time' => 'nullable|date',
            'details' => 'nullable|array',
        ]);

        // Set event_time to now if not provided
        if (!isset($validated['event_time'])) {
            $validated['event_time'] = now();
        }

        $proctoringEvent = ProctoringEvent::create($validated);
        $proctoringEvent->load(['attempt.student', 'attempt.monthlyExam']);

        return $this->success($proctoringEvent, 'Proctoring event created successfully', 201);
    }

    /**
     * Store multiple proctoring events at once (batch).
     */
    public function batch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'events' => 'required|array|min:1',
            'events.*.attempt_id' => 'required|exists:student_attempts,id',
            'events.*.event_type' => 'required|string|max:255',
            'events.*.event_time' => 'nullable|date',
            'events.*.details' => 'nullable|array',
        ]);

        $events = [];
        foreach ($validated['events'] as $eventData) {
            if (!isset($eventData['event_time'])) {
                $eventData['event_time'] = now();
            }
            $events[] = ProctoringEvent::create($eventData);
        }

        return $this->success($events, 'Proctoring events created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProctoringEvent $proctoringEvent): JsonResponse
    {
        $proctoringEvent->load(['attempt.student', 'attempt.monthlyExam']);
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
