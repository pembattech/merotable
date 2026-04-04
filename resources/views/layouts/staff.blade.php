<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('components.pwa')

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Scrollbar ── */
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

        /* ── Shared field input ── */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            font-family: inherit;
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

        @keyframes slideUp {
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
        }

        /* ════════════════════════════════════════════════════════
           MOBILE DRAWER — smooth slide + fade
           ════════════════════════════════════════════════════════

           The root problem with the old code:
             The parent wrapper used display:none ↔ display:block,
             which immediately removes elements from the render tree —
             so the CSS transition on the child panel never fires.

           Fix: keep BOTH the backdrop and the panel permanently
           in the DOM. Control visibility with opacity + pointer-events
           (backdrop) and translateX (panel). That way transitions
           fire in both open AND close directions.
        ════════════════════════════════════════════════════════ */

        /* Backdrop — always present, invisible + non-interactive when closed */
        #mobileMenuBackdrop {
            position: fixed;
            inset: 0;
            z-index: 59;
            background: rgba(0, 0, 0, 0.55);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.28s ease;
        }

        #mobileMenuBackdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Panel — always present, slid off-screen when closed */
        #mobileMenuPanel {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: min(280px, 85vw);
            z-index: 60;
            background: #0f172a;
            border-right: 1px solid #1e293b;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 320ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        #mobileMenuPanel.open {
            transform: translateX(0);
        }

        /* ── Hamburger → × morphing icon ── */
        .ham-line {
            display: block;
            width: 20px;
            height: 2px;
            background: #d1d5db;
            border-radius: 2px;
            transition: transform 0.25s ease, opacity 0.2s ease;
            transform-origin: center;
        }

        /* When button has .open — morph three lines into × */
        #hamburger.open .ham-line:nth-child(1) {
            transform: translateY(6px) rotate(45deg);
        }

        #hamburger.open .ham-line:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        #hamburger.open .ham-line:nth-child(3) {
            transform: translateY(-6px) rotate(-45deg);
        }
    </style>
</head>

<body class="bg-gray-50">

    {{-- ══════════════════════════════════════════
         TOP NAVBAR
    ══════════════════════════════════════════ --}}
    <nav
        class="sticky top-0 z-50 flex items-center justify-between px-4 md:px-6 py-3 bg-[#0f172a] text-white border-b border-gray-800">

        {{-- Left: Hamburger + Logo + Divider + Restro name --}}
        <div class="flex items-center gap-3">

            {{-- Animated hamburger button — mobile only --}}
            <button id="hamburger"
                class="md:hidden flex flex-col gap-[5px] justify-center p-1.5 rounded-lg hover:bg-gray-800 transition"
                onclick="toggleMobileMenu()" aria-label="Toggle navigation menu" aria-expanded="false"
                aria-controls="mobileMenuPanel">
                <span class="ham-line"></span>
                <span class="ham-line"></span>
                <span class="ham-line"></span>
            </button>

            <img class="logo w-28 sm:w-32 md:w-40 lg:w-52"  src="{{ asset('storage/logo/merotable-logo-gray.png') }}" alt="Logo">
            <div class="hidden md:block h-6 w-px bg-gray-700"></div>
            <p class="restroName hidden md:block text-sm md:text-lg font-medium text-gray-200 truncate max-w-[160px]">
                loading…</p>
        </div>

        {{-- Centre: nav links — desktop only --}}
        <div class="hidden md:flex items-center">
            <a href="{{ route('staff.dashboard') }}"
                class="px-3 py-2 rounded-lg text-sm font-medium
                {{ request()->routeIs('staff.dashboard') ? 'text-white underline font-semibold' : 'text-gray-400 hover:bg-gray-800 transition' }}">
                Dashboard
            </a>
            <a href="{{ route('staff.billing') }}"
                class="px-3 py-2 rounded-lg text-sm font-medium
                {{ request()->routeIs('staff.billing*') ? 'text-white underline font-semibold' : 'text-gray-400 hover:bg-gray-800 transition' }}">
                Billing
            </a>
        </div>

        {{-- Right: User info + Logout --}}
        <div class="flex items-center gap-2 md:gap-6">
            <div class="hidden sm:block text-right">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold">Logged in as</p>
                <p class="user-name text-xs md:text-sm font-medium text-white truncate max-w-[120px]"></p>
            </div>
            <a href="javascript:void(0)" onclick="logout()"
                class="flex items-center gap-1.5 p-2 md:p-3 text-red-400 hover:bg-red-500/10 hover:text-red-500 rounded-lg transition">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
                </svg>
                <span class="hidden sm:inline text-sm font-medium">Logout</span>
            </a>
        </div>
    </nav>


    {{-- ══════════════════════════════════════════
         MOBILE DRAWER
         Both elements stay in the DOM at all times
         so CSS transitions can fire in both directions.
    ══════════════════════════════════════════ --}}

    {{-- Dimmed backdrop --}}
    <div id="mobileMenuBackdrop" onclick="closeMobileMenu()"></div>

    {{-- Slide-in panel --}}
    <div id="mobileMenuPanel" role="dialog" aria-modal="true" aria-label="Navigation menu">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800 flex-shrink-0">
            <img class="logo w-28" src="{{ asset('storage/logo/merotable-logo-gray.png') }}" alt="Logo">
            <button onclick="closeMobileMenu()" class="p-1.5 rounded-lg hover:bg-gray-800 transition text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Restaurant name --}}
        <div class="px-4 py-3 border-b border-gray-800 flex-shrink-0">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-0.5">Restaurant</p>
            <p class="restroName text-sm font-medium text-gray-200">loading…</p>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('staff.dashboard') }}" onclick="closeMobileMenu()"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('staff.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('staff.billing') }}" onclick="closeMobileMenu()"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                {{ request()->routeIs('staff.billing*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
                Billing
            </a>
        </nav>

        {{-- User + Logout --}}
        <div class="px-4 py-4 border-t border-gray-800 flex-shrink-0">
            <div class="mb-3">
                <p class="text-xs uppercase tracking-wider text-gray-500 font-semibold mb-0.5">Logged in as</p>
                <p class="user-name text-sm font-medium text-white truncate"></p>
            </div>
            <a href="javascript:void(0)" onclick="logout()"
                class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-500 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
                </svg>
                Logout
            </a>
        </div>
    </div>


    {{-- ══════════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════════ --}}
    <main class="p-4 md:p-8 min-h-[calc(100vh-57px)] overflow-y-auto">
        @yield('content')
    </main>


    {{-- ══════════════════════════════════════════
         TOAST (z-[70] so it stays above the drawer)
    ══════════════════════════════════════════ --}}
    <div id="toast" class="fixed bottom-4 right-4 md:bottom-6 md:right-6 z-[70] hidden">
        <div id="toastBox"
            class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[220px] md:min-w-[260px]">
            <span id="toastIcon" class="text-lg"></span>
            <span id="toastMsg"></span>
        </div>
    </div>


    <script>
        // ── Mobile drawer ──────────────────────────────────────────
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        const hamburger = document.getElementById('hamburger');

        function openMobileMenu() {
            mobileMenuPanel.classList.add('open');
            mobileMenuBackdrop.classList.add('open');
            hamburger.classList.add('open');
            hamburger.setAttribute('aria-expanded', 'true');

            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileMenuPanel.classList.remove('open');
            mobileMenuBackdrop.classList.remove('open');
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');

            document.body.style.overflow = '';
        }

        function toggleMobileMenu() {
            const isOpen = mobileMenuPanel.classList.contains('open');
            isOpen ? closeMobileMenu() : openMobileMenu();
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && menuOpen) closeMobileMenu();
        });

        // ── User / restro info ─────────────────────────────────────
        const userName = localStorage.getItem('user_name');
        const restroName = localStorage.getItem('restro_name');
        if (userName) document.querySelectorAll('.user-name').forEach(el => el.textContent = userName);
        if (restroName) document.querySelectorAll('.restroName').forEach(el => el.textContent = restroName);

        // ── Logout ─────────────────────────────────────────────────
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
            localStorage.clear();
            window.location.href = '/auth';
        }

        // ── Toast ──────────────────────────────────────────────────
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
                `flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-medium min-w-[220px] md:min-w-[260px] ${s.bg}`;
            icon.textContent = s.icon;
            msg.textContent = message;

            toast.classList.remove('hidden');
            clearTimeout(toast._timer);
            toast._timer = setTimeout(() => toast.classList.add('hidden'), 3000);
        }
    </script>

</body>

</html>
