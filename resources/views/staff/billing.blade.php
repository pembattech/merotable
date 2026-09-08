@extends('layouts.staff')

@section('title', 'Cashier Billing | ' . config('app.name'))

@section('content')

    <style>
        /* ── Table card ── */
        .table-card {
            transition: box-shadow 0.18s, transform 0.18s, border-color 0.18s;
        }

        .table-card:hover {
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.12);
            transform: translateY(-2px);
        }

        .table-card.active {
            border-color: #22c55e;
        }

        /* ── Payment method pills ── */
        .method-btn {
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
        }

        @media (min-width: 400px) {
            .method-btn {
                padding: 12px 16px;
                font-size: 0.875rem;
            }
        }

        .method-btn:hover {
            border-color: #cbd5e1;
        }

        .method-btn.selected {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* ── Selected item pill ── */
        .selected-item-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            background: #eff6ff;
            border: 1.5px solid #3b82f6;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e40af;
        }

        .selected-item-pill .remove-btn {
            width: 15px;
            height: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .selected-item-pill .remove-btn:hover {
            background: #1e40af;
        }

        /* ── Field input ── */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.6rem 0.875rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
            font-family: inherit;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        textarea.field-input {
            resize: none;
            line-height: 1.5;
        }

        /* ── Order Summary — mobile bottom sheet ── */
        #summaryPanel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -8px 40px rgba(0, 0, 0, 0.13);
            z-index: 30;
            transform: translateY(calc(100% - 60px));
            /* peek strip height */
            transition: transform 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        #summaryPanel.expanded {
            transform: translateY(0);
        }

        /* Desktop: revert to a plain sticky card */
        @media (min-width: 1024px) {
            #summaryPanel {
                position: sticky;
                top: 1.5rem;
                border-radius: 1rem;
                box-shadow: none;
                transform: none !important;
                max-height: none;
                overflow-y: visible;
            }

            #summaryPeekBar {
                display: none;
            }
        }

        /* Prevent page scroll when sheet is fully open */
        body.summary-open {
            overflow: hidden;
        }

        /* ── Disabled state for action buttons ── */
        #btn-print:disabled,
        #checkoutBtn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
    </style>


    {{-- Extra padding so content isn't hidden under the 60 px peek strip on mobile --}}
    <div class="pb-20 lg:pb-0">

        {{-- ── HEADER ── --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-5 md:mb-6">
            <div>
                <h1 class="text-lg md:text-2xl font-extrabold text-gray-900">Cashier Billing</h1>
                <p class="text-xs md:text-sm text-gray-400 mt-0.5">Select table or search by items</p>
            </div>
            <button onclick="clearSelection()"
                class="self-start sm:self-auto bg-gray-100 hover:bg-gray-200 text-gray-700
                                       font-semibold px-3 md:px-4 py-2 md:py-2.5 rounded-xl transition text-xs md:text-sm whitespace-nowrap">
                Clear Selection
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

            {{-- ══ LEFT: SEARCH + TABLE GRID ══ --}}
            <div class="lg:col-span-2 space-y-4 md:space-y-5">

                {{-- Search by items --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-3 md:mb-4">
                        <h3 class="font-bold text-gray-800 text-sm md:text-base">Search by Items</h3>
                        <span class="text-xs text-gray-400 hidden sm:block">Can't remember table?</span>
                    </div>

                    {{-- Selected item pills --}}
                    <div id="selectedItems" class="mb-3 hidden">
                        <div class="flex flex-wrap gap-2" id="selectedItemsContainer"></div>
                    </div>

                    <div class="relative">
                        <input id="itemSearch" type="text" placeholder="Search items in orders…" oninput="searchByItems()"
                            class="w-full pl-9 md:pl-10 pr-4 py-2 md:py-2.5 border border-gray-200 rounded-xl
                                                   text-xs md:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 md:h-5 md:w-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div id="searchResults" class="mt-3 space-y-2 max-h-48 md:max-h-64 overflow-y-auto"></div>
                </div>

                {{-- Table Grid --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5">
                    <h3 class="font-bold text-gray-800 text-sm md:text-base mb-3 md:mb-4">Select Table</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-2 md:gap-3"
                        id="tableGrid"></div>
                </div>

            </div>

            {{-- ══ RIGHT: ORDER SUMMARY ══
            Mobile → fixed bottom sheet (peek 60 px, tap to expand)
            Desktop → sticky sidebar
            --}}
            <div class="lg:col-span-1">
                <div id="summaryPanel" class="bg-white border border-gray-100">

                    {{-- ── Peek strip (mobile only) ── --}}
                    <div id="summaryPeekBar"
                        class="relative flex items-center justify-between px-5 pt-4 pb-3 cursor-pointer select-none"
                        onclick="toggleSummary()">
                        {{-- Drag handle --}}
                        <div class="absolute left-1/2 -translate-x-1/2 top-2 w-10 h-1 bg-gray-200 rounded-full"></div>

                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800 text-sm">Order Summary</span>
                            <span id="selectedTableBadgeMobile"
                                class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 hidden">—</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-blue-600" id="totalPeek">Rs. 0</span>
                            <svg id="peekChevron" class="h-4 w-4 text-gray-400 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- ── Summary body ── --}}
                    <div class="px-4 md:px-5 pb-5">

                        {{-- Desktop-only heading (peek bar covers it on mobile) --}}
                        <div class="hidden lg:flex items-center justify-between mb-4 pt-5">
                            <h3 class="font-bold text-gray-800">Order Summary</h3>
                            <span id="selectedTableBadge"
                                class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 hidden">—</span>
                        </div>

                        {{-- Bill-printed notice --}}
                        <div id="billPrintedNotice"
                            class="hidden items-center gap-1.5 text-[11px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M6 9V2h12v7" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                <rect x="6" y="14" width="12" height="8" />
                            </svg>
                            <span id="billPrintedNoticeText">Bill printed</span>
                        </div>

                        {{-- Order items --}}
                        <div class="border-t border-b border-gray-100 py-3 md:py-4 mb-3 md:mb-4">
                            <div id="orderItems" class="space-y-3 max-h-56 lg:max-h-80 xl:max-h-96 overflow-y-auto">
                                <div class="text-center py-8 md:py-12 text-gray-400">
                                    <svg class="mx-auto h-10 w-10 md:h-12 md:w-12 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-xs md:text-sm">No table selected</p>
                                </div>
                            </div>
                        </div>

                        {{-- Totals --}}
                        <div class="space-y-1.5 md:space-y-2 mb-3 md:mb-4">
                            <div class="flex justify-between text-xs md:text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="subtotal" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-xs md:text-sm">
                                <p class="text-gray-600">Tax
                                    <span id="taxperc" class="text-gray-600"></span>
                                </p>
                                <span id="tax" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-xs md:text-sm">
                                <p class="text-gray-600">Service Charge
                                    <span id="serviceperc" class="text-gray-600"></span>
                                </p>
                                <span id="service" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-base md:text-lg font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-800">Total</span>
                                <span id="total" class="text-blue-600">Rs. 0</span>
                            </div>
                        </div>


                        <div class="flex gap-2 mt-5">
                            <button id="btn-print" onclick="printBill()" disabled
                                class="flex-1 h-10 rounded-lg border border-slate-300 text-sm font-medium flex items-center justify-center gap-2 hover:bg-slate-50 active:scale-[0.98] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9V2h12v7" />
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                    <rect x="6" y="14" width="12" height="8" />
                                </svg>
                                <span id="btn-print-label">Print bill</span>
                            </button>

                            <button id="checkoutBtn" onclick="openCheckoutModal()" disabled
                                class="disabled:opacity-50 disabled:cursor-not-allowed flex-1 h-10 rounded-lg bg-indigo-600 text-white text-sm font-medium flex items-center justify-center gap-2 hover:bg-indigo-700 active:scale-[0.98] transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" />
                                    <line x1="1" y1="10" x2="23" y2="10" />
                                </svg>
                                Checkout
                            </button>
                        </div>


                        <p class="flex items-start gap-1.5 text-[11px] text-slate-400 mt-3 leading-snug">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0 mt-0.5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            Print bill keeps the table open. Checkout closes it and records payment.
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>


    @include('restaurant.partial.checkout_model')
    @include('staff.attendance')
    @include('staff.invoice')

    <script>
        const token = localStorage.getItem('auth_token');
        const url = localStorage.getItem('restro_url');

        // ── Summary bottom sheet (mobile) ─────────────────────────
        let summaryExpanded = false;

        function toggleSummary() {
            summaryExpanded = !summaryExpanded;
            document.getElementById('summaryPanel').classList.toggle('expanded', summaryExpanded);
            document.getElementById('peekChevron').style.transform = summaryExpanded ? 'rotate(180deg)' : '';
            document.body.classList.toggle('summary-open', summaryExpanded);
        }
        // Close on Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && summaryExpanded) toggleSummary();
        });
        // Collapse sheet when resizing to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && summaryExpanded) {
                summaryExpanded = false;
                document.getElementById('summaryPanel').classList.remove('expanded');
                document.body.classList.remove('summary-open');
            }
        });

        function showToast(message, type = 'info') {
            // Assumes a global toast helper exists elsewhere in the layout.
            // Fallback so this file never silently no-ops.
            if (typeof window.toast === 'function') {
                window.toast(message, type);
            } else {
                console[type === 'error' ? 'error' : 'log'](message);
            }
        }

        // ── Fetch tables ──────────────────────────────────────────
        async function fetchTables() {
            const res = await fetch(`/api/v1/staff/${url}/tables/overview?mode=billing`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            if (!data.success) {
                showToast(data.message || 'Something went wrong ❌', 'error');
                return;
            }
            return data;
        }

        let selectedTable = null;
        let currentSettings = null;
        let selectedMethod = 'cash';

        // ── Render tables ─────────────────────────────────────────
        async function renderTables() {
            const response = await fetchTables();
            if (!response) return;

            const tables = response.data.tables;
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
                },
            };

            if (!tables.length) {
                document.getElementById('tableGrid').innerHTML =
                    '<p class="col-span-full text-center text-gray-400 py-4 text-sm">No tables found</p>';
                return;
            }

            document.getElementById('tableGrid').innerHTML = tables.map(table => {
                const s = statusStyles[table.status] || statusStyles.available;
                // "bill_printed_at" is expected on the order tied to this table
                // (nullable timestamp — see orders.bill_printed_at migration).
                const billPrinted = table.status === 'occupied' && !!table.bill_printed_at;

                return `
                                <div onclick="selectTable(${table.id})"
                     class="table-card ${s.bg} ${s.border} ${s.ring} p-2.5 md:p-4 rounded-2xl border shadow-sm cursor-pointer transition transform hover:scale-[1.03] hover:shadow-lg">

                    <!-- Table number + status -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-2 sm:gap-0">
                        <span class="text-base sm:text-lg md:text-xl font-extrabold tracking-wide">
                            ${table.table_number}
                        </span>
                        ${billPrinted
                        ? `<span class="text-[8px] sm:text-[9px] md:text-xs px-2 py-1 rounded-full font-semibold uppercase bg-amber-100 text-amber-700 text-center flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                                    Billed
                                                </span>`
                        : `<span class="text-[8px] sm:text-[9px] md:text-xs px-2 py-1 rounded-full font-semibold uppercase ${s.badge} text-center">
                                                    ${table.status}
                                                </span>`}
                    </div>

                    <!-- Conditional: occupied or empty -->
                    ${table.status === 'occupied'
                        ? `<div class="mt-2 md:mt-4 text-center sm:text-left">
                                                                       <p class="text-[10px] sm:text-xs md:text-sm opacity-80">Current Bill</p>
                                                                       <p class="text-sm sm:text-base md:text-lg font-bold">Rs. ${table.total_amount}</p>
                                                                       ${billPrinted ? `<p class="text-[9px] sm:text-[10px] opacity-70 mt-0.5">Printed ${formatTime(table.bill_printed_at)}</p>` : ''}
                                                                   </div>`
                        : `<div class="mt-4 md:mt-6 h-3 md:h-6"></div>`}
                </div>`;
            }).join('');
        }

        function formatTime(isoString) {
            try {
                return new Date(isoString).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } catch (e) {
                return '';
            }
        }

        // ── Select table ──────────────────────────────────────────
        async function selectTable(tableId) {
            const res = await fetch(`/api/v1/staff/${url}/tables/${tableId}?mode=billing`, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await res.json();
            if (!data.success) {
                showToast(data.message || 'Something went wrong ❌', 'error');
                return;
            }

            const table = data.data.table;
            const settings = data.data.settings;
            if (!table || table.status !== 'occupied') {
                showToast('This table has no active orders', 'warning');
                return;
            }

            selectedTable = table;
            currentSettings = settings;
            console.log(selectedTable)
            renderOrderItems(table);
            renderBillPrintedNotice(table);

            // Update both desktop & mobile badges
            ['selectedTableBadge', 'selectedTableBadgeMobile'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = selectedTable.tableNumber;
                    el.classList.remove('hidden');
                }
            });
            document.getElementById('checkoutBtn').disabled = false;
            document.getElementById('btn-print').disabled = false;

            // Auto-expand summary sheet on mobile after selecting a table
            if (window.innerWidth < 1024 && !summaryExpanded) toggleSummary();
        }

        // ── Bill-printed notice ─────────────────────────────────────
        function renderBillPrintedNotice(table) {
            const notice = document.getElementById('billPrintedNotice');
            const text = document.getElementById('billPrintedNoticeText');
            const printLabel = document.getElementById('btn-print-label');
            const billPrintedAt = table?.orders[0]?.billPrintedAt;
            
            if (billPrintedAt) {
                text.textContent = `Bill printed at ${formatTime(billPrintedAt)}`;
                notice.classList.remove('hidden');
                notice.classList.add('flex');
                printLabel.textContent = 'Reprint bill';
            } else {
                notice.classList.add('hidden');
                notice.classList.remove('flex');
                printLabel.textContent = 'Print bill';
            }
        }

        // ── Render order items ────────────────────────────────────
        function renderOrderItems(table) {
            const container = document.getElementById('orderItems');

            if (!table.orders?.length) {
                container.innerHTML = `
                                    <div class="text-center py-8 text-gray-400">
                                        <svg class="mx-auto h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-sm">No orders</p>
                                    </div>`;
                updateSummary(0);
                return;
            }

            // NOTE: expects order.orderItems (camelCase) — keep this consistent
            // with whatever casing /tables/{id} actually returns. If your API
            // returns order_items instead, change the two references below.
            const items = table.orders.flatMap(order =>
                (order.orderItems || []).map(i => ({
                    name: i.menuItem || 'Unnamed Item',
                    price: i.price || 0,
                    qty: i.quantity || 1,
                    subTotal: (i.price || 0) * (i.quantity || 1),
                    orderId: order.id,
                }))
            );

            container.innerHTML = items.map(item => `
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">${item.name}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Rs. ${item.price} × ${item.qty}${item.orderId ? ` (Order #${item.orderId})` : ''}</p>
                                    </div>
                                    <p class="font-bold text-gray-800 text-xs md:text-sm flex-shrink-0">Rs. ${item.subTotal.toLocaleString()}</p>
                                </div>`).join('');

            updateSummary(items.reduce((sum, i) => sum + i.subTotal, 0));
        }

        // ── Update summary totals ─────────────────────────────────
        function updateSummary(subtotal) {
            // Guard against calls before any table (and therefore settings) has
            // ever been loaded — e.g. hitting "Clear Selection" on first load.
            if (!currentSettings) {
                document.getElementById('taxperc').textContent = '';
                document.getElementById('serviceperc').textContent = '';
                document.getElementById('subtotal').textContent = `Rs. 0`;
                document.getElementById('tax').textContent = `Rs. 0`;
                document.getElementById('service').textContent = `Rs. 0`;
                document.getElementById('total').textContent = `Rs. 0`;
                document.getElementById('totalPeek').textContent = `Rs. 0`;
                return;
            }

            let tax = 0;
            let serviceCharge = 0;

            if (currentSettings.taxEnabled) {
                tax = (subtotal * currentSettings.taxPercentage) / 100;
            }

            if (currentSettings.serviceChargeEnabled) {
                serviceCharge = (subtotal * currentSettings.serviceChargePercentage) / 100;
            }

            const grandTotal = subtotal + tax + serviceCharge;

            const completepaymentBtn = document.querySelector("#completePaymentBtn");
            if (completepaymentBtn) {
                completepaymentBtn.dataset.subtotal = subtotal;
                completepaymentBtn.dataset.grandtotal = grandTotal;
            }

            document.getElementById('taxperc').textContent = `(${currentSettings.taxPercentage}%)`;
            document.getElementById('serviceperc').textContent = `(${currentSettings.serviceChargePercentage}%)`;

            document.getElementById('subtotal').textContent = `Rs. ${subtotal.toLocaleString()}`;
            document.getElementById('tax').textContent = `Rs. ${tax.toLocaleString()}`;
            document.getElementById('service').textContent = `Rs. ${serviceCharge.toLocaleString()}`;
            document.getElementById('total').textContent = `Rs. ${grandTotal.toLocaleString()}`;
            document.getElementById('totalPeek').textContent = `Rs. ${grandTotal.toLocaleString()}`;
        }

        // ── Print bill ──────────────────────────────────────────────
        let printInFlight = false;

        async function printBill() {
            if (!selectedTable || printInFlight) return;

            printInFlight = true;
            const btn = document.getElementById('btn-print');
            btn.disabled = true;

            try {
                const res = await fetch(`/api/v1/staff/${url}/orders/print-bill`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ table_id: selectedTable.id })
                });
                const data = await res.json();

                if (!data.success) {
                    showToast(data.message || 'Could not print bill ❌', 'error');
                    return;
                }

                console.log(data)

                // Reflect the printed state immediately without waiting on the next poll cycle.
                selectedTable.bill_printed_at = data.data.printed_at;
                renderBillPrintedNotice(selectedTable);
                renderTables();

                openInvoiceModal(data.data, "printbill");

                // // Open the printable receipt in a new tab for the actual print.
                // window.open(`/${url}/orders/${data.data.order_id}/bill`, '_blank');

                showToast('Bill printed — table stays open', 'success');
            } catch (err) {
                console.error(err);
                showToast('Could not print bill ❌', 'error');
            } finally {
                printInFlight = false;
                btn.disabled = !selectedTable;
            }
        }

        // ── Search by items ───────────────────────────────────────
        let selectedItemNames = new Set();
        let searchTimeout, controller, searchController;

        function debounce(fn, delay) {
            let t;
            return function (...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), delay);
            };
        }
        const debouncedSearchTables = debounce(searchTables, 300);

        function searchByItems() {
            const query = document.getElementById('itemSearch').value.toLowerCase().trim();
            const results = document.getElementById('searchResults');

            if (query.length === 0 && selectedItemNames.size > 0) {
                results.innerHTML = '';
                debouncedSearchTables();
                return;
            }
            if (query.length < 2 && selectedItemNames.size === 0) {
                results.innerHTML = '';
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(async () => {
                if (controller) controller.abort();
                controller = new AbortController();
                try {
                    const res = await fetch(`/api/items?search=${encodeURIComponent(query)}`, {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        },
                        signal: controller.signal,
                    });
                    const data = await res.json();
                    if (!data.success) {
                        results.innerHTML = '<p class="text-gray-500 text-sm py-2">No items found</p>';
                        return;
                    }

                    results.innerHTML = data.items.map(item => `
                                        <div class="flex items-center justify-between bg-gray-50 hover:bg-blue-50 rounded-lg px-3 py-2 cursor-pointer transition group"
                                            onclick="addItemToSearch('${item.replace(/'/g, "\\'")}'); document.getElementById('itemSearch').value='';">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-3.5 w-3.5 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                <p class="text-xs md:text-sm font-semibold text-gray-800">${item}</p>
                                            </div>
                                            <svg class="h-3.5 w-3.5 text-blue-500 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>`).join('');
                } catch (err) {
                    if (err.name !== 'AbortError') console.error(err);
                }
            }, 250);
        }

        async function searchTables() {
            if (!selectedItemNames.size) return;
            if (searchController) searchController.abort();
            searchController = new AbortController();

            const params = new URLSearchParams();
            selectedItemNames.forEach(item => params.append('item[]', item));

            const res = await fetch(`/api/search-tables?${params.toString()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                signal: searchController.signal,
            });
            const data = await res.json();
            const container = document.getElementById('searchResults');

            if (!data.tables.length) {
                container.innerHTML = '<p class="text-gray-400 text-sm py-2">No tables found</p>';
                return;
            }

            // NOTE: this endpoint returns order_items (snake_case) while
            // /tables/{id} returns orderItems (camelCase) above. Confirm which
            // one your backend actually sends and make both consistent —
            // left as-is here since it reflects the original response shape.
            container.innerHTML = data.tables.map(table => `
                                <div class="bg-gray-50 hover:bg-blue-50 rounded-xl p-3 cursor-pointer transition border border-gray-100 hover:border-blue-200"
                                    onclick="selectTableFromSearch('${table.id}')">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-bold text-gray-800 text-xs md:text-sm">Table ${table.table_number}</p>
                                        <p class="text-xs md:text-sm font-bold text-blue-600">Rs. ${table.orders?.[0]?.total_amount.toLocaleString() ?? 0}</p>
                                    </div>
                                    ${(table.orders || []).map(order =>
                `<div class="space-y-1 pt-2">
                                                                                                        ${(order.order_items || []).map(oi => `
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-600">${oi.menu_item.name} <span class="text-gray-400">×${oi.quantity}</span></span>
                                                    <span class="text-gray-500 font-medium">Rs. ${(oi.price * oi.quantity).toLocaleString()}</span>
                                                </div>`).join('')}
                                                                                                    </div>`).join('')}
                                </div>`).join('');
        }

        function addItemToSearch(itemName) {
            selectedItemNames.add(itemName);
            renderSelectedItems();
            debouncedSearchTables();
        }

        function removeItemFromSearch(itemName) {
            selectedItemNames.delete(itemName);
            renderSelectedItems();
            if (!selectedItemNames.size) document.getElementById('itemSearch').value = '';
            debouncedSearchTables();
        }

        function renderSelectedItems() {
            const container = document.getElementById('selectedItemsContainer');
            const wrapper = document.getElementById('selectedItems');
            if (!selectedItemNames.size) {
                wrapper.classList.add('hidden');
                return;
            }
            wrapper.classList.remove('hidden');
            container.innerHTML = [...selectedItemNames].map(name => `
                                <div class="selected-item-pill">
                                    <span>${name}</span>
                                    <div class="remove-btn" onclick="removeItemFromSearch('${name.replace(/'/g, "\\'")}')">
                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                </div>`).join('');
        }

        function selectTableFromSearch(tableId) {
            const q = document.getElementById('itemSearch').value;
            selectTable(tableId);
            document.getElementById('itemSearch').value = q;
        }

        // ── Clear selection ───────────────────────────────────────
        function clearSelection() {
            selectedTable = null;
            // currentSettings intentionally kept — it's restaurant-wide config,
            // not per-table, so no need to re-fetch it on every table switch.
            selectedItemNames.clear();
            ['selectedTableBadge', 'selectedTableBadgeMobile'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            document.getElementById('checkoutBtn').disabled = true;
            document.getElementById('btn-print').disabled = true;
            document.getElementById('itemSearch').value = '';
            document.getElementById('searchResults').innerHTML = '';
            renderSelectedItems();
            renderBillPrintedNotice(null);
            renderOrderItems(
                {
                    orders: [],
                }
            );
            if (summaryExpanded) toggleSummary();
        }



        // ── Init ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('btn-print').disabled = true;
            renderTables();
            setInterval(renderTables, 10000);
        });
    </script>

@endsection