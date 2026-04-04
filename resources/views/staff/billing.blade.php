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
                        <input id="itemSearch" type="text" placeholder="Search items in orders…"
                            oninput="searchByItems()"
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
                 Mobile  → fixed bottom sheet (peek 60 px, tap to expand)
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
                                <span class="text-gray-600">Tax (13%)</span>
                                <span id="tax" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-xs md:text-sm">
                                <span class="text-gray-600">Service Charge (10%)</span>
                                <span id="service" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-base md:text-lg font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-800">Total</span>
                                <span id="total" class="text-blue-600">Rs. 0</span>
                            </div>
                        </div>

                        {{-- Checkout button --}}
                        <button id="checkoutBtn" onclick="openCheckoutModal()" disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:cursor-not-allowed
                                   text-white font-bold py-2.5 md:py-3 rounded-xl transition text-sm md:text-base
                                   flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Proceed to Checkout
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </div>{{-- /pb-20 wrapper --}}


    {{-- ══ CHECKOUT MODAL ══ --}}
    <div id="checkoutModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeCheckoutModal()"></div>
        <div
            class="relative bg-white w-full sm:max-w-md sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[92vh]">

            {{-- Drag handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div
                class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800 text-sm md:text-base">Payment</h2>
                        <p class="text-xs text-gray-400" id="checkoutTableLabel">—</p>
                    </div>
                </div>
                <button onclick="closeCheckoutModal()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-4 md:px-5 py-4 md:py-5 space-y-4 md:space-y-5">

                {{-- Total display --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-4 md:p-5 text-white">
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-wider mb-1">Total Amount</p>
                    <p class="text-3xl md:text-4xl font-extrabold" id="modalTotal">Rs. 0</p>
                </div>

                {{-- Payment method --}}
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2 md:mb-3">Payment Method</label>
                    <div class="grid grid-cols-2 gap-2 md:gap-3">
                        <button class="method-btn selected" onclick="selectMethod(this, 'cash')">
                            <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Cash
                        </button>
                        <button class="method-btn" onclick="selectMethod(this, 'card')">
                            <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Card
                        </button>
                        <button class="method-btn" onclick="selectMethod(this, 'mobile')">
                            <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Mobile Pay
                        </button>
                        <button class="method-btn" onclick="selectMethod(this, 'other')">
                            <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Other
                        </button>
                    </div>
                </div>

                {{-- Cash section --}}
                <div id="cashSection">
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Amount Received</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs md:text-sm font-semibold">Rs.</span>
                        <input id="amountReceived" type="number" min="0" placeholder="0"
                            oninput="calculateChange()" class="field-input" style="padding-left: 38px;" />
                    </div>
                    <div id="changeDisplay" class="mt-3 hidden">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 md:p-4">
                            <p class="text-xs text-green-600 font-semibold mb-1">Change to Return</p>
                            <p class="text-xl md:text-2xl font-extrabold text-green-700" id="changeAmount">Rs. 0</p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                        Notes <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea id="paymentNotes" rows="2" placeholder="Add payment notes…" class="field-input resize-none"></textarea>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-4 md:px-5 py-3 md:py-4 border-t border-gray-100 flex-shrink-0 flex gap-3">
                <button onclick="closeCheckoutModal()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                    Cancel
                </button>
                <button onclick="completePayment()"
                    class="flex-[2] bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition
                           shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Complete Payment
                </button>
            </div>

        </div>
    </div>

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
                return `
                <div onclick="selectTable(${table.id})"
     class="table-card ${s.bg} ${s.border} ${s.ring} p-2.5 md:p-4 rounded-2xl border shadow-sm cursor-pointer transition transform hover:scale-[1.03] hover:shadow-lg">
    
    <!-- Table number + status -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-2 sm:gap-0">
        <span class="text-base sm:text-lg md:text-xl font-extrabold tracking-wide">
            ${table.table_number}
        </span>
        <span class="text-[8px] sm:text-[9px] md:text-xs px-2 py-1 rounded-full font-semibold uppercase ${s.badge} text-center">
            ${table.status}
        </span>
    </div>

    <!-- Conditional: occupied or empty -->
    ${table.status === 'occupied'
        ? `<div class="mt-2 md:mt-4 text-center sm:text-left">
                           <p class="text-[10px] sm:text-xs md:text-sm opacity-80">Current Bill</p>
                           <p class="text-sm sm:text-base md:text-lg font-bold">Rs. ${table.total_amount}</p>
                       </div>`
        : `<div class="mt-4 md:mt-6 h-3 md:h-6"></div>`}
</div>`;
            }).join('');
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
            if (!table || table.status !== 'occupied') {
                showToast('This table has no active orders', 'warning');
                return;
            }

            selectedTable = table;
            renderOrderItems(table);

            // Update both desktop & mobile badges
            ['selectedTableBadge', 'selectedTableBadgeMobile'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = 'T' + tableId;
                    el.classList.remove('hidden');
                }
            });
            document.getElementById('checkoutBtn').disabled = false;

            // Auto-expand summary sheet on mobile after selecting a table
            if (window.innerWidth < 1024 && !summaryExpanded) toggleSummary();
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

            const items = table.orders.flatMap(order =>
                order.order_items.map(i => ({
                    name: i.menu_item?.name || 'Unnamed Item',
                    price: i.price || 0,
                    qty: i.quantity || 1,
                    total: (i.price || 0) * (i.quantity || 1),
                    orderId: order.id,
                }))
            );

            container.innerHTML = items.map(item => `
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-xs md:text-sm truncate">${item.name}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Rs. ${item.price} × ${item.qty}${item.orderId ? ` (Order #${item.orderId})` : ''}</p>
                    </div>
                    <p class="font-bold text-gray-800 text-xs md:text-sm flex-shrink-0">Rs. ${item.total.toLocaleString()}</p>
                </div>`).join('');

            updateSummary(items.reduce((sum, i) => sum + i.total, 0));
        }

        // ── Update summary totals ─────────────────────────────────
        function updateSummary(subtotal) {
            const tax = Math.round(subtotal * 0.13);
            const service = Math.round(subtotal * 0.10);
            const total = subtotal + tax + service;

            document.getElementById('subtotal').textContent = `Rs. ${subtotal.toLocaleString()}`;
            document.getElementById('tax').textContent = `Rs. ${tax.toLocaleString()}`;
            document.getElementById('service').textContent = `Rs. ${service.toLocaleString()}`;
            document.getElementById('total').textContent = `Rs. ${total.toLocaleString()}`;
            document.getElementById('totalPeek').textContent = `Rs. ${total.toLocaleString()}`;
        }

        // ── Search by items ───────────────────────────────────────
        let selectedItemNames = new Set();
        let searchTimeout, controller, searchController;

        function debounce(fn, delay) {
            let t;
            return function(...args) {
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

            container.innerHTML = data.tables.map(table => `
                <div class="bg-gray-50 hover:bg-blue-50 rounded-xl p-3 cursor-pointer transition border border-gray-100 hover:border-blue-200"
                    onclick="selectTableFromSearch('${table.id}')">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-gray-800 text-xs md:text-sm">Table ${table.table_number}</p>
                        <p class="text-xs md:text-sm font-bold text-blue-600">Rs. ${table.orders?.[0]?.total_amount.toLocaleString() ?? 0}</p>
                    </div>
                    ${table.orders.map(order =>
                        `<div class="space-y-1 pt-2">
                                                            ${order.order_items.map(oi => `
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
            selectedItemNames.clear();
            ['selectedTableBadge', 'selectedTableBadgeMobile'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            document.getElementById('checkoutBtn').disabled = true;
            document.getElementById('itemSearch').value = '';
            document.getElementById('searchResults').innerHTML = '';
            renderSelectedItems();
            renderOrderItems({
                orders: []
            });
            if (summaryExpanded) toggleSummary();
        }

        // ── Checkout modal ────────────────────────────────────────
        function openCheckoutModal() {
            if (!selectedTable) return;
            document.getElementById('modalTotal').textContent = document.getElementById('total').textContent;
            document.getElementById('checkoutTableLabel').textContent = `Table ${selectedTable.table_number}`;
            document.getElementById('amountReceived').value = '';
            document.getElementById('changeDisplay').classList.add('hidden');
            document.getElementById('paymentNotes').value = '';
            document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
            document.querySelector('.method-btn').classList.add('selected');
            selectedMethod = 'cash';
            document.getElementById('cashSection').style.display = 'block';
            document.getElementById('checkoutModal').classList.replace('hidden', 'flex');
        }

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.replace('flex', 'hidden');
        }

        function selectMethod(btn, method) {
            document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            selectedMethod = method;
            document.getElementById('cashSection').style.display = method === 'cash' ? 'block' : 'none';
        }

        function calculateChange() {
            const total = parseInt(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
            const received = parseInt(document.getElementById('amountReceived').value) || 0;
            if (received > 0 && received >= total) {
                document.getElementById('changeAmount').textContent = `Rs. ${(received - total).toLocaleString()}`;
                document.getElementById('changeDisplay').classList.remove('hidden');
            } else {
                document.getElementById('changeDisplay').classList.add('hidden');
            }
        }

        async function completePayment() {
            const total = parseInt(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
            if (selectedMethod === 'cash') {
                const received = parseInt(document.getElementById('amountReceived').value) || 0;
                if (received < total) {
                    showToast('Amount received is less than total', 'error');
                    return;
                }
            }
            try {

                const order = selectedTable.orders[0];
                if (!order) {
                    showToast('No active order found for this table', 'error');
                    return;
                }

                const response = await fetch(`/api/v1/staff/${url}/invoice`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        order_id: order.id,
                        table_id: selectedTable.id,
                        subtotal: total,
                        // TODO: Fetch the tax amount, discount amount and service charge from the restaurant setting 
                        tax_amount: 0,
                        discount_amount: 0,
                        service_charge: 0,
                        total_amount: total,
                        payment_method: selectedMethod
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Invoice failed');
                }

                openInvoiceModal(data.data)

                showToast(`Payment completed for ${selectedTable.table_number}`, 'success');

                await updateTableStatus(selectedTable.id, selectedTable.orders[0].id, 'available');

                closeCheckoutModal();
                clearSelection();

                await renderTables();

            } catch (error) {
                console.error(error);
                showToast('Payment failed: ' + error.message, 'error');
            }
        }

        async function updateTableStatus(tableId, orderId, status) {
            const res = await fetch(`/api/v1/staff/${url}/table/${tableId}/status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status
                }),
            });
            const data = await res.json();
            if (data.success) await updateOrderStatus(tableId, orderId, 'completed');
        }

        async function updateOrderStatus(tableId, orderId, status) {
            const res = await fetch(`/api/v1/staff/${url}/table/${tableId}/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status
                }),
            });
            const data = await res.json();
            if (data.success) showToast('Payment successful. Order closed and table is now available.', 'success');
        }

        // ── Init ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            renderTables();
            setInterval(renderTables, 10000);
        });
    </script>

@endsection
