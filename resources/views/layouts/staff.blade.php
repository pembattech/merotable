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

<body class="bg-gray-50 overflow-hidden">

    <div class="flex justify-between items-center bg-gray-900 text-white">

        <div class="p-4 text-2xl font-extrabold text-blue-400 tracking-tight">
            MeroTable
        </div>

        <nav class="flex items-center gap-4 pr-3">

            <p class="user-name text-sm font-medium"></p>

            <div>

                <a href="javascript:void(0)" onclick="logout()"
                    class="flex items-center p-3 text-red-400 hover:bg-red-500/10 hover:text-red-500 rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
                    </svg>
                    Logout
                </a>
            </div>
        </nav>
    </div>

    <main class="p-8 h-screen overflow-y-auto">
        @yield('content')
    </main>

    {{-- @include('layouts.footer') --}}

    {{-- <script src="{{ asset('js/CategoryService.js') }}"></script> --}}


    <script>
        let userName = localStorage.getItem('user_name');
        if (userName) {
            document.querySelector('.user-name').textContent = userName;
        }


        async function logout() {
            const token = localStorage.getItem('auth_token');

            try {
                await fetch('/api/v1/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
            } catch (e) {
                console.warn('Logout request failed, clearing session anyway');
            }

            localStorage.clear()

            window.location.href = '/auth';
        }
    </script>

</body>

</html>
