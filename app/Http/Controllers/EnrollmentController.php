<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
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
        
        // Get the previous students_count for debugging
        $previousCount = $course->students_count;
        
        // Try updating students_count with direct SQL update as a workaround
        try {
            \DB::table('courses')
                ->where('id', $course->id)
                ->increment('students_count');
                
            // Refresh course model to get the latest data
            $course->refresh();
            
            // Log the update for debugging
            \Log::info('Course enrollment - Course ID: ' . $course->id . 
                      ', Previous count: ' . $previousCount . 
                      ', New count: ' . $course->students_count);
        } catch (\Exception $e) {
            \Log::error('Error updating students_count: ' . $e->getMessage());
        }
        
        // Load the updated course data for response
        $enrollment->load(['course:id,title,thumbnail,description,instructor_id,students_count']);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in the course',
            'data' => $enrollment,
            'debug' => [
                'previous_count' => $previousCount,
                'new_count' => $course->students_count
            ]
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
        
        // Store previous status to check if we need to update students_count
        $previousStatus = $enrollment->status;
        
        $enrollment->status = 'cancelled';
        $enrollment->save();
        
        // Decrement the students_count only if the enrollment was active or completed
        if ($previousStatus === 'active' || $previousStatus === 'completed') {
            $course = Course::find($enrollment->course_id);
            if ($course && $course->students_count > 0) {
                $course->decrement('students_count');
            }
        }
        
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
        
        // Only decrement students_count if enrollment was active or completed
        if ($enrollment->status === 'active' || $enrollment->status === 'completed') {
            $course = Course::find($enrollment->course_id);
            if ($course && $course->students_count > 0) {
                $course->decrement('students_count');
            }
        }
        
        $enrollment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Enrollment deleted successfully'
        ]);
    }

    /**
     * Direct enrollment for students without approval.
     * This endpoint allows students to enroll in courses automatically.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentEnroll(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has student role
        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Only students can use this enrollment endpoint'
            ], 403);
        }
        
        // Validate request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $course = Course::find($request->course_id);
        
        // Check if the course exists
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }
        
        // Check if the course is published
        if ($course->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'This course is not available for enrollment'
            ], 400);
        }
        
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
        
        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress' => 0
        ]);
        
        // Get the previous students_count for debugging
        $previousCount = $course->students_count;
        
        // Try updating students_count with direct SQL update as a workaround
        try {
            \DB::table('courses')
                ->where('id', $course->id)
                ->increment('students_count');
                
            // Refresh course model to get the latest data
            $course->refresh();
            
            // Log the update for debugging
            \Log::info('Student enrollment - Course ID: ' . $course->id . 
                      ', Previous count: ' . $previousCount . 
                      ', New count: ' . $course->students_count);
        } catch (\Exception $e) {
            \Log::error('Error updating students_count: ' . $e->getMessage());
        }
        
        // Load course data to include in response
        $enrollment->load(['course:id,title,thumbnail,description,instructor_id,students_count', 'course.instructor:id,name']);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in the course',
            'data' => $enrollment,
            'debug' => [
                'previous_count' => $previousCount,
                'new_count' => $course->students_count
            ]
        ], 201);
    }

    /**
     * Check enrollment status for a user in a specific course.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
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
        if ($userId != $user->id && !in_array($user->role, ['admin', 'superAdmin', 'instructor'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to check enrollment status for other users'
            ], 403);
        }
        
        // If instructor, ensure they can only check enrollments for their courses
        if ($user->role === 'instructor' && $userId != $user->id) {
            $course = Course::find($request->course_id);
            if (!$course || $course->instructor_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only check enrollment status for your own courses'
                ], 403);
            }
        }
        
        // Find enrollment
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $request->course_id)
            ->first();
            
        if (!$enrollment) {
            return response()->json([
                'success' => true,
                'message' => 'User is not enrolled in this course',
                'data' => [
                    'is_enrolled' => false,
                    'enrollment' => null
                ]
            ]);
        }
        
        // Load related data for more context
        $enrollment->load(['course:id,title,thumbnail,description,instructor_id', 'course.instructor:id,name']);
        
        return response()->json([
            'success' => true,
            'message' => 'User is enrolled in this course',
            'data' => [
                'is_enrolled' => true,
                'enrollment' => $enrollment
            ]
        ]);
    }
} 