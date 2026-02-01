{{-- RESTAURANT ONLY --}}
<aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
    <div class="p-6 text-2xl font-extrabold text-blue-400 tracking-tight">
        MeroTable
    </div>
    <nav class="flex-1 px-4 space-y-2">
        <a href="{{ route('restaurant.dashboard') }}" class="flex items-center p-3 bg-blue-600 rounded-lg font-semibold">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            Dashboard
        </a>
        <a href="#" class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            Active Tables
        </a>
        <a href="{{ route('restaurant.menu') }}" class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                </path>
            </svg>
            Menu Items
        </a>
        <a href="{{ route('restaurant.reports') }}" class="flex items-center p-3 text-gray-400 hover:bg-gray-800 rounded-lg transition">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            Sales Reports
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
</script>