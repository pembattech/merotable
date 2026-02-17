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

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }
    </style>

    {{-- Assets (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('layouts.sidebar')

    <main class="flex-1 overflow-y-auto p-8">
        @yield('content')


    </main>

    <!-- ====== SUCCESS TOAST ====== -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div id="toastBox"
            class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[260px]">
            <span id="toastIcon" class="text-lg"></span>
            <span id="toastMsg"></span>
        </div>
    </div>

    {{-- @include('layouts.footer') --}}

    {{-- <script src="{{ asset('js/CategoryService.js') }}"></script> --}}

    <script>
        // ================= TOAST =================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const box = document.getElementById('toastBox');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMsg');

            const styles = {
                success: {
                    bg: 'bg-green-600',
                    icon: '✓'
                },
                error: {
                    bg: 'bg-red-500',
                    icon: '✕'
                },
                warning: {
                    bg: 'bg-orange-500',
                    icon: '!'
                },
            };
            const s = styles[type] || styles.success;

            box.className =
                `flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[260px] ${s.bg}`;
            icon.textContent = s.icon;
            msg.textContent = message;

            toast.classList.remove('hidden');
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => toast.classList.add('hidden'), 3000);
        }
    </script>
</body>

</html>
