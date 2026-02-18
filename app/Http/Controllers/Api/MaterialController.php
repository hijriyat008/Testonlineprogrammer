<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // POST /materials (lecturer)
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'lecturer') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
            'file'      => 'required|file|max:5120', // 5MB
        ]);

        $course = Course::findOrFail($data['course_id']);

        // pastikan dosen hanya upload ke course miliknya
        if ($course->lecturer_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // simpan file ke storage public
        $path = $request->file('file')->store('materials', 'public');

        $material = Material::create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'file_path' => $path,
        ]);

        return response()->json([
            'message' => 'Material uploaded',
            'data' => $material,
            'url' => asset('storage/' . $material->file_path),
        ], 201);
    }

    // GET /materials/{id}/download (student)
    public function download($id)
    {
        $user = auth()->user();

        $material = Material::findOrFail($id);

        // student harus sudah enroll
        if ($user->role === 'student') {
            $enrolled = $user->coursesEnrolled()
                ->where('courses.id', $material->course_id)
                ->exists();

            if (!$enrolled) {
                return response()->json(['message' => 'You are not enrolled in this course'], 403);
            }
        }

        if (!Storage::disk('public')->exists($material->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // kasih nama file yang proper (biar ada extension)
        $downloadName = $material->title;
        $ext = pathinfo($material->file_path, PATHINFO_EXTENSION);
        if ($ext && !str_ends_with($downloadName, ".$ext")) {
            $downloadName .= ".$ext";
        }

        return Storage::disk('public')->download($material->file_path, $downloadName);
    }
}
