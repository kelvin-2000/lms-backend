<?php

namespace App\Http\Controllers;

use App\Models\MentorshipProgram;
use App\Models\MentorshipSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MentorshipSessionController extends Controller
{
    /**
     * Display a listing of mentorship sessions.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Admin sees all sessions
        if ($user->isAdmin()) {
            $sessions = MentorshipSession::with(['program', 'mentor', 'mentee'])
                ->orderBy('scheduled_at', 'desc')
                ->get();
        } 
        // Mentors see their sessions
        elseif ($user->role === 'instructor') {
            $sessions = MentorshipSession::with(['program', 'mentor', 'mentee'])
                ->where('mentor_id', $user->id)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        } 
        // Students/mentees see their sessions
        else {
            $sessions = MentorshipSession::with(['program', 'mentor', 'mentee'])
                ->where('mentee_id', $user->id)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Store a newly created mentorship session in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:mentorship_programs,id',
            'mentee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:180',
            'meeting_link' => 'nullable|url|max:255',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get the program and check if current user is the mentor
        $program = MentorshipProgram::findOrFail($request->program_id);
        $user = Auth::user();
        
        if (!$user->isAdmin() && $program->mentor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not the mentor for this program.'
            ], 403);
        }

        // Create the session with the mentor from the program
        $session = MentorshipSession::create([
            'program_id' => $request->program_id,
            'mentor_id' => $program->mentor_id,
            'mentee_id' => $request->mentee_id,
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_at' => $request->scheduled_at,
            'duration' => $request->duration,
            'meeting_link' => $request->meeting_link,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'data' => $session,
            'message' => 'Mentorship session created successfully'
        ], 201);
    }

    /**
     * Display the specified mentorship session.
     */
    public function show(MentorshipSession $session)
    {
        $session->load(['program', 'mentor', 'mentee']);
        
        // Check if user has permission to view this session
        $user = Auth::user();
        if (!$user->isAdmin() && $session->mentor_id !== $user->id && $session->mentee_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this session.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    /**
     * Update the specified mentorship session in storage.
     */
    public function update(Request $request, MentorshipSession $session)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'sometimes|required|date',
            'duration' => 'sometimes|required|integer|min:15|max:180',
            'meeting_link' => 'nullable|url|max:255',
            'status' => 'sometimes|required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has permission to update this session
        $user = Auth::user();
        if (!$user->isAdmin() && $session->mentor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this session.'
            ], 403);
        }

        $session->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $session,
            'message' => 'Mentorship session updated successfully'
        ]);
    }

    /**
     * Remove the specified mentorship session from storage.
     */
    public function destroy(MentorshipSession $session)
    {
        // Check if user has permission to delete this session
        $user = Auth::user();
        if (!$user->isAdmin() && $session->mentor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this session.'
            ], 403);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mentorship session deleted successfully'
        ]);
    }
} 