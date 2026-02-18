<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-slate-50">
  <div class="min-h-screen grid place-items-center p-4">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-6">
      <h2 class="text-xl font-semibold mb-4">Register</h2>

      @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
          ❌ {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="/register" class="space-y-3">
        @csrf

        <div>
          <label class="text-sm font-medium">Nama</label>
          <input name="name" required class="mt-1 w-full rounded-2xl border px-4 py-3">
        </div>

        <div>
          <label class="text-sm font-medium">Email</label>
          <input name="email" type="email" required class="mt-1 w-full rounded-2xl border px-4 py-3">
        </div>

        <div>
          <label class="text-sm font-medium">Role</label>
          <select name="role" required class="mt-1 w-full rounded-2xl border px-4 py-3 bg-white">
            <option value="student">Mahasiswa</option>
            <option value="lecturer">Dosen</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium">Password</label>
          <input name="password" type="password" required class="mt-1 w-full rounded-2xl border px-4 py-3">
        </div>

        <div>
          <label class="text-sm font-medium">Confirm Password</label>
          <input name="password_confirmation" type="password" required class="mt-1 w-full rounded-2xl border px-4 py-3">
        </div>

        <button class="w-full rounded-2xl bg-slate-900 text-white py-3 font-semibold">
          Register
        </button>
      </form>

      <p class="mt-4 text-sm text-slate-600">
        Sudah punya akun? <a href="/login" class="font-semibold text-slate-900">Login</a>
      </p>
    </div>
  </div>
</body>
</html>
