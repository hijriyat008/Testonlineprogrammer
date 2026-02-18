<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'content'   => ['required', 'string'],
        ]);

        // harus enroll course
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $data['course_id'])
            ->exists();

        abort_unless($enrolled, 403, 'You are not enrolled in this course');

        Discussion::create([
            'course_id' => $data['course_id'],
            'user_id'   => $user->id,
            'content'   => $data['content'],
        ]);

        return response()->json(['message' => 'Discussion created'], 201);
    }

    public function reply(Request $request, $id)
    {
        $user = auth()->user();

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $discussion = Discussion::with('course')->findOrFail($id);

        // harus enroll course dari diskusi tsb
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $discussion->course_id)
            ->exists();

        abort_unless($enrolled, 403, 'You are not enrolled in this course');

        Reply::create([
            'discussion_id' => $discussion->id,
            'user_id'       => $user->id,
            'content'       => $data['content'],
        ]);

        return response()->json(['message' => 'Reply created'], 201);
    }
}
