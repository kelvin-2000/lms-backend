<?php

namespace App\Http\Controllers;

use App\Models\MentorshipProgram;
use App\Models\MentorshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MentorshipApplicationController extends Controller
{
    /**
     * Apply for a mentorship program.
     */
    public function apply(Request $request, MentorshipProgram $program)
    {
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

        // Check if the program is open
        if ($program->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'This mentorship program is not accepting applications at this time.'
            ], 422);
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
} 