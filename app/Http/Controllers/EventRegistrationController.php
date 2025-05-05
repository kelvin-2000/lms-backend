<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventRegistrationController extends Controller
{
    /**
     * Register a user for an event.
     */
    public function register(Request $request, Event $event)
    {
        // Check if the event is at capacity
        if ($event->capacity !== null && $event->registrations()->count() >= $event->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'This event has reached its capacity.'
            ], 422);
        }

        // Check if the event is still open for registrations
        if ($event->status !== 'upcoming' || $event->start_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'This event is no longer open for registration.'
            ], 422);
        }

        // Check if the user is already registered
        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRegistration) {
            if ($existingRegistration->status === 'cancelled') {
                // If previously cancelled, reactivate the registration
                $existingRegistration->status = 'registered';
                $existingRegistration->save();

                return response()->json([
                    'success' => true,
                    'data' => $existingRegistration,
                    'message' => 'Event registration reactivated successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this event.'
            ], 422);
        }

        // Create a new registration
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'status' => 'registered'
        ]);

        return response()->json([
            'success' => true,
            'data' => $registration,
            'message' => 'Registered for event successfully.'
        ], 201);
    }

    /**
     * Cancel a user's registration for an event.
     */
    public function cancel(Event $event)
    {
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event.'
            ], 404);
        }

        if ($registration->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Your registration is already cancelled.'
            ], 422);
        }

        // Cancel the registration
        $registration->status = 'cancelled';
        $registration->save();

        return response()->json([
            'success' => true,
            'data' => $registration,
            'message' => 'Event registration cancelled successfully.'
        ]);
    }

    /**
     * Update the status of a registration (admin only).
     */
    public function updateStatus(Request $request, EventRegistration $registration)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:registered,attended,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $registration->status = $request->status;
        $registration->save();

        return response()->json([
            'success' => true,
            'data' => $registration,
            'message' => 'Registration status updated successfully.'
        ]);
    }

    /**
     * Check registration status for a user in a specific event.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'sometimes|exists:users,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        $userId = $request->user_id ?? $user->id;
        
        // If checking for another user, ensure current user has permission
        if ($userId != $user->id && !in_array($user->role, ['admin', 'superAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to check registration status for other users'
            ], 403);
        }
        
        // Find registration
        $registration = EventRegistration::where('user_id', $userId)
            ->where('event_id', $request->event_id)
            ->first();
            
        if (!$registration) {
            return response()->json([
                'success' => true,
                'message' => 'User is not registered for this event',
                'data' => [
                    'is_registered' => false,
                    'registration' => null
                ]
            ]);
        }
        
        // Load related data for more context
        $registration->load(['event:id,title,description,start_date,end_date,location,capacity']);
        
        return response()->json([
            'success' => true,
            'message' => 'User is registered for this event',
            'data' => [
                'is_registered' => true,
                'registration_status' => $registration->status,
                'registration' => $registration
            ]
        ]);
    }
} 