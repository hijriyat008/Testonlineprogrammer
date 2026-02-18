<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseWebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $courses = Course::with(['lecturer', 'materials', 'assignments.submissions.student', 'discussions' => function ($q) {
            $q->latest()
                ->with([
                    'user:id,name',
                    'replies' => function ($qr) {
                        $qr->latest()->with('user:id,name');
                    },
                ]);
        },])->latest()->get();


        $enrolledIds = [];
        if ($user->role === 'student') {
            $enrolledIds = $user->coursesEnrolled()->pluck('courses.id')->toArray();
        }

        return view('dashboard', compact('user', 'courses', 'enrolledIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
        ]);

        Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'lecturer_id' => $request->user()->id
        ]);

        return back()->with('success', 'Mata kuliah dibuat.');
    }

    public function enroll(Request $request, $id)
    {
        $request->user()->coursesEnrolled()->syncWithoutDetaching([$id]);
        return back()->with('success', 'Berhasil enroll.');
    }

    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($course->lecturer_id !== $request->user()->id) {
            abort(403);
        }

        $course->delete();
        return back()->with('success', 'Mata kuliah dihapus.');
    }

    public function update(Request $request, $id)
    {
        $course = \App\Models\Course::findOrFail($id);

        // cuma dosen pemilik course yang boleh edit
        if ($course->lecturer_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($data);

        return back()->with('success', 'Mata kuliah berhasil diupdate.');
    }
}
