@extends('layouts.super-admin')

@section('title', 'Restaurants')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Restaurants</h1>
            <p class="text-sm text-gray-500">Manage restaurants registered on MeroTable</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex gap-3 mb-4">
        <input type="text" id="search-input" placeholder="Search by name, email, or owner..."
            class="flex-1 max-w-sm px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select id="status-filter" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="blocked">Blocked</option>
            <option value="expired">Expired</option>
            <option value="pending">Pending</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="px-6 py-3 font-medium">Restaurant</th>
                        <th class="px-6 py-3 font-medium">Owner</th>
                        <th class="px-6 py-3 font-medium">Orders Today</th>
                        <th class="px-6 py-3 font-medium">Lifetime Orders</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="restaurant-rows">
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500" id="pagination-info"></p>
            <div class="flex gap-2" id="pagination-controls"></div>
        </div>
    </div>

    {{-- Slide-over drawer --}}
    <div id="drawer-backdrop" class="fixed inset-0 bg-black/30 opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>

    <div id="drawer" class="fixed top-0 right-0 h-screen w-full max-w-md bg-white shadow-2xl translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white">
            <h2 class="font-semibold text-gray-800">Restaurant Details</h2>
            <button onclick="closeDrawer()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <div id="drawer-content" class="p-6">
            <p class="text-gray-400 text-sm">Loading...</p>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let searchTimeout;

        function saStatusBadge(status) {
            const colors = {
                active: 'bg-green-100 text-green-700',
                inactive: 'bg-gray-100 text-gray-700',
                blocked: 'bg-red-100 text-red-700',
            };
            const cls = colors[status] || 'bg-gray-100 text-gray-700';
            return `<span class="px-2 py-1 rounded-full text-xs font-medium ${cls}">${status ?? 'unknown'}</span>`;
        }

        function saFormatDate(dateStr) {
            if (!dateStr) return '—';
            return new Date(dateStr).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        async function loadRestaurants(page = 1) {
            currentPage = page;
            const search = document.getElementById('search-input').value.trim();
            const status = document.getElementById('status-filter').value;

            const params = new URLSearchParams({ page });
            if (search) params.append('search', search);
            if (status) params.append('status', status);

            const tbody = document.getElementById('restaurant-rows');
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">Loading...</td></tr>`;

            try {
                const res = await saFetch(`/super-admin/restaurants?${params}`);
                const data = await res.json();
                const rows = data.data || [];

                if (rows.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No restaurants found</td></tr>`;
                } else {
                    tbody.innerHTML = rows.map(r => `
                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 cursor-pointer" onclick="openDrawer('${r.slug}')">
                            <td class="px-6 py-3 font-medium text-gray-800">${r.name ?? '—'}</td>
                            <td class="px-6 py-3 text-gray-600">${r.owner_name ?? '—'}</td>
                            <td class="px-6 py-3 text-gray-600">${r.orders_today_count ?? 0}</td>
                            <td class="px-6 py-3 text-gray-600">${r.orders_lifetime_count ?? 0}</td>
                            <td class="px-6 py-3">${saStatusBadge(r.status)}</td>
                            <td class="px-6 py-3 text-right" onclick="event.stopPropagation()">
                                <button onclick="activateRestaurant('${r.slug}')" class="text-indigo-600 hover:underline text-xs font-medium mr-3">
                                    ${r.status === 'active' ? 'Block' : 'Activate'}
                                </button>
                                <button onclick="deleteRestaurant('${r.slug}')" class="text-red-600 hover:underline text-xs font-medium">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }

                renderPagination(data);
            } catch (err) {
                console.error('Failed to load restaurants:', err);
            }
        }

        function renderPagination(data) {
            const info = document.getElementById('pagination-info');
            const controls = document.getElementById('pagination-controls');

            if (!data.total) { info.innerText = ''; controls.innerHTML = ''; return; }

            info.innerText = `Showing ${data.from ?? 0}–${data.to ?? 0} of ${data.total}`;

            controls.innerHTML = `
                <button onclick="loadRestaurants(${currentPage - 1})" ${!data.prev_page_url ? 'disabled class="opacity-40"' : ''}
                    class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">Prev</button>
                <button onclick="loadRestaurants(${currentPage + 1})" ${!data.next_page_url ? 'disabled class="opacity-40"' : ''}
                    class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">Next</button>
            `;
        }

        // ---- Drawer ----

        async function openDrawer(slug) {
            document.getElementById('drawer-backdrop').classList.remove('opacity-0', 'pointer-events-none');
            document.getElementById('drawer').classList.remove('translate-x-full');

            const content = document.getElementById('drawer-content');
            content.innerHTML = `<p class="text-gray-400 text-sm">Loading...</p>`;

            try {
                const res = await saFetch(`/super-admin/restaurants/${slug}`);
                const r = await res.json();

                const staffRows = (r.staff || []).map(s => `
                    <li class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">${s.name}</p>
                            <p class="text-xs text-gray-500">${s.email}</p>
                        </div>
                        <span class="text-xs px-2 py-1 bg-gray-100 rounded-full text-gray-600 capitalize">${s.role ?? 'staff'}</span>
                    </li>
                `).join('') || `<li class="text-sm text-gray-400 py-2">No staff added yet</li>`;

                content.innerHTML = `
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg overflow-hidden">
                            ${r.logo ? `<img src="${r.logo}" class="w-full h-full object-cover">` : r.name?.charAt(0) ?? '?'}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">${r.name}</h3>
                            <p class="text-sm text-gray-500">${saStatusBadge(r.status)}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 mb-1">Orders Today</p>
                            <p class="text-xl font-bold text-gray-800">${r.orders_today_count ?? 0}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 mb-1">Lifetime Orders</p>
                            <p class="text-xl font-bold text-gray-800">${r.orders_lifetime_count ?? 0}</p>
                        </div>
                    </div>

                    <dl class="space-y-3 mb-6 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Owner</dt><dd class="text-gray-800">${r.owner_name ?? '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="text-gray-800">${r.email ?? '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd class="text-gray-800">${r.contact_number ?? '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Address</dt><dd class="text-gray-800 text-right">${r.address ?? '—'}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Joined</dt><dd class="text-gray-800">${saFormatDate(r.created_at)}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Plan</dt><dd class="text-gray-800">${r.current_subscription?.plan?.name ?? 'No active plan'}</dd></div>
                    </dl>

                    ${r.description ? `<div class="mb-6"><p class="text-xs text-gray-500 mb-1">Description</p><p class="text-sm text-gray-700">${r.description}</p></div>` : ''}

                    <div>
                        <p class="text-xs text-gray-500 mb-2">Staff (${(r.staff || []).length})</p>
                        <ul>${staffRows}</ul>
                    </div>
                `;
            } catch (err) {
                content.innerHTML = `<p class="text-sm text-red-500">Failed to load restaurant details.</p>`;
                console.error(err);
            }
        }

        function closeDrawer() {
            document.getElementById('drawer-backdrop').classList.add('opacity-0', 'pointer-events-none');
            document.getElementById('drawer').classList.add('translate-x-full');
        }

        document.getElementById('drawer-backdrop').addEventListener('click', closeDrawer);

        // ---- Actions ----

        async function activateRestaurant(slug) {
            try {
                await saFetch(`/super-admin/restaurants/${slug}/approve`, { method: 'POST' });
                loadRestaurants(currentPage);
            } catch (err) { console.error(err); }
        }

        async function deleteRestaurant(slug) {
            if (!confirm('Delete this restaurant? This cannot be undone.')) return;
            try {
                await saFetch(`/super-admin/restaurants/${slug}`, { method: 'DELETE' });
                loadRestaurants(currentPage);
            } catch (err) { console.error(err); }
        }

        document.getElementById('search-input').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadRestaurants(1), 400);
        });

        document.getElementById('status-filter').addEventListener('change', () => loadRestaurants(1));

        loadRestaurants();
    </script>

@endsection
