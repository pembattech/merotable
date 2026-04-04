{{-- Mobile Topbar --}}
<div class="md:hidden fixed top-0 left-0 right-0 z-50 bg-gray-900 flex items-center justify-between px-4 py-3 shadow-lg">
    <div class="flex items-center gap-2">
        <img class="logo w-28" src="{{ asset('storage/logo/merotable-logo-gray.png') }}" alt="Logo">
        </div>
    <button id="sidebar-toggle" class="text-gray-400 hover:text-white focus:outline-none" aria-label="Open menu">
        <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

{{-- Overlay backdrop (mobile only) --}}
<div id="sidebar-backdrop"
    class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden md:hidden transition-opacity duration-300"
    onclick="closeSidebar()">
</div>

{{-- Sidebar --}}
<aside id="sidebar"
    class="fixed md:static inset-y-0 left-0 z-40
           w-64 bg-gray-900 text-white flex flex-col
           transform -translate-x-full md:translate-x-0
           transition-transform duration-300 ease-in-out
           md:transition-none
           h-full md:h-screen">

    {{-- Logo (desktop only — hidden on mobile since topbar shows it) --}}
    <div class="hidden md:block p-6 text-xl md:text-2xl font-extrabold text-blue-400 tracking-tight">
            <img class="logo" src="{{ asset('storage/logo/merotable-logo-gray.png') }}" alt="Logo">
    </div>

    {{-- Spacer so nav isn't hidden behind the fixed mobile topbar when drawer is open --}}
    <div class="md:hidden h-16 shrink-0"></div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 space-y-2 overflow-y-auto py-2">

        {{-- Dashboard --}}
        <a href="{{ route('restaurant.dashboard') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg font-semibold text-sm md:text-base
           {{ request()->routeIs('restaurant.dashboard')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10l9-7 9 7v10a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3z" />
            </svg>
            Dashboard
        </a>

        {{-- Active Tables --}}
        <a href="{{ route('restaurant.table') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.table*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 10h16M6 10v10M18 10v10M9 3h6v4H9z" />
            </svg>
            Active Tables
        </a>

        {{-- Menu Items --}}
        <a href="{{ route('restaurant.menu') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.menu*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6M12 3v18" />
            </svg>
            Menu Items
        </a>

        {{-- Sales Reports --}}
        <a href="{{ route('restaurant.reports') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.reports*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.866 0-7 1.343-7 3s3.134 3 7 3 7-1.343 7-3-3.134-3-7-3z
                         M5 11v4c0 1.657 3.134 3 7 3s7-1.343 7-3v-4" />
            </svg>
            Sales Reports
        </a>

        {{-- Staff Management --}}
        <a href="{{ route('restaurant.staff') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.staff*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1
                         M9 20H4v-2a4 4 0 014-4h1
                         M16 3.13a4 4 0 010 7.75
                         M8 3.13a4 4 0 000 7.75
                         M12 7a4 4 0 110 8 4 4 0 010-8z" />
            </svg>
            Staff Management
        </a>

        {{-- Setting --}}
        <a href="{{ route('restaurant.setting') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.setting*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11.983 2.25c.414 0 .75.336.75.75v1.087a7.5 7.5 0 012.327.964l.77-.77a.75.75 0 011.06 0l1.06 1.06a.75.75 0 010 1.06l-.77.77c.41.73.73 1.515.964 2.327H21a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75h-1.087a7.5 7.5 0 01-.964 2.327l.77.77a.75.75 0 010 1.06l-1.06 1.06a.75.75 0 01-1.06 0l-.77-.77a7.5 7.5 0 01-2.327.964V21a.75.75 0 01-.75.75h-1.5a.75.75 0 01-.75-.75v-1.087a7.5 7.5 0 01-2.327-.964l-.77.77a.75.75 0 01-1.06 0l-1.06-1.06a.75.75 0 010-1.06l.77-.77a7.5 7.5 0 01-.964-2.327H3a.75.75 0 01-.75-.75v-1.5A.75.75 0 013 9.75h1.087a7.5 7.5 0 01.964-2.327l-.77-.77a.75.75 0 010-1.06l1.06-1.06a.75.75 0 011.06 0l.77.77a7.5 7.5 0 012.327-.964V3c0-.414.336-.75.75-.75h1.5zM12 9.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" />
            </svg>
            Setting
        </a>

        {{-- Logout --}}
        <a href="javascript:void(0)" onclick="logout()"
            class="flex items-center p-3 text-sm md:text-base text-red-400 hover:bg-red-500/10 hover:text-red-500 rounded-lg transition">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
            </svg>
            Logout
        </a>

    </nav>

    {{-- Footer --}}
    <div class="p-6 border-t border-gray-800 shrink-0">
        <p class="text-xs md:text-xs text-gray-500 uppercase font-bold">Logged in as</p>
        <p class="user-name text-xs md:text-sm font-medium text-white"></p>
    </div>

</aside>


<script>
    const userName = localStorage.getItem('user_name');
    if (userName) {
        document.querySelectorAll('.user-name').forEach(el => el.textContent = userName);
    }

    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const iconOpen = document.getElementById('icon-open');
    const iconClose = document.getElementById('icon-close');

    document.getElementById('sidebar-toggle').addEventListener('click', () => {
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        isOpen ? closeSidebar() : openSidebar();
    });

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        iconOpen.classList.add('hidden');
        iconClose.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });

    async function logout() {
        closeSidebar();
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch('/api/v1/auth/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await res.json();

            if (!data.success) {
                showToast(data.message || 'Something went wrong ❌', 'error');
                return;
            }
        } catch (e) {
            console.warn('Logout request failed, clearing session anyway');
        }
        localStorage.clear();
        window.location.href = '/auth';
    }
</script>
