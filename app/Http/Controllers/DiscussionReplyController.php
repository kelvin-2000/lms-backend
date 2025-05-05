<?php

namespace App\Http\Controllers;

use App\Models\DiscussionReply;
use App\Models\Discussion;
use App\Models\Course;
use Illuminate\Http\Request;

class DiscussionReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $replies = DiscussionReply::with(['user', 'discussion'])->get();
        return response()->json($replies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'discussion_id' => 'required|exists:discussions,id',
            'content' => 'required|string',
        ]);

        $discussion = Discussion::findOrFail($request->discussion_id);
        $course = Course::findOrFail($discussion->course_id);

        // Check if the user is enrolled in the course or is the instructor or an admin
        $isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->exists();
        $isInstructor = auth()->user()->id === $course->instructor_id;

        if (!$isEnrolled && !$isInstructor && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'You must be enrolled in the course to reply to discussions'], 403);
        }

        $reply = DiscussionReply::create([
            'discussion_id' => $request->discussion_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json($reply, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DiscussionReply $discussionReply)
    {
        $discussionReply->load(['user', 'discussion']);
        return response()->json($discussionReply);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiscussionReply $discussionReply)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        // Check if the user is the owner of the reply or an admin
        if (auth()->id() !== $discussionReply->user_id && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to update this reply'], 403);
        }

        $discussionReply->update($request->only(['content']));
        return response()->json($discussionReply);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiscussionReply $discussionReply)
    {
        // Check if the user is the owner of the reply, the discussion owner, the course instructor, or an admin
        $discussion = Discussion::findOrFail($discussionReply->discussion_id);
        $course = Course::findOrFail($discussion->course_id);
        
        $isDiscussionOwner = auth()->id() === $discussion->user_id;
        $isInstructor = auth()->user()->id === $course->instructor_id;

        if (auth()->id() !== $discussionReply->user_id && !$isDiscussionOwner && !$isInstructor && !auth()->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to delete this reply'], 403);
        }

        $discussionReply->delete();
        return response()->json(null, 204);
    }
} 