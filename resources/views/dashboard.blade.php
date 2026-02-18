@extends('layouts.main')

@section('title', 'Dashboard')
@section('page_title', 'Courses')
@section('page_subtitle', 'Kelola & ikuti mata kuliah')

@section('content')
    @if ($user->role === 'lecturer')
        <div class="rounded-3xl border border-slate-200 bg-white p-5">
            <h2 class="font-semibold">Tambah Mata Kuliah</h2>
            <form method="POST" action="/courses" class="mt-4 grid gap-3 md:grid-cols-3">
                @csrf
                <div class="md:col-span-1">
                    <label class="text-sm font-medium">Nama</label>
                    <input name="name" required
                        class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                        placeholder="Contoh: Pemrograman Web">
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Deskripsi (opsional)</label>
                    <input name="description"
                        class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
                        placeholder="Contoh: Laravel API + best practice">
                </div>

                <div class="md:col-span-3">
                    <button
                        class="rounded-2xl bg-slate-900 px-4 py-3 text-white font-semibold hover:bg-slate-800 transition">
                        + Simpan
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($courses as $c)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 hover:shadow-sm transition">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-lg">{{ $c->name }}</h3>
                        <p class="text-sm text-slate-500">Dosen: {{ $c->lecturer?->name }}</p>
                    </div>
                    <span
                        class="inline-flex items-center rounded-2xl bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                        #{{ $c->id }}
                    </span>
                </div>

                <p class="mt-3 text-sm text-slate-600">
                    {{ $c->description ?? 'Tidak ada deskripsi.' }}
                </p>

                <div class="mt-5 flex items-center gap-2">
                    @if ($user->role === 'student')
                        @if (in_array($c->id, $enrolledIds))
                            <button disabled
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500">
                                ✅ Sudah Enroll
                            </button>
                        @else
                            <form method="POST" action="/courses/{{ $c->id }}/enroll" class="w-full">
                                @csrf
                                <button
                                    class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                    Enroll
                                </button>
                            </form>
                        @endif
                    @endif

                    @if ($user->role === 'lecturer' && $c->lecturer_id === $user->id)
                        <button type="button"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold hover:bg-slate-50 transition"
                            onclick="openEditCourse({{ $c->id }}, @js($c->name), @js($c->description))">
                            Edit
                        </button>

                        <form method="POST" action="/courses/{{ $c->id }}/delete" class="w-full">
                            @csrf
                            <button onclick="return confirm('Hapus mata kuliah ini?')"
                                class="w-full rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100 transition">
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>

                <div class="mt-4 space-y-2">
                    <div class="text-sm font-semibold text-slate-700">Materi</div>
                    @if ($user->role === 'lecturer')
                        <form action="/materials" method="POST" enctype="multipart/form-data" class="mt-2 space-y-2">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $c->id }}">

                            <input type="text" name="title" placeholder="Judul materi"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">

                            <input type="file" name="file" class="w-full text-sm">

                            <button
                                class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                Upload
                            </button>
                        </form>
                    @endif
                    @if (($c->materials ?? collect())->count() === 0)
                        <div class="text-sm text-slate-500">Belum ada materi.</div>
                    @else
                        <div class="space-y-2">
                            @foreach ($c->materials as $m)
                                <div
                                    class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-3 py-2">
                                    <div class="text-sm">
                                        <div class="font-medium">{{ $m->title }}</div>
                                        <div class="text-xs text-slate-500">{{ $m->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    @if ($user->role === 'lecturer')
                                        <form method="POST" action="{{ route('materials.destroy', $m->id) }}"
                                            onclick="return confirm('Yakin mau hapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="rounded-xl bg-red-500 px-3 py-2 text-xs font-semibold text-white hover:bg-red-600">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                    @if ($user->role === 'student')
                                        <a href="{{ route('materials.download', $m->id) }}"
                                            class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                            Download
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-4 space-y-2">
                        <div class="text-sm font-semibold text-slate-700">Tugas</div>

                        @if (($c->assignments ?? collect())->count() === 0)
                            <div class="text-sm text-slate-500">Belum ada tugas.</div>
                        @else
                            <div class="space-y-2">
                                @foreach ($c->assignments as $a)
                                    <div class="rounded-2xl border border-slate-200 bg-white p-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="font-semibold">{{ $a->title }}</div>
                                                <div class="text-sm text-slate-600">{{ $a->description }}</div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    Deadline: {{ $a->deadline->format('d M Y H:i') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- STUDENT: submit --}}
                                        @if ($user->role === 'student')
                                            @if ($a->file_path)
                                                <a href="{{ route('assignments.download', $a->id) }}"
                                                    class="mt-2 inline-flex items-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold hover:bg-slate-50">
                                                    Download Soal
                                                </a>
                                            @endif
                                            <form class="mt-3 flex items-center gap-2" method="POST"
                                                action="{{ route('submissions.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="assignment_id" value="{{ $a->id }}">
                                                <input type="file" name="file" required class="block w-full text-sm">
                                                <button
                                                    class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                                    Upload Jawaban
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- LECTURER: create assignment --}}
                        @if ($user->role === 'lecturer')
                            <div class="mt-4 border-t pt-4">
                                <div class="text-sm font-semibold text-slate-700">Tugas</div>

                                <form action="/assignments" method="POST" enctype="multipart/form-data"
                                    class="mt-3 grid gap-3">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $c->id }}" />

                                    <input name="title" placeholder="Judul tugas"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />

                                    <textarea name="description" placeholder="Deskripsi"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>

                                    <input type="datetime-local" name="deadline"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />

                                    <input type="file" name="file" class="text-sm" />

                                    <button
                                        class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                                        + Buat Tugas
                                    </button>
                                </form>
                            </div>
                        @endif
                        <div class="mt-4 space-y-2">
                            @forelse($c->assignments as $a)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="font-semibold">{{ $a->title }}</div>
                                            <div class="text-sm text-slate-600">{{ $a->description }}</div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                Deadline: {{ \Carbon\Carbon::parse($a->deadline)->format('d M Y H:i') }}
                                            </div>

                                            @if ($a->file_path)
                                                <a href="/assignments/{{ $a->id }}/download"
                                                    class="mt-2 inline-block text-sm font-semibold text-slate-900 underline">
                                                    Download Soal
                                                </a>
                                            @endif
                                        </div>

                                        @if ($user->role === 'lecturer')
                                            <form action="/assignments/{{ $a->id }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button
                                                    class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Mahasiswa: upload jawaban --}}
                                    @if ($user->role === 'student')
                                        <div class="mt-4 border-t pt-3">
                                            <form action="/submissions" method="POST" enctype="multipart/form-data"
                                                class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="assignment_id"
                                                    value="{{ $a->id }}" />
                                                <input type="file" name="file" class="text-sm" />
                                                <button
                                                    class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                                    Upload Jawaban
                                                </button>
                                            </form>

                                            @php
                                                $mySub = $a->submissions->where('student_id', $user->id)->first();
                                            @endphp

                                            @if ($mySub)
                                                <div class="mt-2 text-sm text-slate-600">
                                                    ✅ Sudah submit: {{ $mySub->file_name }}
                                                    <a class="ml-2 underline"
                                                        href="/submissions/{{ $mySub->id }}/download">Download</a>
                                                    @if (!is_null($mySub->score))
                                                        <span class="ml-2 font-semibold text-slate-900">Nilai:
                                                            {{ $mySub->score }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Dosen: lihat submission + nilai (simple list) --}}
                                    @if ($user->role === 'lecturer')
                                        <div class="mt-4 border-t pt-3 space-y-2">
                                            <div class="text-sm font-semibold text-slate-700">Jawaban Mahasiswa</div>
                                            @forelse($a->submissions as $s)
                                                <div
                                                    class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2">
                                                    <div class="text-sm">
                                                        <div class="font-medium">
                                                            {{ $s->student->name ?? 'User#' . $s->student_id }}</div>
                                                        <a class="text-xs underline"
                                                            href="/submissions/{{ $s->id }}/download">{{ $s->file_name }}</a>
                                                        <a href="{{ route('submissions.download', $s->id) }}"
                                                            class="text-sm underline text-slate-700">
                                                            Download Jawaban
                                                        </a>
                                                    </div>

                                                    <form action="/submissions/{{ $s->id }}/grade" method="POST"
                                                        class="flex items-center gap-2">
                                                        @csrf
                                                        <input name="score" value="{{ $s->score }}"
                                                            placeholder="0-100"
                                                            class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-sm" />
                                                        <button
                                                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">
                                                            Simpan
                                                        </button>
                                                    </form>
                                                </div>
                                            @empty
                                                <div class="text-sm text-slate-500">Belum ada submission.</div>
                                            @endforelse
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-sm text-slate-500">Belum ada tugas.</div>
                            @endforelse
                        </div>
                        {{-- DISKUSI --}}
                        <div class="mt-6 border-t border-slate-200 pt-4">
                            <div class="text-sm font-semibold text-slate-700">Diskusi</div>

                            {{-- Form buat diskusi baru (student & lecturer) --}}
                            <form action="{{ route('discussions.store') }}" method="POST" class="mt-3 space-y-2">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $c->id }}">

                                <textarea name="content" rows="2" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"
                                    placeholder="Tulis pertanyaan / diskusi..."></textarea>

                                <button type="submit"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                    Kirim Diskusi
                                </button>
                            </form>

                            {{-- List diskusi --}}
                            @php
                                $discussions = $c->discussions ?? collect();
                            @endphp

                            @if ($discussions->count() === 0)
                                <div class="mt-3 text-sm text-slate-500">Belum ada diskusi.</div>
                            @else
                                <div class="mt-4 space-y-4">
                                    @foreach ($discussions as $d)
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-900">
                                                        {{ $d->user->name ?? 'User' }}
                                                        <span class="ml-2 text-xs font-normal text-slate-500">
                                                            {{ optional($d->created_at)->format('d M Y H:i') }}
                                                        </span>
                                                    </div>
                                                    <div class="mt-1 text-sm text-slate-700 whitespace-pre-line">
                                                        {{ $d->content }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Replies list --}}
                                            @php
                                                $replies = $d->replies ?? collect();
                                            @endphp

                                            <div class="mt-4 space-y-3">
                                                @foreach ($replies as $r)
                                                    <div class="rounded-xl bg-slate-50 p-3">
                                                        <div class="text-xs font-semibold text-slate-800">
                                                            {{ $r->user->name ?? 'User' }}
                                                            <span class="ml-2 font-normal text-slate-500">
                                                                {{ optional($r->created_at)->format('d M Y H:i') }}
                                                            </span>
                                                        </div>
                                                        <div class="mt-1 text-sm text-slate-700 whitespace-pre-line">
                                                            {{ $r->content }}
                                                        </div>
                                                    </div>
                                                @endforeach

                                                {{-- Reply form per diskusi --}}
                                                <form action="{{ route('replies.store', $d->id) }}" method="POST"
                                                    class="mt-3 flex gap-2">
                                                    @csrf
                                                    <input name="content" required
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                                                        placeholder="Tulis balasan...">
                                                    <button type="submit"
                                                        class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                                        Balas
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <!-- Modal Edit Course -->
        <div id="editCourseModal" class="hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/30" onclick="closeEditCourse()"></div>

            <div class="relative mx-auto mt-24 w-full max-w-lg rounded-3xl bg-white border border-slate-200 p-6 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold">Edit Mata Kuliah</h3>
                        <p class="text-sm text-slate-500">Ubah nama/deskripsi course.</p>
                    </div>
                    <button class="h-10 w-10 rounded-xl border border-slate-200 hover:bg-slate-50"
                        onclick="closeEditCourse()">✕</button>
                </div>

                <form id="editCourseForm" method="POST" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input id="editCourseName" name="name" required
                            class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                    </div>

                    <div>
                        <label class="text-sm font-medium">Deskripsi (opsional)</label>
                        <input id="editCourseDesc" name="description"
                            class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-900/20">
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button type="button" onclick="closeEditCourse()"
                            class="w-full rounded-2xl border border-slate-200 bg-white py-3 font-semibold hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button
                            class="w-full rounded-2xl bg-slate-900 text-white py-3 font-semibold hover:bg-slate-800 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
