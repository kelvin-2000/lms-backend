<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of upcoming events.
     */
    public function upcoming(Request $request)
    {
        $query = Event::where('status', 'upcoming')
            ->where('start_date', '>=', now());

        // Search by title or description if search parameter is provided
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by event type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by location if provided
        if ($request->filled('location')) {
            if ($request->location === 'in-person') {
                $query->where('location', '!=', 'online');
            } else {
                $query->where('location', 'like', '%' . $request->location . '%');
            }
        }

        $events = $query->orderBy('start_date')->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Display a listing of all events.
     */
    public function index()
    {
        $events = Event::orderBy('start_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'thumbnail' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|in:webinar,workshop,conference,other',
            'status' => 'required|in:upcoming,ongoing,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event created successfully'
        ], 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'location' => 'sometimes|required|string|max:255',
            'thumbnail' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'sometimes|required|in:webinar,workshop,conference,other',
            'status' => 'sometimes|required|in:upcoming,ongoing,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $event->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event updated successfully'
        ]);
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Get registrations for a specific event.
     */
    public function registrations(Event $event)
    {
        $registrations = $event->registrations()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }

    /**
     * Search upcoming events by title and location.
     */
    public function search(Request $request)
    {
        $query = Event::where('status', 'upcoming')
            ->where('start_date', '>=', now());

        // Search by title if provided
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Search by location if provided
        if ($request->has('location')) {
            if ($request->location === 'in-person') {
                $query->where('location', '!=', 'online');
            } else {
                $query->where('location', 'like', '%' . $request->location . '%');
            }
        }

        // Filter by event type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $events = $query->orderBy('start_date')->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }
} 