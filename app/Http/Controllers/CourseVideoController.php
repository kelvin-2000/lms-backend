<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Http\Request;

class CourseVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = CourseVideo::with('course')->get();
        return response()->json($videos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|string|max:255',
            'duration' => 'nullable|integer',
            'order' => 'nullable|integer',
        ]);

        // Check if the user is the instructor of the course or an admin
        $course = Course::findOrFail($request->course_id);
        if (auth()->user()->id !== $course->instructor_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to add videos to this course'], 403);
        }

        $video = CourseVideo::create($request->all());
        return response()->json($video, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseVideo $courseVideo)
    {
        return response()->json($courseVideo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseVideo $courseVideo)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'sometimes|required|string|max:255',
            'duration' => 'nullable|integer',
            'order' => 'nullable|integer',
        ]);

        // Check if the user is the instructor of the course or an admin
        $course = Course::findOrFail($courseVideo->course_id);
        if (auth()->user()->id !== $course->instructor_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to update videos for this course'], 403);
        }

        $courseVideo->update($request->all());
        return response()->json($courseVideo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseVideo $courseVideo)
    {
        // Check if the user is the instructor of the course or an admin
        $course = Course::findOrFail($courseVideo->course_id);
        if (auth()->user()->id !== $course->instructor_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to delete videos from this course'], 403);
        }

        $courseVideo->delete();
        
        // Return a 200 OK with a success message instead of 204 No Content
        return response()->json([
            'success' => true,
            'message' => 'Course video deleted successfully'
        ], 200);
    }

    /**
     * Reorder videos in a course.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'videos' => 'required|array',
            'videos.*.id' => 'required|exists:course_videos,id',
            'videos.*.order' => 'required|integer',
        ]);

        // Check if all videos belong to the same course and the user is authorized
        $firstVideo = CourseVideo::findOrFail($request->videos[0]['id']);
        $course = Course::findOrFail($firstVideo->course_id);
        
        if (auth()->user()->id !== $course->instructor_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to reorder videos for this course'], 403);
        }

        foreach ($request->videos as $videoData) {
            $video = CourseVideo::findOrFail($videoData['id']);
            
            // Ensure all videos belong to the same course
            if ($video->course_id !== $firstVideo->course_id) {
                return response()->json([
                    'message' => 'All videos must belong to the same course'
                ], 400);
            }
            
            $video->update(['order' => $videoData['order']]);
        }

        return response()->json(['message' => 'Videos reordered successfully']);
    }
} 