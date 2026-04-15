{{-- TODO: CHECK table-note.md from ./docs --}}
{{-- TODO: Real-time (WebSocket) instead of polling --}}
@extends('layouts.app')

@section('title', 'Tables | ' . config('app.name'))

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

        /* ── Filter pill ── */
        .filter-wrapper {
            position: relative;
            display: flex;
            gap: 4px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 4px;
            width: max-content;
            margin-bottom: 15px;
        }

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
            padding: 5px 8px;
            border-radius: 8px;
            font-size: 0.70rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border: none;
            background: transparent;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        @media (min-width: 640px) {
            .table-status-btn {
                padding: 6px 18px;
                font-size: 0.85rem;
            }
        }

        .table-status-btn.active {
            color: #fff;
        }

        /* ── Order status badges ── */
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

        /* ── Timeline ── */
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 13px;
            top: 30px;
            bottom: -4px;
            width: 1.5px;
            background: linear-gradient(to bottom, #d1fae5, #f0fdf4);
        }

        /* ── Collapse ── */
        .activities-collapse {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .activities-collapse.open {
            max-height: 500px;
        }

        .chevron {
            transition: transform 0.2s ease;
        }

        .chevron.rotate {
            transform: rotate(180deg);
        }

        /* ── Mode tabs ── */
        .mode-tabs {
            display: flex;
            background: #f4f5fb;
            border-radius: 10px;
            padding: 4px;
            gap: 3px;
            margin: 10px 10px 18px;
        }

        .mode-tab {
            flex: 1;
            padding: 7px 8px;
            border-radius: 7px;
            border: none;
            background: none;
            font-size: 12px;
            font-weight: 600;
            color: #8b90a7;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: background 0.15s, color 0.15s, box-shadow 0.15s;
        }

        .mode-tab svg {
            width: 13px;
            height: 13px;
        }

        .mode-tab.active {
            background: #fff;
            color: #2563eb;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        /* ── Tab panels ── */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* ── Bulk row ── */
        .bulk-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* ── Range preview ── */
        .range-preview {
            background: #f4f5fb;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 16px;
            min-height: 52px;
            transition: border-color 0.2s;
        }

        .range-preview.has-items {
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .rp-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #8b90a7;
            margin-bottom: 6px;
        }

        .rp-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            max-height: 60px;
            overflow-y: auto;
        }

        .rp-chip {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            background: #2563eb;
            color: #fff;
            border-radius: 5px;
        }

        .rp-chip.dup {
            background: #ef4444;
            text-decoration: line-through;
            opacity: 0.7;
        }

        .rp-empty {
            font-size: 12px;
            color: #8b90a7;
            font-style: italic;
        }

        /* ── Add-table drawer: bottom sheet on mobile, side panel on md+ ── */
        #drawer {
            position: fixed;
            z-index: 50;
            transition: transform 0.3s ease;
            background: #fff;
        }

        @media (max-width: 767px) {
            #drawer {
                bottom: 0;
                left: 0;
                right: 0;
                top: auto;
                width: 100%;
                max-height: 92vh;
                border-radius: 20px 20px 0 0;
                transform: translateY(100%);
                overflow-y: auto;
                box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.15);
            }

            #drawer.open {
                transform: translateY(0);
            }

            #drawer-handle {
                display: block;
            }
        }

        @media (min-width: 768px) {
            #drawer {
                top: 0;
                right: 0;
                bottom: 0;
                width: 500px;
                transform: translateX(100%);
                overflow-y: auto;
                box-shadow: -8px 0 40px rgba(0, 0, 0, 0.1);
            }

            #drawer.open {
                transform: translateX(0);
            }

            #drawer-handle {
                display: none;
            }
        }
    </style>

    {{-- ── Header ── --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8">
        <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Table Overview</h1>

        <div class="flex items-center gap-2 flex-wrap">

            <button id="downloadQRBtn"
                class="flex items-center gap-2 bg-gray-900 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-bold shadow-lg hover:bg-gray-700 whitespace-nowrap">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download QR
            </button>

            <button id="openDrawer"
                class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-bold shadow-lg hover:bg-blue-700 whitespace-nowrap">
                + Add Table
            </button>
        </div>
    </header>

    <div class="filter-wrapper">
        <div class="slider" id="filterSlider"></div>
        <button onclick="setFilter('all', this)" class="table-status-btn active">All</button>
        <button onclick="setFilter('available', this)" class="table-status-btn">Available</button>
        <button onclick="setFilter('occupied', this)" class="table-status-btn">Occupied</button>
        <button onclick="setFilter('reserved', this)" class="table-status-btn">Reserved</button>
    </div>

    {{-- ── Tables Grid ── --}}
    <section>
        <div id="tablesGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4"></div>
    </section>

    {{-- ── Order Modal ── --}}
    <div id="openOrderModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeOrderModal()"></div>
        <div id="orderContent"
            class="animate-slide-up relative bg-white w-full sm:max-w-lg sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
        </div>
    </div>

    {{-- ── History Modal ── --}}
    <div id="historyModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeHistoryModal()"></div>
        <div id="historyContent"
            class="animate-slide-up relative bg-white w-full sm:max-w-lg sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">

            {{-- Header --}}
            <div
                class="px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <button onclick="toggleHistory()"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-800 font-semibold text-xs md:text-sm transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Current Order
                </button>
            </div>

            {{-- Summary stats --}}
            <div class="px-4 md:px-5 py-3 md:py-4 bg-gray-50">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Today's Summary</p>
                <div class="grid grid-cols-3 gap-2 md:gap-3">
                    <div class="bg-white rounded-xl p-2 md:p-3 text-center border border-gray-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Orders</p>
                        <p class="text-lg md:text-xl font-extrabold text-gray-800" id="ohOrdersCount">-</p>
                    </div>
                    <div class="bg-white rounded-xl p-2 md:p-3 text-center border border-gray-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Completed</p>
                        <p class="text-lg md:text-xl font-extrabold text-green-600" id="ohCompletedCount">-</p>
                    </div>
                    <div class="bg-white rounded-xl p-2 md:p-3 text-center border border-gray-100">
                        <p class="text-xs text-gray-500 font-medium mb-1">Revenue</p>
                        <p class="text-lg md:text-xl font-extrabold text-blue-600" id="ohRevenueCount">-</p>
                    </div>
                </div>
            </div>

            {{-- Order history timeline --}}
            <div class="px-4 md:px-5 py-4 space-y-4 overflow-y-auto flex-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Order History</p>
                <div class="order-history-timeline" id="orderHistoryTimeline"></div>
            </div>
        </div>
    </div>

    {{-- ── Add Table Drawer ── --}}
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <div id="drawer">
        {{-- Drag handle (mobile only) --}}
        <div id="drawer-handle" class="flex justify-center pt-3 pb-1">
            <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
        </div>

        {{-- Drawer header --}}
        <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 rounded-xl p-2">
                    <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-sm md:text-base leading-tight">Add Table</h2>
                    <p class="text-xs text-gray-400">Single table or generate a bulk range</p>
                </div>
            </div>
            <button id="closeDrawer"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mode tabs --}}
        <div class="mode-tabs">
            <button class="mode-tab active" id="tabSingle" onclick="switchMode('single')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor"
                        stroke-width="2" fill="none" />
                </svg>
                Single Table
            </button>
            <button class="mode-tab" id="tabBulk" onclick="switchMode('bulk')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h10" />
                </svg>
                Bulk / Range
            </button>
        </div>

        {{-- Single tab --}}
        <div class="tab-panel active" id="panelSingle">
            <form id="addTableForm" class="px-4 md:px-5 pb-5 space-y-4 md:space-y-5">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2" for="tableNumber">Table
                        Number</label>
                    <input class="field-input text-sm" type="text" id="tableNumber" name="table_number"
                        maxlength="5" placeholder="e.g. T16 or A1" autocomplete="off" required>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2" for="spaceArea">Space
                        Area</label>
                    <input class="field-input text-sm" type="text" id="spaceArea" name="area_name"
                        placeholder="e.g. Main Hall or Terrace" autocomplete="off" required>
                </div>
                <div class="flex gap-3 pt-1">
                    <button onclick="closeDrawer()" type="button"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Table
                    </button>
                </div>
            </form>
        </div>

        {{-- Bulk tab --}}
        <div class="tab-panel" id="panelBulk">
            <form id="bulkForm" onsubmit="submitBulk(event)" class="px-4 md:px-5 pb-5 space-y-4 md:space-y-5">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Prefix</label>
                    <input class="field-input text-sm" type="text" id="bulkPrefix" maxlength="3"
                        placeholder="e.g. T or A or VIP" oninput="updatePreview()" autocomplete="off">
                    <p class="text-xs text-gray-400 mt-1">Prefix + number must stay under 5 chars (e.g. "T" + "16" =
                        "T16").</p>
                </div>
                <div class="bulk-row">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">From</label>
                        <input class="field-input text-sm" type="number" id="bulkFrom" min="1" max="999"
                            placeholder="1" oninput="updatePreview()">
                    </div>
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">To</label>
                        <input class="field-input text-sm" type="number" id="bulkTo" min="1" max="999"
                            placeholder="20" oninput="updatePreview()">
                    </div>
                </div>
                <div class="range-preview" id="rangePreview">
                    <div class="rp-label">Preview</div>
                    <div class="rp-chips"><span class="rp-empty">Fill in prefix and range above…</span></div>
                </div>
                <div class="flex gap-3">
                    <button onclick="closeDrawer()" type="button"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Tables
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        const allOrderDetails = [];

        function toggleHistory() {
            const historyModal = document.getElementById('historyModal');
            const orderHistoryTimeline = document.getElementById('orderHistoryTimeline');

            if (historyModal.classList.contains('hidden')) {
                historyModal.classList.remove('hidden');
                historyModal.classList.add('flex');

                const table = allOrderDetails[0].data.table;
                console.log(table)
                let totalOrders = table.orders.length,
                    totalCompleted = 0,
                    revenue = 0;
                for (const o of table.orders) {
                    if (o.status === 'completed') {
                        totalCompleted++;
                        revenue += o.totalAmount;
                    }
                }
                document.getElementById('ohOrdersCount').textContent = totalOrders;
                document.getElementById('ohCompletedCount').textContent = totalCompleted;
                document.getElementById('ohRevenueCount').textContent = `Rs. ${revenue.toLocaleString()}`;

                let timelineHTML = '';
                const sortedOrders = [...table.orders].sort((a, b) =>
                    a.status === 'open' ? -1 : b.status === 'open' ? 1 : 0
                );

                sortedOrders.forEach(order => {
                    const ring = order.status === 'open' ? 'ring-2 ring-blue-100' : '';
                    timelineHTML += `<div class="border border-green-200 bg-green-50 rounded-xl p-3 md:p-4 mb-2 ${ring}">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Order #${order.id}</p>
                                <p class="text-xs text-gray-500 mt-1">${order.createdAt}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-base md:text-lg font-extrabold text-gray-800">Rs. ${order.totalAmount}</p>
                                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">${order.status}</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">`;

                    order.orderItems.forEach(oi => {
                        timelineHTML += `<div class="flex justify-between text-xs md:text-sm">
                            <span class="text-gray-600">${oi.menuItem.name} <span class="text-gray-400">×${oi.quantity}</span></span>
                            <span class="font-semibold text-gray-800">Rs. ${oi.price * oi.quantity}</span>
                        </div>`;
                    });

                    timelineHTML += `</div>
                        <div class="mt-3 pt-3 border-t border-green-200 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Completed</p>
                            <button onclick="toggleActivities()" class="flex items-center gap-1 text-xs font-semibold text-green-600 hover:text-green-700 transition">
                                <span id="activityBtnText">View Activities</span>
                                <svg id="chevronIcon" class="h-3.5 w-3.5 chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        <div id="activitiesSection" class="activities-collapse">
                            <div class="mt-3 pt-3 border-t border-green-200">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Order Activity</p>
                                <div class="relative space-y-0">`;

                    timelineHTML += order.activities.map((act, i) => {
                        const c = ACT_CFG[act.action] || ACT_CFG.created;
                        const isLast = i === order.activities.length - 1;
                        return `<div class="relative flex gap-3 ${isLast ? '' : 'pb-5'} timeline-item">
                            <div class="flex-shrink-0 w-7 h-7 ${c.bg} rounded-full flex items-center justify-center mt-0.5 z-10 ring-2 ring-white">
                                <svg class="h-3.5 w-3.5 ${c.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.path}"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <p class="text-xs md:text-sm text-gray-600 leading-snug">${buildActivitySentence(act)}</p>
                                <p class="text-xs text-gray-400 mt-1">${formatTime(act.created_at)}</p>
                            </div>
                        </div>`;
                    }).join('');

                    timelineHTML += `</div></div></div></div>`;
                });

                orderHistoryTimeline.innerHTML = timelineHTML;
            } else {
                historyModal.classList.remove('flex');
                historyModal.classList.add('hidden');
            }
        }

        function toggleActivities() {
            const section = document.getElementById('activitiesSection');
            const chevron = document.getElementById('chevronIcon');
            const btnText = document.getElementById('activityBtnText');
            const open = section.classList.contains('open');
            section.classList.toggle('open', !open);
            chevron.classList.toggle('rotate', !open);
            btnText.textContent = open ? 'View Activities' : 'Hide Activities';
        }
    </script>

    <script>
        let allTables = [];
        let currentFilter = 'all';
        const token = localStorage.getItem('auth_token');
        const url = localStorage.getItem('restro_url');

        document.addEventListener('DOMContentLoaded', () => {
            fetchTables();
            setInterval(fetchTables, 15000);
        });

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
            let tables = currentFilter === 'all' ? allTables : allTables.filter(t => t.status === currentFilter);
            tables.forEach(t => grid.insertAdjacentHTML('beforeend', tableCard(t)));
        }

        function setFilter(filter, btn) {
            currentFilter = filter;
            document.querySelectorAll('.table-status-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            moveSlider(btn);
            renderTables();
        }

        function moveSlider(btn) {
            const slider = document.getElementById('filterSlider');
            slider.style.width = btn.offsetWidth + 'px';
            slider.style.transform = `translateX(${btn.offsetLeft}px)`;
        }

        window.addEventListener('load', () => {
            const activeBtn = document.querySelector('.table-status-btn.active');
            if (activeBtn) moveSlider(activeBtn);
        });

        function tableCard(table) {
            const s = {
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
                },
            } [table.status] || {
                bg: 'bg-white',
                border: 'border-gray-200',
                badge: 'bg-gray-100 text-gray-600',
                ring: ''
            };

            return `<div onclick="openOrderModal(${table.id})"
                class="${s.bg} ${s.border} ${s.ring} p-3 md:p-4 rounded-2xl border shadow-sm cursor-pointer transition hover:scale-[1.03] hover:shadow-lg">
                <div class="flex justify-between items-center">
                    <span class="text-xl md:text-2xl font-extrabold tracking-wide">${table.table_number}</span>
                    <span class="text-[9px] md:text-[10px] px-1.5 md:px-2 py-1 rounded-full font-semibold uppercase ${s.badge}">${table.status}</span>
                </div>
                ${table.total_amount > 0
                    ? `<div class="mt-3 md:mt-4"><p class="text-xs opacity-80">Today's Total</p><p class="text-base md:text-lg font-bold">Rs. ${table.total_amount}</p></div>`
                    : `<div class="mt-5 md:mt-6 h-4 md:h-6"></div>`}
            </div>`;
        }

        async function openOrderModal(tableId) {
            const modal = document.getElementById('openOrderModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const res = await fetch(`/api/v1/owner/restaurant/${url}/tables/${tableId}`, {
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

            allOrderDetails.push(data);
            document.getElementById('orderContent').innerHTML = orderHTML(data.data);
        }

        function closeOrderModal() {
            document.getElementById('openOrderModal').classList.add('hidden');
            allOrderDetails.length = 0;
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.remove('flex');
            document.getElementById('historyModal').classList.add('hidden');
        }

        // ── Activity config ──
        const ACT_CFG = {
            created: {
                bg: 'bg-blue-100',
                icon: 'text-blue-500',
                path: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
            },
            item_added: {
                bg: 'bg-green-100',
                icon: 'text-green-600',
                path: 'M12 6v6m0 0v6m0-6h6m-6 0H6'
            },
            item_removed: {
                bg: 'bg-orange-100',
                icon: 'text-orange-500',
                path: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
            },
            qty_updated: {
                bg: 'bg-blue-100',
                icon: 'text-blue-500',
                path: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
            },
            cancelled: {
                bg: 'bg-red-100',
                icon: 'text-red-500',
                path: 'M6 18L18 6M6 6l12 12'
            },
            checkout: {
                bg: 'bg-purple-100',
                icon: 'text-purple-600',
                path: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
            },
        };

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
                    return `${actor} added <span class="font-semibold text-gray-800">${meta.name||'item'}</span>${meta.quantity?` <span class="text-blue-600 font-semibold">×${meta.quantity}</span>`:''}`;
                case 'item_removed':
                    return `${actor} removed <span class="font-semibold text-gray-800">${meta.name||'item'}</span>`;
                case 'qty_updated':
                    return `${actor} updated <span class="font-semibold text-gray-800">${meta.name||'item'}</span> qty${meta.from!==undefined?` <span class="text-gray-400">(${meta.from} → <span class="text-blue-600 font-semibold">${meta.to}</span>)</span>`:''}`;
                case 'cancelled':
                    return `${actor} cancelled order${meta.reason?` <span class="text-gray-400">— ${meta.reason}</span>`:''}`;
                case 'checkout':
                    return `${actor} completed checkout`;
                default:
                    return `${actor} ${act.action.replace(/_/g,' ')}`;
            }
        }

        function orderHTML(order) {
            console.log(order)
            if (!order) return `<p class="text-gray-400 text-center mt-10">No active order</p>`;

            const table = order.table;
            const tableStatus = capitalize(table.status);
            const activeOrder = table.orders?.find(o => o?.status?.toLowerCase() === 'open');
            const isOpen = !!activeOrder;
            const currentOrder = activeOrder ?? {};
            const activities = isOpen ? currentOrder.activities ?? [] : [];
            const totalEarning = isOpen ? currentOrder.totalAmount : '-';

            let html = `
                <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-50 rounded-xl p-2">
                            <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800 text-sm md:text-base leading-tight">Order Details</h2>
                            <p class="text-xs text-gray-400">Table ${table.tableNumber} · ${tableStatus}</p>
                        </div>
                    </div>
                    <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <button onclick="toggleHistory()"
                    class="w-full flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2 md:py-2.5 transition text-xs md:text-sm border-b border-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    View All Orders Today
                </button>

                <div class="overflow-y-auto flex-1 px-4 md:px-5 py-4 space-y-4 md:space-y-5">

                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-4 md:p-5 text-white relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-28 h-28 bg-white/10 rounded-full"></div>
                        <div class="relative">
                            <div class="flex items-start justify-between mb-4 md:mb-5">
                                <div>
                                    <p class="text-blue-200 text-xs font-medium uppercase tracking-wider mb-1">Order ID</p>
                                    <p class="text-white font-extrabold text-xl md:text-2xl">${isOpen ? `#${currentOrder.id}` : 'N/A'}</p>
                                </div>
                                <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-full capitalize">
                                    ${isOpen ? currentOrder.status : 'N/A'}
                                </span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-blue-200 text-xs mb-1">Total Amount</p>
                                    <p class="text-white font-extrabold text-2xl md:text-3xl">Rs. ${totalEarning}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-blue-200 text-xs mb-1">Placed at</p>
                                    <p class="text-white text-xs md:text-sm font-semibold">${isOpen ? formatTime(currentOrder.createdAt) : '-'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Items Ordered</p>
                        <div class="space-y-2">
                            ${isOpen && currentOrder.orderItems?.length
                                ? currentOrder.orderItems.map((item, i) => `
                                                        <div class="flex items-center justify-between bg-gray-50 hover:bg-blue-50/60 rounded-xl px-3 md:px-4 py-2.5 md:py-3 transition group"
                                                             style="animation: slideUp ${0.1 + i * 0.06}s ease both;">
                                                            <div class="flex items-center gap-2 md:gap-3 min-w-0">
                                                                <div class="w-8 h-8 md:w-9 md:h-9 bg-white rounded-lg shadow-sm flex items-center justify-center text-xs font-bold text-blue-600 border border-gray-200 flex-shrink-0">
                                                                    ${item.menuItem.name}
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">${item.menuItem.name}</p>
                                                                    <p class="text-xs text-gray-400 mt-0.5">Rs.&nbsp;${item.price.toLocaleString()} &times; ${item.quantity}</p>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                                                                <span class="text-xs font-semibold px-2 py-1 rounded-full status-${item.status} hidden sm:inline">
                                                                    ${capitalize(item.status)}
                                                                </span>
                                                                <p class="font-bold text-gray-800 text-xs md:text-sm">
                                                                    Rs.&nbsp;${(item.price * item.quantity).toLocaleString()}
                                                                </p>
                                                            </div>
                                                        </div>`).join('')
                                : `<p class="text-sm text-gray-400 text-center py-4">No items ordered</p>`}
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Order Activity</p>
                        <div class="relative space-y-0">
                            ${!isOpen || !activities.length
                                ? `<p class="text-sm text-gray-400 text-center py-4">No activity recorded</p>`
                                : activities.map((act, i) => {
                                    const c = ACT_CFG[act.action] || ACT_CFG.created;
                                    const isLast = i === activities.length - 1;
                                    return `<div class="relative flex gap-3 ${isLast ? '' : 'pb-5'} timeline-item">
                                                            <div class="flex-shrink-0 w-7 h-7 ${c.bg} rounded-full flex items-center justify-center mt-0.5 z-10 ring-2 ring-white">
                                                                <svg class="h-3.5 w-3.5 ${c.icon}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${c.path}"/>
                                                                </svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0 pt-0.5">
                                                                <p class="text-xs md:text-sm text-gray-600 leading-snug">${buildActivitySentence(act)}</p>
                                                                <p class="text-xs text-gray-400 mt-1">${formatTime(act.created_at)}</p>
                                                            </div>
                                                        </div>`;
                                }).join('')}
                        </div>
                    </div>
                </div>

                <div class="px-4 md:px-5 py-3 md:py-4 border-t border-gray-100 flex-shrink-0">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs md:text-sm text-gray-500 font-medium">Grand Total</span>
                        <span class="text-lg md:text-xl font-extrabold text-gray-800">Rs. ${totalEarning}</span>
                    </div>
                    <button onclick="proceedToCheckout()"
                        class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-3 md:py-3.5 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-200 text-sm">
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Proceed to Checkout
                    </button>
                </div>`;

            return html;
        }
    </script>

    <script>
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const form = document.getElementById('addTableForm');
        const openBtn = document.getElementById('openDrawer');
        const closeBtn = document.getElementById('closeDrawer');

        function openDrawer() {
            drawer.classList.add('open');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            form.reset();
        }

        openBtn.addEventListener('click', openDrawer);
        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = {
                table_number: form.table_number.value,
                area_name: form.area_name.value
            };
            try {
                const res = await fetch('/api/v1/owner/restaurant/table/add', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Something went wrong ❌', 'error');
                    return;
                }
                showToast(data.message, 'success');
                closeDrawer();
                fetchTables();
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        });

        function switchMode(mode) {
            document.getElementById('panelSingle').classList.toggle('active', mode === 'single');
            document.getElementById('panelBulk').classList.toggle('active', mode === 'bulk');
            document.getElementById('tabSingle').classList.toggle('active', mode === 'single');
            document.getElementById('tabBulk').classList.toggle('active', mode === 'bulk');
            setTimeout(() => {
                document.getElementById(mode === 'bulk' ? 'bulkPrefix' : 'tableNumber').focus();
            }, 100);
            if (mode === 'bulk') updatePreview();
        }

        function getBulkNumbers() {
            const prefix = (document.getElementById('bulkPrefix').value || '').trim();
            const from = parseInt(document.getElementById('bulkFrom').value);
            const to = parseInt(document.getElementById('bulkTo').value);
            if (!prefix || isNaN(from) || isNaN(to) || from < 1 || to < from || (to - from) > 99) return null;
            const nums = [];
            for (let i = from; i <= to; i++) nums.push(`${prefix}${i}`.toUpperCase());
            return nums;
        }

        function updatePreview() {
            const preview = document.getElementById('rangePreview');
            const chips = preview.querySelector('.rp-chips');
            const nums = getBulkNumbers();
            if (!nums) {
                preview.classList.remove('has-items');
                chips.innerHTML = '<span class="rp-empty">Fill in prefix and range above…</span>';
                return;
            }
            preview.classList.add('has-items');
            chips.innerHTML = nums.map(n => `<span class="rp-chip">${n}</span>`).join('');
        }

        function submitBulk(e) {
            e.preventDefault();
            showToast('Working on', 'error')
            // const nums = getBulkNumbers();
            // if (!nums) { showToast('Check prefix and range values', 'error'); return; }
            // showToast(`${nums.length} tables queued (implement API call)`, 'success');
        }


        // QR download 
        document.getElementById('downloadQRBtn').addEventListener('click', async function() {

            showToast('Generating QR codes for your tables…', 'info');

            try {
                // 1. Call the API
                const response = await axios({
                    url: '/api/v1/owner/restaurant/tables/qr-pdf',
                    method: 'GET',
                    responseType: 'blob', // Important: Handle binary data
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                // 2. Create a URL for the binary data
                const url = window.URL.createObjectURL(new Blob([response.data]));

                // 3. Create a temporary link and click it
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'table_qr_codes.pdf');
                document.body.appendChild(link);
                link.click();

                // 4. Cleanup
                link.remove();
                window.URL.revokeObjectURL(url);

                showToast('QR PDF downloaded successfully!', 'success');
            } catch (error) {
                showToast('Failed to download QR PDF', 'error');
                console.error(error);
            }
        });
    </script>

@endsection
