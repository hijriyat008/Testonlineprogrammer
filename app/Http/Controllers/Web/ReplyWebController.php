<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\Reply;
use Illuminate\Http\Request;

class ReplyWebController extends Controller
{
    public function store(Request $request, $discussionId)
    {
        $user = auth()->user();

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $discussion = Discussion::with('course')->findOrFail($discussionId);

        // wajib enroll ke course diskusi tsb
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $discussion->course_id)
            ->exists();

        abort_unless($enrolled, 403);

        Reply::create([
            'discussion_id' => $discussion->id,
            'user_id'       => $user->id,
            'content'       => $data['content'],
        ]);

        return back()->with('success', 'Balasan terkirim');
    }
}
