<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialWebController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        abort_if($user->role !== 'lecturer', 403);

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:5120',
        ]);

        $course = Course::findOrFail($data['course_id']);
        abort_if($course->lecturer_id !== $user->id, 403);

        $path = $request->file('file')->store('materials', 'public');

        Material::create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'file_path' => $path,
        ]);

        return back()->with('success', 'Materi berhasil diupload.');
    }

    public function download($id)
    {
        $user = auth()->user();
        $material = Material::findOrFail($id);

        if ($user->role === 'student') {
            $enrolled = $user->coursesEnrolled()
                ->where('courses.id', $material->course_id)
                ->exists();

            abort_unless($enrolled, 403, 'You are not enrolled in this course');
        }

        abort_unless(Storage::disk('public')->exists($material->file_path), 404, 'File not found');

        $ext = pathinfo($material->file_path, PATHINFO_EXTENSION);
        $name = $material->title . ($ext ? "." . $ext : "");

        return Storage::disk('public')->download($material->file_path, $name);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $material = Material::findOrFail($id);

        // Hanya dosen pemilik course yang boleh hapus
        if ($user->role !== 'lecturer') {
            abort(403);
        }

        if ($material->course->lecturer_id !== $user->id) {
            abort(403);
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        // Hapus dari database
        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus');
    }
}
