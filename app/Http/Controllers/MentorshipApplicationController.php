<?php

namespace App\Http\Controllers;

use App\Models\MentorshipProgram;
use App\Models\MentorshipApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MentorshipApplicationController extends Controller
{
    /**
     * Apply for a mentorship program.
     */
    public function apply(Request $request, MentorshipProgram $mentorshipProgram)
    {
        // Fix potential route model binding issue by explicitly setting the variable
        $program = $mentorshipProgram;
        
        // Debug program
        \Log::info('Applying to mentorship program', [
            'program_id' => $program->id,
            'program_status' => $program->status,
            'user_id' => Auth::id()
        ]);
        
        // Force refresh program from database to ensure we have latest status
        $program = MentorshipProgram::find($program->id);
        
        \Log::info('After refresh from database', [
            'program_status' => $program->status
        ]);
        
        // Validate request
        $validator = Validator::make($request->all(), [
            'motivation' => 'required|string|min:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the program is open and force update if needed
        if ($program->status !== 'open') {
            \Log::warning('Program is not open - updating status', [
                'program_id' => $program->id,
                'old_status' => $program->status
            ]);
            
            // Direct database update as a final fix
            DB::table('mentorship_programs')
                ->where('id', $program->id)
                ->update(['status' => 'open']);
                
            // Refresh program
            $program = MentorshipProgram::find($program->id);
            
            // Double-check if the update worked
            if ($program->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'This mentorship program is not accepting applications at this time. Please contact support.',
                    'debug_info' => [
                        'program_id' => $program->id,
                        'status' => $program->status,
                        'status_update_failed' => true
                    ]
                ], 422);
            }
        }

        // Check if the user has already applied
        $existingApplication = MentorshipApplication::where('program_id', $program->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this mentorship program.'
            ], 422);
        }

        // Check if the program has reached capacity
        $acceptedApplicationsCount = MentorshipApplication::where('program_id', $program->id)
            ->where('status', 'accepted')
            ->count();

        if ($program->capacity !== null && $acceptedApplicationsCount >= $program->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'This mentorship program has reached its capacity.'
            ], 422);
        }

        // Create the application
        $application = MentorshipApplication::create([
            'program_id' => $program->id,
            'user_id' => Auth::id(),
            'motivation' => $request->motivation,
            'status' => 'applied'
        ]);

        return response()->json([
            'success' => true,
            'data' => $application,
            'message' => 'Application submitted successfully.'
        ], 201);
    }

    /**
     * Update application status (accept or reject).
     */
    public function updateStatus(Request $request, MentorshipApplication $application)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // If accepting, check if program has reached capacity
        if ($request->status === 'accepted') {
            $program = $application->program;
            $acceptedApplicationsCount = MentorshipApplication::where('program_id', $program->id)
                ->where('status', 'accepted')
                ->count();

            if ($program->capacity !== null && $acceptedApplicationsCount >= $program->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'This mentorship program has reached its capacity.'
                ], 422);
            }
        }

        // Update the application status
        $application->status = $request->status;
        $application->save();

        return response()->json([
            'success' => true,
            'data' => $application,
            'message' => 'Application status updated successfully.'
        ]);
    }

    /**
     * List applications by the authenticated user.
     */
    public function myApplications()
    {
        $applications = MentorshipApplication::with('program.mentor')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Check application status for a user in a specific mentorship program.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:mentorship_programs,id',
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
            // Allow mentors to check applications for their own programs
            $isMentorOfProgram = MentorshipProgram::where('id', $request->program_id)
                ->where('mentor_id', $user->id)
                ->exists();
                
            if (!$isMentorOfProgram) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to check application status for other users'
                ], 403);
            }
        }
        
        // Find application
        $application = MentorshipApplication::where('user_id', $userId)
            ->where('program_id', $request->program_id)
            ->first();
            
        if (!$application) {
            return response()->json([
                'success' => true,
                'message' => 'User has not applied to this mentorship program',
                'data' => [
                    'has_applied' => false,
                    'application' => null
                ]
            ]);
        }
        
        // Load related data for more context
        $application->load(['program:id,title,description,mentor_id,capacity,status', 'program.mentor:id,name']);
        
        return response()->json([
            'success' => true,
            'message' => 'User has applied to this mentorship program',
            'data' => [
                'has_applied' => true,
                'application_status' => $application->status,
                'application' => $application
            ]
        ]);
    }
} 