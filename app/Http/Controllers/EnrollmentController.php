<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Enrollment::query();
        
        // Regular users can only see their own enrollments
        if (!in_array($user->role, ['admin', 'superAdmin'])) {
            $query->where('user_id', $user->id);
        } 
        // Admin can filter by user
        else if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by course if requested
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        
        // Filter by status if requested
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Include related models
        $query->with(['course:id,title,thumbnail,instructor_id', 'course.instructor:id,name']);
        
        // Sort enrollments
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Paginate the results
        $perPage = $request->get('per_page', 10);
        $enrollments = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Enroll a user in a course.
     */
    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Check if the user is already enrolled
        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ], 400);
        }
        
        // Check if the course is published
        if ($course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'This course is not available for enrollment'
            ], 400);
        }
        
        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress' => 0
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in the course',
            'data' => $enrollment
        ], 201);
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Enrollment $enrollment)
    {
        $user = Auth::user();
        
        // Users can only view their own enrollments
        if ($user->id !== $enrollment->user_id && !in_array($user->role, ['admin', 'superAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this enrollment'
            ], 403);
        }
        
        $enrollment->load(['course:id,title,thumbnail,description,instructor_id', 'course.instructor:id,name']);
        
        return response()->json([
            'success' => true,
            'data' => $enrollment
        ]);
    }

    /**
     * Update enrollment progress.
     */
    public function updateProgress(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        
        // Users can only update their own progress
        if ($user->id !== $enrollment->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this enrollment'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'progress' => 'required|numeric|min:0|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $enrollment->progress = $request->progress;
        
        // If progress is 100%, mark as completed
        if ($enrollment->progress == 100 && $enrollment->status == 'active') {
            $enrollment->status = 'completed';
            $enrollment->completed_at = now();
        }
        
        $enrollment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Mark enrollment as completed.
     */
    public function complete(Enrollment $enrollment)
    {
        $user = Auth::user();
        
        // Users can only complete their own enrollments
        if ($user->id !== $enrollment->user_id && !in_array($user->role, ['admin', 'superAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this enrollment'
            ], 403);
        }
        
        if ($enrollment->status == 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment is already marked as completed'
            ], 400);
        }
        
        $enrollment->status = 'completed';
        $enrollment->progress = 100;
        $enrollment->completed_at = now();
        $enrollment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Enrollment marked as completed',
            'data' => $enrollment
        ]);
    }

    /**
     * Cancel enrollment.
     */
    public function cancel(Enrollment $enrollment)
    {
        $user = Auth::user();
        
        // Users can only cancel their own enrollments
        if ($user->id !== $enrollment->user_id && !in_array($user->role, ['admin', 'superAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to cancel this enrollment'
            ], 403);
        }
        
        if ($enrollment->status == 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment is already cancelled'
            ], 400);
        }
        
        $enrollment->status = 'cancelled';
        $enrollment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Enrollment cancelled successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Remove the specified enrollment.
     */
    public function destroy(Enrollment $enrollment)
    {
        $user = Auth::user();
        
        // Only admins can delete enrollments
        if (!in_array($user->role, ['admin', 'superAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete enrollments'
            ], 403);
        }
        
        $enrollment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Enrollment deleted successfully'
        ]);
    }
} 