<aside class="fixed left-0 top-0 w-72 h-screen bg-slate-900 text-white flex flex-col">

    <div class="p-6">
        <h1 class="text-2xl font-bold">MeroTable</h1>
        <p class="text-sm text-gray-400">Super Admin</p>
    </div>

    <nav class="px-4 space-y-2 flex-1">
        @php
            $links = [
                ['route' => 'web-super-admin.dashboard', 'label' => 'Dashboard'],
                ['route' => 'web-super-admin.restaurants.index', 'label' => 'Restaurants'],
                ['route' => 'web-super-admin.plans.index', 'label' => 'Plans'],
                ['route' => 'web-super-admin.subscriptions.index', 'label' => 'Subscriptions'],
                ['route' => 'web-super-admin.transactions.index', 'label' => 'Transactions'],
                ['route' => 'web-super-admin.reports', 'label' => 'Reports'],
                ['route' => 'web-super-admin.settings.index', 'label' => 'Settings'],
            ];
        @endphp

        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               class="block px-4 py-3 rounded transition
                      {{ request()->routeIs($link['route']) ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-300' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-slate-800">
        <button onclick="saLogout()" class="w-full text-left px-4 py-3 rounded hover:bg-slate-800 text-gray-300">
            Logout
        </button>
    </div>

</aside>