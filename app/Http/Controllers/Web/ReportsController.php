<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->role === 'lecturer', 403);

        // 1) Statistik jumlah mahasiswa per mata kuliah (yang dibuat dosen ini)
        $courses = Course::where('lecturer_id', $user->id)
            ->withCount('students') // pastikan relasi students() ada di model Course
            ->latest()
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'students_count' => $c->students_count,
            ]);

        // 2) Statistik tugas: sudah / belum dinilai (untuk tugas di course dosen ini)
        $assignmentIds = Assignment::whereIn('course_id', $courses->pluck('id'))->pluck('id');

        $totalSubmissions = Submission::whereIn('assignment_id', $assignmentIds)->count();
        $graded = Submission::whereIn('assignment_id', $assignmentIds)->whereNotNull('score')->count();
        $ungraded = Submission::whereIn('assignment_id', $assignmentIds)->whereNull('score')->count();
        $avgScore = Submission::whereIn('assignment_id', $assignmentIds)->whereNotNull('score')->avg('score');

        // 3) (Opsional) Statistik 1 mahasiswa tertentu via query ?student_id=#
        $studentStat = null;
        if ($request->filled('student_id')) {
            $student = User::where('role', 'student')->find($request->student_id);

            if ($student) {
                // ambil submission mahasiswa tsb yang tugasnya ada di course dosen ini
                $q = Submission::where('student_id', $student->id)
                    ->whereIn('assignment_id', $assignmentIds);

                $studentStat = [
                    'student' => $student,
                    'total' => (clone $q)->count(),
                    'graded' => (clone $q)->whereNotNull('score')->count(),
                    'ungraded' => (clone $q)->whereNull('score')->count(),
                    'avg' => (clone $q)->whereNotNull('score')->avg('score'),
                ];
            }
        }

        return view('reports.index', [
            'courses' => $courses,
            'totalSubmissions' => $totalSubmissions,
            'graded' => $graded,
            'ungraded' => $ungraded,
            'avgScore' => $avgScore,
            'studentStat' => $studentStat,
        ]);
    }
    // GET /reports/courses
    // Statistik jumlah mahasiswa per mata kuliah
    public function courses(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->role === 'lecturer', 403);

        // asumsi: Course punya lecturer_id
        $rows = Course::query()
            ->where('lecturer_id', $user->id)
            ->withCount('students') // relasi students() dari pivot enrollments
            ->orderByDesc('students_count')
            ->get(['id', 'name']);

        return response()->json([
            'data' => $rows
        ]);
    }

    // GET /reports/assignments
    // Statistik tugas yang sudah/belum dinilai
    public function assignments(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->role === 'lecturer', 403);

        // semua assignment milik dosen (via course)
        $assignments = Assignment::query()
            ->whereHas('course', fn($q) => $q->where('lecturer_id', $user->id))
            ->withCount([
                'submissions as total_submissions',
                'submissions as graded_submissions' => fn($q) => $q->whereNotNull('score'),
                'submissions as ungraded_submissions' => fn($q) => $q->whereNull('score'),
            ])
            ->orderByDesc('id')
            ->get(['id', 'course_id', 'title', 'deadline']);

        // agregat ringkas
        $summary = [
            'total_assignments' => $assignments->count(),
            'total_submissions' => (int) $assignments->sum('total_submissions'),
            'graded'            => (int) $assignments->sum('graded_submissions'),
            'ungraded'          => (int) $assignments->sum('ungraded_submissions'),
        ];

        return response()->json([
            'summary' => $summary,
            'data' => $assignments,
        ]);
    }

    // GET /reports/students/{id}
    // Statistik tugas & nilai mahasiswa tertentu (untuk dosen)
    public function student($id)
    {
        $user = auth()->user();
        abort_unless($user->role === 'lecturer', 403);

        $student = User::query()
            ->where('role', 'student')
            ->findOrFail($id);

        // ambil submission mahasiswa yang assignment-nya dari course dosen ini
        $submissions = $student->submissions()
            ->whereHas('assignment.course', fn($q) => $q->where('lecturer_id', $user->id))
            ->with(['assignment:id,course_id,title,deadline', 'assignment.course:id,name'])
            ->orderByDesc('id')
            ->get(['id', 'assignment_id', 'student_id', 'file_path', 'score', 'created_at']);

        $summary = [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ],
            'total_submissions' => $submissions->count(),
            'graded'   => $submissions->whereNotNull('score')->count(),
            'ungraded' => $submissions->whereNull('score')->count(),
            'avg_score' => $submissions->whereNotNull('score')->avg('score'),
            'min_score' => $submissions->whereNotNull('score')->min('score'),
            'max_score' => $submissions->whereNotNull('score')->max('score'),
        ];

        return response()->json([
            'summary' => $summary,
            'data' => $submissions,
        ]);
    }
}
