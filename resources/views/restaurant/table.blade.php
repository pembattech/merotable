{{-- TODO: CHECK table-note.md from ./docs --}}

{{-- TODO: Real-time (WebSocket) instead of polling --}}
@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')

    <style>
        .filterBtn {
            @apply px-4 py-1.5 text-sm rounded-lg font-medium text-gray-600 transition;
        }

        .filterBtn.active {
            @apply bg-white shadow text-blue-600;
        }
    </style>

    <style>
        #orderDrawer {
            backdrop-filter: blur(6px);
        }
    </style>





    <header class="flex justify-between items-center mb-6UIA8">
        <h1 class="text-2xl font-bold text-gray-800">Table Overview</h1>

        <div class="flex gap-2 bg-gray-100 p-1 rounded-xl">
            <button onclick="setFilter('all')" class="filterBtn active">All</button>
            <button onclick="setFilter('available')" class="filterBtn">Available</button>
            <button onclick="setFilter('occupied')" class="filterBtn">Occupied</button>
        </div>

    </header>

    <section>
        <div id="tablesGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
    </section>

    {{-- Order Drawer --}}
    <div id="orderDrawer"
        class="fixed inset-y-0 right-0 w-full sm:w-[420px]
            bg-white shadow-2xl translate-x-full
            transition-transform duration-300 z-50 rounded-l-3xl">

        <div class="p-4 border-b flex justify-between">
            <h2 class="font-bold text-lg">Order Details</h2>
            <button onclick="closeDrawer()">✕</button>
        </div>
        <div id="orderContent" class="p-4 text-sm"></div>
    </div>




    <script>
        let allTables = [];
        let currentFilter = 'all';
        const token = localStorage.getItem('auth_token');

        document.addEventListener('DOMContentLoaded', () => {
            fetchTables();
            setInterval(fetchTables, 15000); // 🔄 poll every 15 sec
        });

        async function fetchTables() {
            const res = await fetch('/api/v1/owner/restaurant/tables', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const json = await res.json();
            allTables = json.data.tables;

            renderTables();
        }

        function renderTables() {
            const grid = document.getElementById('tablesGrid');
            grid.innerHTML = '';

            let tables = allTables;

            if (currentFilter !== 'all') {
                tables = tables.filter(t => t.status === currentFilter);
            }

            tables.forEach(table => {
                grid.insertAdjacentHTML('beforeend', tableCard(table));
            });
        }

        function setFilter(filter) {
            currentFilter = filter;
            document.querySelectorAll('.filterBtn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            renderTables();
        }

        function tableCard(table) {
            const statusStyles = {
                available: {
                    bg: 'bg-white',
                    border: 'border-gray-200',
                    badge: 'bg-gray-100 text-gray-600',
                    ring: ''
                },
                occupied: {
                    bg: 'bg-gradient-to-br from-indigo-600 to-blue-600 text-white',
                    border: 'border-indigo-700',
                    badge: 'bg-white/20 text-white',
                    ring: 'ring-2 ring-indigo-400'
                },
                reserved: {
                    bg: 'bg-gradient-to-br from-amber-400 to-orange-400 text-white',
                    border: 'border-amber-500',
                    badge: 'bg-white/20 text-white',
                    ring: ''
                }
            };

            const s = statusStyles[table.status];

            return `
        <div onclick="openOrder(${table.id})"
             class="${s.bg} ${s.border} ${s.ring}
                    p-4 rounded-2xl border shadow-sm
                    cursor-pointer transition
                    hover:scale-[1.03] hover:shadow-lg">

            <div class="flex justify-between items-center">
                <span class="text-2xl font-extrabold tracking-wide">
                    ${table.table_number}
                </span>
                <span class="text-[10px] px-2 py-1 rounded-full font-semibold uppercase ${s.badge}">
                    ${table.status}
                </span>
            </div>

            ${table.status === 'occupied' ? `
                    <div class="mt-4">
                        <p class="text-xs opacity-80">Current Bill</p>
                        <p class="text-lg font-bold">Rs. ${table.order_amount}</p>
                    </div>
                ` : `
                    <div class="mt-6 h-6"></div>
                `}
        </div>
    `;
        }


        /* 🧾 ORDER DETAILS */

        async function openOrder(tableId) {
            const drawer = document.getElementById('orderDrawer');
            drawer.classList.remove('translate-x-full');

            const res = await fetch(`/api/v1/owner/restaurant/tables/${tableId}/order`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const json = await res.json();

            document.getElementById('orderContent').innerHTML = orderHTML(json.data);
        }

        function closeDrawer() {
            document.getElementById('orderDrawer').classList.add('translate-x-full');
        }

        function orderHTML(order) {
            if (!order) {
                return `<p class="text-gray-400 text-center mt-10">No active order</p>`;
            }

            return `
        <div class="space-y-4">
            <div class="bg-gray-50 p-4 rounded-xl">
                <p class="text-xs text-gray-500">Order ID</p>
                <p class="font-semibold">${order.id}</p>
                <p class="text-lg font-bold mt-2">Rs. ${order.total}</p>
            </div>

            <div class="space-y-3">
                ${order.items.map(item => `
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">${item.name}</p>
                                <p class="text-xs text-gray-400">× ${item.qty}</p>
                            </div>
                            <p class="font-semibold">Rs. ${item.subtotal}</p>
                        </div>
                    `).join('')}
            </div>

            <button class="w-full mt-6 bg-indigo-600 text-white py-3 rounded-xl font-semibold">
                Proceed to Checkout
            </button>
        </div>
    `;
        }
    </script>





@endsection
