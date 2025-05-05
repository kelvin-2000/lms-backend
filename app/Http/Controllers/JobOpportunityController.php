<?php

namespace App\Http\Controllers;

use App\Models\JobOpportunity;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobOpportunityController extends Controller
{
    /**
     * Display a listing of open job opportunities.
     */
    public function open(Request $request)
    {
        $query = JobOpportunity::where('status', 'open')
            ->where('deadline', '>=', now());

        // Search by title, company, or location
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by job type
        if ($request->has('type')) {
            $query->where('job_type', $request->type);
        }

        // Filter by work location type
        if ($request->has('location_type')) {
            $query->where('work_location_type', $request->location_type);
        }

        // Filter by experience level
        if ($request->has('experience')) {
            $query->where('experience_level', $request->experience);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $jobs = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Display a listing of all job opportunities.
     */
    public function index()
    {
        $jobs = JobOpportunity::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    /**
     * Store a newly created job opportunity in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'location' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,internship',
            'work_location_type' => 'sometimes|required|in:on-site,remote,hybrid',
            'experience_level' => 'sometimes|required|in:entry-level,mid-level,senior-level,senior,executive',
            'application_url' => 'nullable|url|max:255',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:open,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $job = JobOpportunity::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $job,
            'message' => 'Job opportunity created successfully'
        ], 201);
    }

    /**
     * Display the specified job opportunity.
     */
    public function show(JobOpportunity $jobOpportunity)
    {
        return response()->json([
            'success' => true,
            'data' => $jobOpportunity
        ]);
    }

    /**
     * Update the specified job opportunity in storage.
     */
    public function update(Request $request, JobOpportunity $jobOpportunity)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'job_type' => 'sometimes|required|in:full-time,part-time,contract,internship',
            'work_location_type' => 'sometimes|required|in:on-site,remote,hybrid',
            'experience_level' => 'sometimes|required|in:entry-level,mid-level,senior-level,senior,executive',
            'application_url' => 'nullable|url|max:255',
            'deadline' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:open,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Keep a copy of the original job for debugging
        $originalJob = $jobOpportunity->toArray();
        
        // Update the job
        $jobOpportunity->update($request->all());
        
        // Get a fresh instance of the model
        $updatedJob = $jobOpportunity->fresh();
        
        // Write to log file for debugging
        \Log::info('Job opportunity update', [
            'job_id' => $jobOpportunity->id,
            'updated_job_exists' => (bool)$updatedJob,
            'updated_job' => $updatedJob ? $updatedJob->toArray() : null
        ]);
        
        // Ensure we have data to return
        if (!$updatedJob) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving updated job data',
                'job_id' => $jobOpportunity->id,
                'original_job' => $originalJob
            ], 500);
        }

        // Return explicit array representation of the model
        return response()->json([
            'success' => true,
            'data' => $updatedJob->toArray(),
            'message' => 'Job opportunity updated successfully'
        ]);
    }

    /**
     * Remove the specified job opportunity from storage.
     */
    public function destroy(JobOpportunity $jobOpportunity)
    {
        $jobOpportunity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job opportunity deleted successfully'
        ]);
    }

    /**
     * Get applications for a specific job opportunity.
     */
    public function applications(JobOpportunity $jobOpportunity)
    {
        $applications = $jobOpportunity->applications()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }
} 