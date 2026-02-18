@extends('layouts.main')

@section('content')
    <div class="mx-auto w-full max-w-6xl px-6 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <div class="text-2xl font-bold text-slate-900">Reports</div>
                <div class="text-sm text-slate-500">Ringkasan statistik tugas & penilaian</div>
            </div>

            <a href="{{ url('/dashboard') }}"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        {{-- Cards --}}
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500">Total Submission</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totalSubmissions ?? 0 }}</div>
                <div class="mt-1 text-xs text-slate-400">Total file jawaban masuk</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500">Sudah Dinilai</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $graded ?? 0 }}</div>
                <div class="mt-1 text-xs text-slate-400">Submission dengan nilai</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500">Belum Dinilai</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $ungraded ?? 0 }}</div>
                <div class="mt-1 text-xs text-slate-400">Masih pending penilaian</div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold text-slate-500">Rata-rata Nilai</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">
                    {{ isset($avgScore) && $avgScore !== null ? number_format($avgScore, 1) : '-' }}
                </div>
                <div class="mt-1 text-xs text-slate-400">Dari submission yang sudah dinilai</div>
            </div>
        </div>

        {{-- Optional: hint / note --}}
        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-sm font-semibold text-slate-900">Catatan</div>
            <div class="mt-1 text-sm text-slate-600">
                Statistik ini dihitung dari semua tugas pada mata kuliah yang kamu ajar (role dosen).
            </div>
        </div>
    </div>
@endsection
