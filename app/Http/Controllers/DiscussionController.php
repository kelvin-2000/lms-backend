<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Course;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discussions = Discussion::with(['user', 'course'])->get();
        return response()->json($discussions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Check if the user is enrolled in the course
        $isEnrolled = auth()->user()->enrollments()->where('course_id', $request->course_id)->exists();
        $course = Course::findOrFail($request->course_id);
        $isInstructor = auth()->user()->id === $course->instructor_id;

        if (!$isEnrolled && !$isInstructor && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'You must be enrolled in the course to create discussions'], 403);
        }

        $discussion = Discussion::create([
            'course_id' => $request->course_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($discussion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion)
    {
        $discussion->load(['user', 'course', 'replies.user']);
        return response()->json($discussion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discussion $discussion)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        // Check if the user is the owner of the discussion or an admin
        if (auth()->id() !== $discussion->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to update this discussion'], 403);
        }

        $discussion->update($request->only(['title', 'content']));
        return response()->json($discussion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion)
    {
        // Check if the user is the owner of the discussion, the course instructor, or an admin
        $course = Course::findOrFail($discussion->course_id);
        $isInstructor = auth()->user()->id === $course->instructor_id;

        if (auth()->id() !== $discussion->user_id && !$isInstructor && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to delete this discussion'], 403);
        }

        $discussion->delete();
        return response()->json(null, 204);
    }

    /**
     * Get all replies for a discussion.
     */
    public function replies(Discussion $discussion)
    {
        $replies = DiscussionReply::with('user')
            ->where('discussion_id', $discussion->id)
            ->orderBy('created_at')
            ->get();
        
        return response()->json($replies);
    }
} 