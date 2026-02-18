<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionWebController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'content'   => ['required', 'string'],
        ]);

        // wajib enroll biar boleh diskusi (student & lecturer)
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $data['course_id'])
            ->exists();

        abort_unless($enrolled, 403);

        Discussion::create([
            'course_id' => $data['course_id'],
            'user_id'   => $user->id,
            'content'   => $data['content'],
        ]);

        return back()->with('success', 'Diskusi berhasil dikirim');
    }
}
