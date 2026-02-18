@if (session('success'))
    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        ✅ {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        ❌ {{ $errors->first() }}
    </div>
@endif
