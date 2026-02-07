{{-- RESTAURANT ONLY --}}
<aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
    <div class="p-6 text-2xl font-extrabold text-blue-400 tracking-tight">
        MeroTable
    </div>
    <nav class="flex-1 px-4 space-y-2">
        <a href="{{ route('restaurant.dashboard') }}" class="flex items-center p-3 bg-blue-600 rounded-lg font-semibold">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10l9-7 9 7v10a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H3z" />
            </svg>
            Dashboard
        </a>
        <a href="{{ route('restaurant.table') }}"
            class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 10h16M6 10v10M18 10v10M9 3h6v4H9z" />
            </svg>
            Active Tables
        </a>
        <a href="{{ route('restaurant.menu') }}"
            class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6M12 3v18" />
            </svg>
            Menu Items
        </a>
        <a href="{{ route('restaurant.reports') }}"
            class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.866 0-7 1.343-7 3s3.134 3 7 3 7-1.343 7-3-3.134-3-7-3z
             M5 11v4c0 1.657 3.134 3 7 3s7-1.343 7-3v-4" />
            </svg>
            Sales Reports
        </a>
        <a href="{{ route('restaurant.staff') }}"
            class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1
                 M9 20H4v-2a4 4 0 014-4h1
                 M16 3.13a4 4 0 010 7.75
                 M8 3.13a4 4 0 000 7.75
                 M12 7a4 4 0 110 8 4 4 0 010-8z" />
            </svg>
            Staff Management
        </a>
        <a href="javascript:void(0)" onclick="logout()"
            class="flex items-center p-3 text-red-400 hover:bg-red-500/10 hover:text-red-500 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
            </svg>
            Logout
        </a>

    </nav>
    <div class="p-6 border-t border-gray-800">
        <p class="text-xs text-gray-500 uppercase font-bold">Logged in as</p>
        <p class="user-name text-sm font-medium"></p>
    </div>
</aside>


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
