<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // GET /courses (semua user login bisa lihat)
    public function index()
    {
        $courses = Course::with(
            'lecturer:id,name,email',
            'discussions.user',
            'discussions.replies.user'
        )->latest()->get();
        return response()->json($courses);
    }

    // POST /courses (dosen)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'lecturer_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Course created',
            'data' => $course
        ], 201);
    }

    // PUT /courses/{id} (dosen pemilik)
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden - not your course'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Course updated',
            'data' => $course
        ]);
    }

    // DELETE /courses/{id} (dosen pemilik)
    public function destroy(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        if ($course->lecturer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden - not your course'], 403);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted']);
    }

    // POST /courses/{id}/enroll (mahasiswa)
    public function enroll(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // pastikan mahasiswa
        if ($request->user()->role !== 'student') {
            return response()->json(['message' => 'Only students can enroll'], 403);
        }

        // attach kalau belum ada
        $request->user()->coursesEnrolled()->syncWithoutDetaching([$course->id]);

        return response()->json([
            'message' => 'Enrolled successfully',
            'course' => $course->only(['id', 'name'])
        ]);
    }
}
