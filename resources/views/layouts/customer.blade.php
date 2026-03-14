<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('components.pwa')

    <title>@yield('title', 'MeroTable — Order')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        @media (min-width: 640px) {
            main {
                min-height: calc(100dvh - 60px);
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <nav
        class="sticky top-0 z-50 flex items-center justify-between px-4 md:px-6 py-3 bg-[#0f172a] text-white border-b border-gray-800">


        <!-- Brand + Restro name -->
        <div class="flex items-center gap-3">
            <span class="text-xl md:text-2xl font-extrabold text-blue-600 tracking-tight leading-none">MeroTable</span>
            <div class="hidden md:block h-5 w-px bg-gray-200"></div>
            <span class="restroName hidden md:block text-sm font-semibold text-gray-200 truncate max-w-[160px]">Pemba
                Pasal</span>
        </div>

        <!-- Table chip -->
        <div class="flex items-center gap-1.5 border rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-400">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
            </svg>
            Table <span class="text-gray-400 font-bold ml-0.5" id="tableLabel">—</span>
        </div>

        <!-- Call staff -->
        <button id="callStaffBtn" onclick="callStaff()"
            class="flex items-center gap-2 px-3 py-2 bg-violet-600 hover:bg-violet-700 active:scale-95 text-white text-xs font-bold rounded-xl shadow-md shadow-violet-200 transition-all duration-150">
            <span class="bell-icon text-sm">🔔</span>
            <span class="hidden sm:inline">Call Staff</span>
        </button>

    </nav>

    <main class="p-4 md:p-8 min-h-[calc(100dvh-56px)] overflow-y-auto">
        @yield('content')
    </main>

    <!-- ====== TOAST ====== -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div id="toastBox"
            class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[260px]">
            <span id="toastIcon" class="text-lg"></span>
            <span id="toastMsg"></span>
        </div>
    </div>

    <script>
        function showToast(message, type = 'success') {
            console.log(message, type)
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
                info: {
                    bg: 'bg-blue-500',
                    icon: 'i'
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
