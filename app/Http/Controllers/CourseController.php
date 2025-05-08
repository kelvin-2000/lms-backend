<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(Request $request)
    {
        $query = Course::query()->with(['instructor', 'sections.lessons']);

        // Apply category filter
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Apply level filter
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $courses = $query->paginate(6);

        return response()->json([
            'data' => $courses,
            'message' => 'Courses retrieved successfully'
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Check if the user is an admin or instructor
        if (!in_array($user->role, ['admin', 'instructor'])) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to create a course'
            ], 403);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'long_description' => 'required|string',
            'thumbnail' => 'nullable|string',
            'thumbnail_url' => 'nullable|string', 
            'duration' => 'nullable|integer',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'category' => 'required|string|in:web_development,mobile_development,design,database,programming,data_science,artificial_intelligence,cloud_computing,cybersecurity,devops',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:draft,published',
            'last_update' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create the course
        $course = Course::create([
            'instructor_id' => $user->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'long_description' => $request->long_description,
            'thumbnail' => $request->thumbnail,
            'thumbnail_url' => $request->thumbnail_url ?? $request->thumbnail,
            'duration' => $request->duration,
            'level' => $request->level,
            'category' => $request->category,
            'price' => $request->price,
            'status' => $request->status,
            'last_update' => $request->last_update 
                ? date('Y-m-d H:i:s', strtotime($request->last_update)) 
                : now(),
        ]);
        
        // Load instructor relationship
        $course->load('instructor');
        
        // Return response
        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'long_description' => $course->long_description,
                'thumbnail' => $course->thumbnail,
                'thumbnail_url' => $course->thumbnail_url,
                'duration' => $course->duration,
                'level' => $course->level,
                'category' => $course->category,
                'price' => $course->price,
                'status' => $course->status,
                'last_update' => $course->last_update,
                'instructor' => [
                    'id' => $course->instructor->id,
                    'name' => $course->instructor->name,
                    'email' => $course->instructor->email,
                ]
            ]
        ], 201);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['instructor', 'videos', 'discussions.user']);

        $response = [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'longDescription' => $course->long_description,
            'thumbnailUrl' => $course->thumbnail_url,
            'level' => $course->level,
            'duration' => $course->duration,
            'totalVideos' => $course->total_videos,
            'price' => $course->price ?? 'Free',
            'rating' => (float) $course->rating,
            'ratingCount' => $course->rating_count,
            'studentsCount' => $course->students_count,
            'lastUpdate' => $course->last_update,
            'requirements' => $course->requirements ?? [],
            'whatYouWillLearn' => $course->what_you_will_learn ?? [],
            'instructor' => [
                'id' => $course->instructor->id,
                'name' => $course->instructor->name,
                'avatar' => $course->instructor->avatar,
                'title' => $course->instructor->title,
                'bio' => $course->instructor->bio,
            ],
            'videos' => $course->videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'duration' => $video->duration,
                    'videoUrl' => $video->video_url,
                    'isFree' => $video->is_free,
                ];
            }),
            'discussions' => $course->discussions->map(function ($discussion) {
                return [
                    'id' => $discussion->id,
                    'userId' => $discussion->user->id,
                    'userName' => $discussion->user->name,
                    'userAvatar' => $discussion->user->avatar,
                    'title' => $discussion->title,
                    'content' => $discussion->content,
                    'date' => $discussion->created_at->diffForHumans(),
                    'replies' => $discussion->reply_count,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Get featured courses
     */
    public function featured()
    {
        try {
            // Get published courses
            $featuredCourses = Course::where('status', 'published')
                ->with('instructor:id,name,email,avatar,bio')
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $featuredCourses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured courses: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get popular courses
     */
    public function popular()
    {
        // This assumes you're tracking enrollments to determine popularity
        $popularCourses = Course::where('status', 'published')
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->limit(6)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $popularCourses
        ]);
    }
    
    /**
     * Get latest courses
     */
    public function latest()
    {
        $latestCourses = Course::where('status', 'published')
            ->latest()
            ->limit(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $latestCourses
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        // Check if user is authorized to update this course
        $user = Auth::user();
        if (!in_array($user->role, ['admin']) && $course->instructor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this course'
            ], 403);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'long_description' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|nullable|string',
            'thumbnail_url' => 'sometimes|nullable|string', 
            'duration' => 'sometimes|nullable|integer',
            'level' => 'sometimes|required|string|in:beginner,intermediate,advanced',
            'category' => 'sometimes|required|string|in:web_development,mobile_development,design,database,programming,data_science,artificial_intelligence,cloud_computing,cybersecurity,devops',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:draft,published',
            'last_update' => 'sometimes|nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Prepare data for update
        $updateData = $request->only([
            'title', 'description', 'long_description', 'thumbnail', 
            'thumbnail_url', 'duration', 'level', 'category', 'price', 'status'
        ]);
        
        // Handle slug if title was updated
        if ($request->has('title')) {
            $updateData['slug'] = Str::slug($request->title);
        }
        
        // Handle last_update with proper datetime formatting
        if ($request->has('last_update')) {
            $updateData['last_update'] = date('Y-m-d H:i:s', strtotime($request->last_update));
        }
        
        // Update the course
        $course->update($updateData);
        
        // Load instructor relationship
        $course->load('instructor');
        
        // Return response
        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'description' => $course->description,
                'long_description' => $course->long_description,
                'thumbnail' => $course->thumbnail,
                'thumbnail_url' => $course->thumbnail_url,
                'duration' => $course->duration,
                'level' => $course->level,
                'category' => $course->category,
                'price' => $course->price,
                'status' => $course->status,
                'last_update' => $course->last_update,
                'instructor' => [
                    'id' => $course->instructor->id,
                    'name' => $course->instructor->name,
                    'email' => $course->instructor->email,
                ]
            ]
        ]);
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        // Check if user is authorized to delete this course
        $user = Auth::user();
        if (!in_array($user->role, ['admin']) && $course->instructor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this course'
            ], 403);
        }
        
        // Delete the course
        $course->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }

    /**
     * Get videos for a specific course
     */
    public function videos(Course $course)
    {
        // Load the videos relationship
        $course->load('videos');
        
        return response()->json([
            'success' => true,
            'data' => $course->videos,
            'message' => 'Course videos retrieved successfully'
        ]);
    }

    /**
     * Get discussions for a specific course
     */
    public function discussions(Course $course)
    {
        // Load the discussions relationship with their authors
        $course->load(['discussions.user']);
        
        return response()->json([
            'success' => true,
            'data' => $course->discussions,
            'message' => 'Course discussions retrieved successfully'
        ]);
    }
    
    /**
     * Get enrollments for a specific course
     */
    public function enrollments(Course $course)
    {
        // Check if user is authorized to view enrollments
        $user = Auth::user();
        if (!in_array($user->role, ['admin']) && $course->instructor_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view enrollments for this course'
            ], 403);
        }
        
        // Load the enrollments relationship with the enrolled users
        $course->load(['enrollments.user']);
        
        return response()->json([
            'success' => true,
            'data' => $course->enrollments,
            'message' => 'Course enrollments retrieved successfully'
        ]);
    }

    /**
     * Get related courses for a specific instructor.
     */
    public function instructorRelatedCourses(Request $request, $instructorId)
    {
        try {
            // Validate instructor exists
            $instructor = \App\Models\User::where('id', $instructorId)
                ->where('role', 'instructor')
                ->first();
                
            if (!$instructor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instructor not found'
                ], 404);
            }
            
            // Get published courses by the instructor
            $courses = Course::where('instructor_id', $instructorId)
                ->where('status', 'published')
                ->with('instructor:id,name,email,avatar,title,bio')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => [
                    'instructor' => [
                        'id' => $instructor->id,
                        'name' => $instructor->name,
                        'avatar' => $instructor->avatar,
                        'title' => $instructor->title,
                        'bio' => $instructor->bio
                    ],
                    'courses' => $courses
                ],
                'message' => 'Instructor related courses retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving instructor related courses: ' . $e->getMessage()
            ], 500);
        }
    }
} 