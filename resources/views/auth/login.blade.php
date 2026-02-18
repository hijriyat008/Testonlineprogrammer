<!doctype html>
<html>
<head>
  <title>Login</title>
  <style>
    body{font-family:Arial;max-width:420px;margin:40px auto}
    input,select,button{width:100%;padding:10px;margin:6px 0}
    .card{border:1px solid #ddd;padding:20px;border-radius:10px}
    .err{color:red}
  </style>
</head>
<body>
  <div class="card">
    <h2>Login</h2>

    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    @if (session('success'))
      <div style="color:green">{{ session('success') }}</div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <p>Belum punya akun? <a href="{{ route('register') }}">Register</a></p>
  </div>
</body>
</html>
