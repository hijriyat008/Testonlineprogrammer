<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'E-Learning')</title>

    {{-- CSS dari public --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-slate-50 text-slate-800">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        <div class="flex-1">
            {{-- Navbar --}}
            @include('partials.navbar')

            <main class="p-4 md:p-8">
                @include('partials.flash')

                @yield('content')
            </main>
        </div>
    </div>

    {{-- JS dari public --}}
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
