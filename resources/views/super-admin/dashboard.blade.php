@extends('layouts.super-admin')

@section('title', 'Dashboard')


@section('content')

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500">Overview of MeroTable platform activity</p>
        </div>
        <div id="admin-name" class="text-sm text-gray-500"></div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="stat-cards">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-7 bg-gray-200 rounded w-1/2"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-7 bg-gray-200 rounded w-1/2"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-7 bg-gray-200 rounded w-1/2"></div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
            <div class="h-4 bg-gray-200 rounded w-2/3 mb-3"></div>
            <div class="h-7 bg-gray-200 rounded w-1/2"></div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Recent Transactions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-100">
                        <th class="px-6 py-3 font-medium">ID</th>
                        <th class="px-6 py-3 font-medium">Amount</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody id="activity-rows">
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-400">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function saStatusBadge(status) {
            const colors = {
                success: 'bg-green-100 text-green-700',
                pending: 'bg-yellow-100 text-yellow-700',
                failed: 'bg-red-100 text-red-700',
            };
            const cls = colors[status] || 'bg-gray-100 text-gray-700';
            return `<span class="px-2 py-1 rounded-full text-xs font-medium ${cls}">${status ?? 'unknown'}</span>`;
        }

        function saFormatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
        }

        (async function loadDashboard() {
            try {
                const res = await saFetch('/super-admin');
                const data = await res.json();

                // admin name (pulled from localStorage, set by auth-guard)
                const user = JSON.parse(localStorage.getItem('super_admin_user') || '{}');
                document.getElementById('admin-name').innerText = user.name ? `Signed in as ${user.name}` : '';

                // stat cards
                const stats = data.stats || {};
                const cards = [
                    { label: 'Total Restaurants', value: stats.total_restaurants ?? 0 },
                    { label: 'Active Subscriptions', value: stats.active_subscriptions ?? 0 },
                    { label: 'Total Revenue', value: `Rs. ${Number(stats.total_revenue ?? 0).toLocaleString()}` },
                    { label: 'Total Plans', value: stats.total_plans ?? 0 },
                ];

                document.getElementById('stat-cards').innerHTML = cards.map(c => `
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">${c.label}</p>
                        <p class="text-2xl font-bold text-gray-800">${c.value}</p>
                    </div>
                `).join('');

                // activity table
                const rows = data.recent_activity || [];
                const tbody = document.getElementById('activity-rows');

                if (rows.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No recent activity</td></tr>`;
                    return;
                }

                tbody.innerHTML = rows.map(row => `
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="px-6 py-3 text-gray-700">#${row.id}</td>
                        <td class="px-6 py-3 text-gray-700">Rs. ${Number(row.amount ?? 0).toLocaleString()}</td>
                        <td class="px-6 py-3">${saStatusBadge(row.status)}</td>
                        <td class="px-6 py-3 text-gray-500">${saFormatDate(row.created_at)}</td>
                    </tr>
                `).join('');

            } catch (err) {
                console.error('Failed to load dashboard:', err);
            }
        })();
    </script>

@endsection