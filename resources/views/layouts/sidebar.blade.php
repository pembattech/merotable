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
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z"/></svg>
            Dashboard
        </a>

        {{-- Active Tables --}}
        <a href="{{ route('restaurant.table') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.table*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M173-600h614l-34-120H208l-35 120Zm307-60Zm192 140H289l-11 80h404l-10-80ZM160-160l49-360h-89q-20 0-31.5-16T82-571l57-200q4-13 14-21t24-8h606q14 0 24 8t14 21l57 200q5 19-6.5 35T840-520h-88l48 360h-80l-27-200H267l-27 200h-80Z"/></svg>
            Active Tables
        </a>

        {{-- Menu Items --}}
        <a href="{{ route('restaurant.menu') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.menu*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-80h-40v-80h40v-120h-40v-80h40v-120h-40v-80h40v-80q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v640q0 33-23.5 56.5T720-80H240Zm0-80h480v-640H240v80h40v80h-40v120h40v80h-40v120h40v80h-40v80Zm0 0v-640 640Zm140-120h60v-160q26-7 43-28.5t17-48.5v-163h-40v151h-30v-151h-40v151h-30v-151h-40v163q0 27 17 48.5t43 28.5v160Zm220 0h60v-400q-50 0-85 35t-35 85v120h60v160Z"/></svg>
            Menu Items
        </a>

        {{-- Sales Reports --}}
        <a href="{{ route('restaurant.reports') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.reports*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M280-280h80v-200h-80v200Zm320 0h80v-400h-80v400Zm-160 0h80v-120h-80v120Zm0-200h80v-80h-80v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>
            Sales Reports
        </a>

        {{-- Invoice --}}
        <a href="{{ route('restaurant.invoices') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.invoices*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Zm-40 404h560v-568H200v568Zm0-568v568-568Z"/></svg>
            Invoice
        </a>

        {{-- Staff Management --}}
        <a href="{{ route('restaurant.staff') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.staff*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17-62.5t47-43.5q60-30 124.5-46T480-440q67 0 131.5 16T736-378q30 15 47 43.5t17 62.5v112H160Zm376.5-423.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM640-332v92h80v-32q0-11-5-20t-15-14q-14-8-29.5-14.5T640-332Zm-240-21v53h160v-53q-20-4-40-5.5t-40-1.5q-20 0-40 1.5t-40 5.5ZM240-240h80v-92q-15 5-30.5 11.5T260-306q-10 5-15 14t-5 20v32Zm400 0H320h320ZM480-640Z"/></svg>
            Staff Management
        </a>

        {{-- Setting --}}
        <a href="{{ route('restaurant.setting') }}" onclick="closeSidebar()"
            class="flex items-center p-3 rounded-lg text-sm md:text-base
           {{ request()->routeIs('restaurant.setting*')
               ? 'bg-blue-600 text-white'
               : 'text-gray-400 hover:bg-gray-800 transition' }}">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" class="w-5 h-5 mr-3 shrink-0" viewBox="0 -960 960 960" width="24px" fill="currentColor"><path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/></svg>
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
