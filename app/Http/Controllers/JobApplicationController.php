<?php

namespace App\Http\Controllers;

use App\Models\JobOpportunity;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the user's job applications.
     */
    public function index()
    {
        $applications = JobApplication::with('job')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    /**
     * Apply for a job opportunity.
     */
    public function apply(Request $request, JobOpportunity $job)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'cover_letter' => 'required|string|min:50',
            'resume_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if job is open
        if ($job->status !== 'open' || $job->deadline < now()) {
            return response()->json([
                'success' => false,
                'message' => 'This job opportunity is not accepting applications at this time.'
            ], 422);
        }

        // Check if the user has already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job opportunity.'
            ], 422);
        }

        // Create the application
        $application = JobApplication::create([
            'job_id' => $job->id,
            'user_id' => Auth::id(),
            'cover_letter' => $request->cover_letter,
            'resume_url' => $request->resume_url,
            'portfolio_url' => $request->portfolio_url,
            'status' => 'applied'
        ]);

        return response()->json([
            'success' => true,
            'data' => $application,
            'message' => 'Application submitted successfully.'
        ], 201);
    }

    /**
     * Display the specified application.
     */
    public function show(JobApplication $application)
    {
        // Check if the user is the owner of the application or an admin
        if (Auth::id() !== $application->user_id && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this application.'
            ], 403);
        }

        $application->load('job');

        return response()->json([
            'success' => true,
            'data' => $application
        ]);
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, JobApplication $application)
    {
        // Only admin can update application status
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this application.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:applied,reviewed,interviewed,accepted,rejected',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $application->update([
            'status' => $request->status,
            'feedback' => $request->feedback
        ]);

        return response()->json([
            'success' => true,
            'data' => $application,
            'message' => 'Application status updated successfully.'
        ]);
    }

    /**
     * Remove the specified application.
     */
    public function destroy(JobApplication $application)
    {
        // Check if the user is the owner of the application
        if (Auth::id() !== $application->user_id && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this application.'
            ], 403);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully.'
        ]);
    }
} 