{{-- TODO: CHECK table-note.md from ./docs --}}

{{-- TODO: Real-time (WebSocket) instead of polling --}}
@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')


    <style>
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

        .table-status-btn {
            padding: 6px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .table-status-btn.active {
            background: #1e293b;
            color: #fff;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-preparing {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-served {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 13px;
            top: 30px;
            bottom: -4px;
            width: 1.5px;
            background: linear-gradient(to bottom, #e5e7eb, #f3f4f6);
        }

        /* custom scrollbar */
        #modal-body::-webkit-scrollbar {
            width: 4px;
        }

        #modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        #modal-body::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        .filter-wrapper {
            position: relative;
            display: flex;
            gap: 4px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 4px;
            width: fit-content;
        }

        /* Sliding background */
        .slider {
            position: absolute;
            top: 4px;
            left: 4px;
            height: calc(100% - 8px);
            width: 0;
            background: #1e293b;
            border-radius: 8px;
            transition: transform 0.3s ease, width 0.3s ease;
            z-index: 0;
        }

        .table-status-btn {
            position: relative;
            z-index: 1;
            padding: 6px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border: none;
            background: transparent;
            transition: color 0.2s ease;
        }

        .table-status-btn.active {
            color: #fff;
        }
    </style>





    <header class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-extrabold text-gray-800">Table Overview</h1>

        <div class="filter-wrapper">
            <div class="slider" id="filterSlider"></div>

            <button onclick="setFilter('all', this)" class="table-status-btn active">All</button>
            <button onclick="setFilter('available', this)" class="table-status-btn">Available</button>
            <button onclick="setFilter('occupied', this)" class="table-status-btn">Occupied</button>
        </div>


    </header>

    <section>
        <div id="tablesGrid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
    </section>

    {{-- Order Drawer --}}
    <div id="openOrderModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeOrderModal()"></div>

        <div id="orderContent"
            class="animate-slide-up relative bg-white w-full sm:max-w-lg sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">

            {{-- <div id="orderContent" class="p-4 text-sm"></div> --}}
        </div>




        <script>
            let allTables = [];
            let currentFilter = 'all';
            const token = localStorage.getItem('auth_token');

            document.addEventListener('DOMContentLoaded', () => {
                fetchTables();
                setInterval(fetchTables, 15000); // 🔄 poll every 15 sec
            });

            // ── HELPERS ───────────────────────────────────────────────────
            function formatTime(iso) {
                if (!iso) return '—';
                return new Date(iso).toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            function capitalize(s) {
                return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
            }

            function orderStatusStyle(s) {
                return {
                    open: 'bg-white/20 text-white',
                    closed: 'bg-white/10 text-blue-100',
                    cancelled: 'bg-red-300/30 text-red-100'
                } [s] ||
                'bg-white/20 text-white';
            }


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

            function setFilter(filter, btn) {
                currentFilter = filter;

                document.querySelectorAll('.table-status-btn').forEach(b =>
                    b.classList.remove('active')
                );

                btn.classList.add('active');
                moveSlider(btn);
                renderTables();
            }

            function moveSlider(btn) {
                const slider = document.getElementById('filterSlider');

                slider.style.width = btn.offsetWidth + 'px';
                slider.style.transform = `translateX(${btn.offsetLeft}px)`;
            }

            // Initialize on load
            window.addEventListener('load', () => {
                const activeBtn = document.querySelector('.table-status-btn.active');
                moveSlider(activeBtn);
            });


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
                        ring: 'ring-2 ring-amber-300'
                    }
                };

                const s = statusStyles[table.status];

                return `
        <div onclick="openOrderModal(${table.id})"
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
                                                                                                                                                                                                        <p class="text-lg font-bold">Rs. ${table.today_total_amount}</p>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                ` : `
                                                                                                                                                                                                    <div class="mt-6 h-6"></div>
                                                                                                                                                                                                `}
        </div>
    `;
            }


            /* 🧾 ORDER DETAILS */

            async function openOrderModal(tableId) {
                const drawer = document.getElementById('openOrderModal');
                drawer.classList.remove('hidden');
                drawer.classList.add('flex');


                // const modal = document.getElementById('orderDetailsModal');
                // modal.classList.remove('hidden');
                // modal.classList.add('flex');


                const res = await fetch(`/api/v1/owner/restaurant/tables/${tableId}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Something went wrong ❌', 'error');
                    return;
                }

                document.getElementById('orderContent').innerHTML = orderHTML(data.data);
            }

            function closeOrderModal() {
                document.getElementById('openOrderModal').classList.add('hidden');
            }

            // ── ACTIVITY ──────────────────────────────────────────────────
            function buildActivitySentence(act) {
                const staff = act.staff?.name || 'Staff';
                const role = act.staff?.role || '';
                const meta = act.meta || {};
                const actor = role ?
                    `<span class="font-semibold text-gray-800">${staff}</span> <span class="text-gray-400 text-xs">(${role})</span>` :
                    `<span class="font-semibold text-gray-800">${staff}</span>`;

                switch (act.action) {
                    case 'created':
                        return `${actor} created the order`;
                    case 'item_added':
                        return `${actor} added <span class="font-semibold text-gray-800">${meta.name || 'item'}</span>` +
                            (meta.quantity ? ` <span class="text-blue-600 font-semibold">×${meta.quantity}</span>` : '');
                    case 'item_removed':
                        return `${actor} removed <span class="font-semibold text-gray-800">${meta.name || 'item'}</span>`;
                    case 'qty_updated':
                        return `${actor} updated <span class="font-semibold text-gray-800">${meta.name || 'item'}</span> qty` +
                            (meta.from !== undefined ?
                                ` <span class="text-gray-400">(${meta.from} → <span class="text-blue-600 font-semibold">${meta.to}</span>)</span>` :
                                '');
                    case 'cancelled':
                        return `${actor} cancelled order` +
                            (meta.reason ? ` <span class="text-gray-400">— ${meta.reason}</span>` : '');
                    case 'checkout':
                        return `${actor} completed checkout`;
                    default:
                        return `${actor} ${act.action.replace(/_/g, ' ')}`;
                }
            }

            const ACT_CFG = {
                created: {
                    bg: 'bg-blue-100',
                    icon: 'text-blue-500',
                    dot: 'bg-blue-500',
                    path: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
                },
                item_added: {
                    bg: 'bg-green-100',
                    icon: 'text-green-600',
                    dot: 'bg-green-500',
                    path: 'M12 6v6m0 0v6m0-6h6m-6 0H6'
                },
                item_removed: {
                    bg: 'bg-orange-100',
                    icon: 'text-orange-500',
                    dot: 'bg-orange-400',
                    path: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
                },
                qty_updated: {
                    bg: 'bg-blue-100',
                    icon: 'text-blue-500',
                    dot: 'bg-blue-400',
                    path: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
                },
                cancelled: {
                    bg: 'bg-red-100',
                    icon: 'text-red-500',
                    dot: 'bg-red-500',
                    path: 'M6 18L18 6M6 6l12 12'
                },
                checkout: {
                    bg: 'bg-purple-100',
                    icon: 'text-purple-600',
                    dot: 'bg-purple-500',
                    path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                },
            };

            function orderHTML(order) {

                console.log(order)
                if (!order) {
                    return `<p class="text-gray-400 text-center mt-10">No active order</p>`;
                }


                // const currentOrder = order.table.orders[0];
                // const tableStatus = capitalize(order.table.status);
                // const activities = currentOrder.activities ?? [];
                // const totalEarning = order.total_earning;

                const tableOrders = order.table.orders ?? [];
                const currentOrder = tableOrders.length ? tableOrders[0] : null;

                const tableStatus = capitalize(order.table.status);
                const activities = currentOrder?.activities ?? [];
                const totalEarning = order.total_earning ?? 0;


                let orderSection = '';

                orderSection += `
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 rounded-xl p-2">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-base leading-tight">Order Details</h2>
                    <p class="text-xs text-gray-400" id="modal-table-label">Table T${order.table.id} · ${capitalize(order.table.status)}</p>
                </div>
            </div>
            <button onclick="closeOrderModal()"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-2 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- SCROLLABLE BODY -->
        <div class="overflow-y-auto flex-1 px-5 py-4 space-y-5" id="modal-body">

            <!-- Summary Card -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-28 h-28 bg-white/10 rounded-full"></div>
                <div class="absolute -right-2 bottom-2 w-16 h-16 bg-white/5 rounded-full"></div>
                <div class="relative">
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <p class="text-blue-200 text-xs font-medium uppercase tracking-wider mb-1">Order ID</p>
                            <p class="text-white font-extrabold text-2xl" id="modal-order-id">#${currentOrder?.id ?? '-'}</p>
                        </div>
                        <span id="modal-order-status"
                            class="bg-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-full capitalize">
                            ${currentOrder?.status ?? 'N/A'}
                        </span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-blue-200 text-xs mb-1">Total Amount</p>
                            <p class="text-white font-extrabold text-3xl" id="modal-total">Rs. ${order.total_earning}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-200 text-xs mb-1">Placed at</p>
                            <p class="text-white text-sm font-semibold" id="modal-created-at">${formatTime(order.table.created_at)}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Items Ordered</p>
                <div id="modal-items" class="space-y-2">
                    ${currentOrder?.order_items.length ?
                    currentOrder?.order_items.map((item, i) => `
                                                                                                                                                                    <div class="flex items-center justify-between bg-gray-50 hover:bg-blue-50/60 rounded-xl px-4 py-3 transition group"
                                                                                                                                                             style="animation: slideUp ${0.1 + i * 0.06}s ease both;">
                                                                                                                                                            <div class="flex items-center gap-3">
                                                                                                                                                                <div class="w-9 h-9 bg-white rounded-lg shadow-sm flex items-center justify-center text-xs font-bold text-blue-600 border border-gray-200 group-hover:border-blue-300 transition">
                                                                                                                                                                    ${item.menu_item_id}
                                                                                                                                                                </div>
                                                                                                                                                                <div>
                                                                                                                                                                    <p class="font-semibold text-gray-800 text-sm">${item.menu_item.name}</p>
                                                                                                                                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                                                                                                                                        Rs.&nbsp;${item.price.toLocaleString()} &times; ${item.quantity}
                                                                                                                                                                    </p>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="flex items-center gap-3">
                                                                                                                                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full status-${item.status}">
                                                                                                                                                                    ${capitalize(item.status)}
                                                                                                                                                                </span>
                                                                                                                                                                <p class="font-bold text-gray-800 text-sm min-w-[68px] text-right">
                                                                                                                                                                    Rs.&nbsp;${(item.price * item.quantity).toLocaleString()}
                                                                                                                                                                </p>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                            `).join('')
                                                                                                                                                    : `<p class="text-sm text-gray-400 text-center py-4">No items ordered</p>`
                                                                                                                                                }
                </div>
            </div>

            <!-- Activity Timeline -->
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Order Activity</p>
                <div id="modal-activity" class="relative space-y-0">
                    `;

                if (!activities?.length) {
                    orderSection += `<p class="text-sm text-gray-400 text-center py-4">No activity recorded</p>`;
                }

                orderSection += activities.map((act, i) => {
                    const c = ACT_CFG[act.action] || ACT_CFG.created;
                    const isLast = i === activities.length - 1;
                    const sentence = buildActivitySentence(act);
                    const time = formatTime(act.created_at);


                    return `
                <div class = "relative flex gap-3 ${isLast ? '' : 'pb-5'} timeline-item" >
                    <!--Icon bubble -->
                    <div class ="flex-shrink-0 w-7 h-7 ${c.bg} rounded-full flex items-center justify-center mt-0.5 z-10 ring-2 ring-white" >
                        <svg class = "h-3.5 w-3.5 ${c.icon}" fill = "none" stroke = "currentColor" viewBox = "0 0 24 24" >
                            <path stroke - linecap = "round" stroke - linejoin = "round" stroke - width = "2" d = "${c.path}" / >
                        </svg> 
                    </div> 
                    <!--Content-->
                    <div class = "flex-1 min-w-0 pt-0.5" >
                        <p class = "text-sm text-gray-600 leading-snug" > ${sentence} </p>
                        <p class = "text-xs text-gray-400 mt-1" > ${time} </p>
                    </div>
                </div>`;

                }).join('');

                orderSection += `</div>
                </div>
                </div>

                 <!-- FOOTER -->
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/80">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500 font-medium">Grand Total</span>
                <span class="text-xl font-extrabold text-gray-800" id="modal-footer-total">Rs. ${totalEarning}</span>
            </div>
            <button onclick="proceedToCheckout()"
                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-3.5 rounded-xl transition duration-150 flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Proceed to Checkout
            </button>
        </div>`;


                return orderSection;

            }
        </script>





    @endsection
