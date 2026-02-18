<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionWebController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->role === 'student', 403);

        $data = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'file' => ['required', 'file', 'max:10240'], // 10MB
        ]);

        $assignment = Assignment::with('course')->findOrFail($data['assignment_id']);

        // harus enroll course
        $enrolled = $user->coursesEnrolled()
            ->where('courses.id', $assignment->course_id)
            ->exists();
        abort_unless($enrolled, 403);

        // deadline check
        abort_if(now()->greaterThan($assignment->deadline), 422, 'Deadline sudah lewat');

        $path = $request->file('file')->store('submissions', 'public');
        $uploaded = $request->file('file');
        $path = $uploaded->store('submissions', 'public');
        $fileName = $uploaded->getClientOriginalName();

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $user->id],
            ['file_path' => $path]
        );

        return back()->with('success', 'Jawaban berhasil dikirim');
    }

    public function grade(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless($user->role === 'lecturer', 403);

        $data = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $submission = Submission::with('assignment.course')->findOrFail($id);

        // cuma dosen pemilik course
        abort_unless($submission->assignment->course->lecturer_id === $user->id, 403);

        $submission->update(['score' => $data['score']]);

        return back()->with('success', 'Nilai tersimpan');
    }

    public function download($id)
    {
        $user = auth()->user();
        $submission = Submission::with('assignment.course')->findOrFail($id);

        // dosen pemilik course boleh download semua submission di course dia
        if ($user->role === 'lecturer') {
            abort_unless($submission->assignment->course->lecturer_id === $user->id, 403);
        }
        // mahasiswa hanya boleh download submission dia sendiri
        elseif ($user->role === 'student') {
            abort_unless($submission->student_id === $user->id, 403);
        } else {
            abort(403);
        }

        abort_unless(Storage::disk('public')->exists($submission->file_path), 404);

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->file_name ?? basename($submission->file_path)
        );
    }
}
