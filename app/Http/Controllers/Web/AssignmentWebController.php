<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentWebController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->role === 'lecturer', 403);

        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['required', 'date'],
            'file' => ['nullable', 'file', 'max:20480'], // 20MB
        ]);

        $course = Course::findOrFail($data['course_id']);
        abort_unless($course->lecturer_id === $user->id, 403);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $fileName = $uploaded->getClientOriginalName();
            $filePath = $uploaded->store('assignments', 'public');
        }

        Assignment::create([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        return back()->with('success', 'Tugas berhasil dibuat');
    }
    public function download($id)
    {
        $user = auth()->user();
        $assignment = Assignment::with('course')->findOrFail($id);

        // dosen pemilik course boleh download
        if ($user->role === 'lecturer') {
            abort_unless($assignment->course->lecturer_id === $user->id, 403);
        }

        // mahasiswa harus enroll course
        if ($user->role === 'student') {
            $enrolled = $user->coursesEnrolled()
                ->where('courses.id', $assignment->course_id)
                ->exists();
            abort_unless($enrolled, 403);
        }

        abort_unless($assignment->file_path, 404);
        abort_unless(Storage::disk('public')->exists($assignment->file_path), 404);

        $downloadName = $assignment->file_name ?: basename($assignment->file_path);

        return Storage::disk('public')->download($assignment->file_path, $downloadName);
    }
}
