<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('images/branding/merotable-logo-WHITE.png') }}" type="image/x-icon">
    {{-- <img class="logo h-8 w-8 object-contain" src="{{ asset('images/branding/merotable-logo-WHITE.png') }}"
        alt="Logo"> --}}

    {{-- @include('components.pwa') --}}

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

        /* scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #a9a9a9;
            border-radius: 5px;
        }

        /* field input */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        /* @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        } */

        /* fade-in stagger */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            opacity: 0;
            animation: fadeUp 0.5s ease forwards;
        }

        .d1 {
            animation-delay: 0.05s;
        }

        .d2 {
            animation-delay: 0.10s;
        }

        .d3 {
            animation-delay: 0.15s;
        }

        .d4 {
            animation-delay: 0.20s;
        }

        .d5 {
            animation-delay: 0.25s;
        }

        .d6 {
            animation-delay: 0.30s;
        }

        .d7 {
            animation-delay: 0.35s;
        }

        .d8 {
            animation-delay: 0.40s;
        }

        /* Hide the page until initialization is complete */
        #pageContent {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition:
                opacity 0.4s ease,
                transform 0.4s ease,
                visibility 0.4s;
        }

        #pageContent.page-loaded {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Accessibility: respect reduced-motion preferences */
        @media (prefers-reduced-motion: reduce) {

            #pageContent,
            .fade-up {
                opacity: 1;
                visibility: visible;
                transform: none;
                animation: none;
                transition: none;
            }
        }
    </style>

    {{-- Assets (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


{{-- Importing Helper --}}
@include('components.helper')

{{--
On mobile: body is a block column (flex-col) so the fixed topbar
(rendered inside the sidebar include) sits above everything.
The <main> gets pt-16 on mobile to clear the fixed topbar height,
    reset to pt-0 on md+ where the sidebar is static.
    --}}

    <body class="bg-gray-50 md:flex md:h-screen md:overflow-hidden">

        @include('layouts.sidebar')

        <main class="pageContent flex-1 overflow-y-auto p-6 md:p-8 pt-20 md:pt-8">
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
                console.log(message, type);

                const toast = document.getElementById('toast');
                const box = document.getElementById('toastBox');
                const icon = document.getElementById('toastIcon');
                const msg = document.getElementById('toastMsg');

                if (!toast || !box || !icon || !msg) {
                    console.error('Toast elements not found.');
                    return;
                }

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
                    }
                };

                const style = styles[type] || styles.success;

                // Remove previous background colors.
                box.classList.remove(
                    'bg-green-600',
                    'bg-red-500',
                    'bg-orange-500',
                    'bg-blue-500'
                );

                // Apply the correct background and layout classes.
                box.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[260px] ${style.bg}`;

                icon.textContent = style.icon;
                msg.textContent = message;

                toast.classList.remove('hidden');

                // Clear the previous timeout.
                clearTimeout(toast._timer);

                // Hide the toast after 3 seconds.
                toast._timer = setTimeout(() => {
                    toast.classList.add('hidden');
                }, 3000);
            }


            function formatDateTime(dateString) {
                if (!dateString) return '-';

                const date = new Date(dateString.replace(' ', 'T'));

                return date.toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            }

            function capitalize(s) {
                return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
            }
        </script>

    </body>

</html>