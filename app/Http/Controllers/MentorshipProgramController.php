<?php

namespace App\Http\Controllers;

use App\Models\MentorshipProgram;
use App\Models\MentorshipApplication;
use App\Models\MentorshipSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MentorshipProgramController extends Controller
{
    /**
     * Display a listing of open mentorship programs.
     */
    public function open(Request $request)
    {
        $query = MentorshipProgram::with('mentor');
            // ->where('status', 'open');

        // Apply category filter
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply duration filter
        if ($request->has('duration') && $request->duration !== '') {
            switch ($request->duration) {
                case 'short':
                    $query->where(function($q) {
                        $q->where('duration', 'like', '%1%')
                          ->orWhere('duration', 'like', '%2%')
                          ->orWhere('duration', 'like', '%12 weeks%')
                          ->orWhere('duration', 'like', '%8 weeks%');
                    });
                    break;
                case 'medium':
                    $query->where(function($q) {
                        $q->where('duration', 'like', '%3%')
                          ->orWhere('duration', 'like', '%4%')
                          ->orWhere('duration', 'like', '%16 weeks%')
                          ->orWhere('duration', 'like', '%20 weeks%');
                    });
                    break;
                case 'long':
                    $query->where(function($q) {
                        $q->where('duration', 'like', '%5%')
                          ->orWhere('duration', 'like', '%6%')
                          ->orWhere('duration', 'like', '%7%')
                          ->orWhere('duration', 'like', '%8%')
                          ->orWhere('duration', 'like', '%9%')
                          ->orWhere('duration', 'like', '%10%')
                          ->orWhere('duration', 'like', '%11%')
                          ->orWhere('duration', 'like', '%12%')
                          ->orWhere('duration', 'like', '%24 weeks%')
                          ->orWhere('duration', 'like', '%36 weeks%')
                          ->orWhere('duration', 'like', '%48 weeks%');
                    });
                    break;
            }
        }

        // Apply search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('mentor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $programs = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $programs
        ]);
    }

    /**
     * Display a listing of all mentorship programs.
     */
    public function index()
    {
        $programs = MentorshipProgram::with('mentor')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $programs
        ]);
    }

    /**
     * Store a newly created mentorship program in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'mentor_id' => 'required|exists:users,id',
            'duration' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'required|in:open,closed,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $program = MentorshipProgram::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $program,
            'message' => 'Mentorship program created successfully'
        ], 201);
    }

    /**
     * Display the specified mentorship program.
     */
    public function show(MentorshipProgram $mentorshipProgram)
    {
        $mentorshipProgram->load('mentor');
        
        return response()->json([
            'success' => true,
            'data' => $mentorshipProgram
        ]);
    }

    /**
     * Update the specified mentorship program in storage.
     */
    public function update(Request $request, MentorshipProgram $mentorshipProgram)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'mentor_id' => 'sometimes|required|exists:users,id',
            'duration' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'sometimes|required|in:open,closed,completed',
            'category' => 'nullable|string|in:technology,business,career,academic',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Keep a copy of the original program for debugging
        $originalProgram = $mentorshipProgram->toArray();
        
        // Update the program
        $mentorshipProgram->update($request->all());
        
        // Get a fresh instance of the model
        $updatedProgram = $mentorshipProgram->fresh();
        
        // Write to log file for debugging
        \Log::info('Mentorship program update', [
            'program_id' => $mentorshipProgram->id,
            'updated_program_exists' => (bool)$updatedProgram,
            'updated_program' => $updatedProgram ? $updatedProgram->toArray() : null
        ]);
        
        // Ensure we have data to return
        if (!$updatedProgram) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving updated program data',
                'program_id' => $mentorshipProgram->id,
                'original_program' => $originalProgram
            ], 500);
        }

        // Load the mentor relationship
        $updatedProgram->load('mentor');

        // Return explicit array representation of the model
        return response()->json([
            'success' => true,
            'data' => $updatedProgram->toArray(),
            'message' => 'Mentorship program updated successfully'
        ]);
    }

    /**
     * Remove the specified mentorship program from storage.
     */
    public function destroy(MentorshipProgram $mentorshipProgram)
    {
        $mentorshipProgram->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mentorship program deleted successfully'
        ]);
    }

    /**
     * Get applications for a specific mentorship program.
     */
    public function applications(MentorshipProgram $mentorshipProgram)
    {
        $applications = $mentorshipProgram->applications()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Get sessions for a specific mentorship program.
     */
    public function sessions(MentorshipProgram $mentorshipProgram)
    {
        $sessions = $mentorshipProgram->sessions()->with(['mentor', 'mentee'])->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }
} 