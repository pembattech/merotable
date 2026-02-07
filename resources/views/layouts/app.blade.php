<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        apiTest();
    </script>

    <style>
        body {
            display: none;
        }
    </style>


    <script src="{{ asset('js/auth-guard.js') }}"></script>
    <script>
        requireAuth();
    </script>


    <title>@yield('title', 'My Laravel App')</title>

    {{-- Assets (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('layouts.sidebar')

    <main class="flex-1 overflow-y-auto p-8">
        @yield('content')
    </main>

    {{-- @include('layouts.footer') --}}

    {{-- <script src="{{ asset('js/CategoryService.js') }}"></script> --}}

</body>

</html>
