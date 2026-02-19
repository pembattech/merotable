@extends('layouts.staff')

@section('title', 'Cashier Billing | ' . config('app.name'))

@section('content')
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp 0.35s ease both;
        }

        .d1 {
            animation-delay: .04s
        }

        .d2 {
            animation-delay: .08s
        }

        .d3 {
            animation-delay: .12s
        }

        .d4 {
            animation-delay: .16s
        }

        .d5 {
            animation-delay: .20s
        }

        .d6 {
            animation-delay: .24s
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-panel {
            animation: slideUp 0.26s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        /* table card */
        .table-card:hover {
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.12);
            transform: translateY(-2px);
        }

        .table-card.active {
            border-color: #22c55e;
        }


        /* scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        .modal-scroll::-webkit-scrollbar {
            width: 4px;
        }

        /* field input */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        /* payment method pills */
        .method-btn {
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            background: #fff;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
        }

        .method-btn:hover {
            border-color: #cbd5e1;
        }

        .method-btn.selected {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* selected item pill */
        .selected-item-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #eff6ff;
            border: 1.5px solid #3b82f6;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1e40af;
        }

        .selected-item-pill .remove-btn {
            width: 16px;
            height: 16px;
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
    </style>
    </head>

    <body class="min-h-screen p-5 md:p-8">
        <div class="max-w-7xl mx-auto">

            <!-- ── HEADER ── -->
            <div class="flex items-center justify-between mb-6 fade-up d1">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Cashier Billing</h1>
                    <p class="text-sm text-gray-400 mt-1">Select table or search by items</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="clearSelection()"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2.5 rounded-xl transition text-sm">
                        Clear
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- ══════════════════════════════════════════════
                                             LEFT: TABLE SELECTION + SEARCH
                                        ══════════════════════════════════════════════ -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Search by items -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 fade-up d2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800">Search by Items</h3>
                            <span class="text-xs text-gray-400">Can't remember table?</span>
                        </div>

                        <!-- Selected items -->
                        <div id="selectedItems" class="mb-3 hidden">
                            <div class="flex flex-wrap gap-2" id="selectedItemsContainer"></div>
                        </div>

                        <div class="relative">
                            <input id="itemSearch" type="text" placeholder="Search items in orders..."
                                oninput="selectedItemNames.size === 0 ? showQuickSearch() : searchByItems()"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <div id="searchResults" class="mt-3 space-y-2 max-h-64 overflow-y-auto"></div>
                    </div>

                    <!-- Table Grid -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 fade-up d3">
                        <h3 class="font-bold text-gray-800 mb-4">Select Table</h3>
                        <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3" id="tableGrid"></div>
                    </div>

                </div>

                <!-- ══════════════════════════════════════════════
                                             RIGHT: ORDER DETAILS + CHECKOUT
                                        ══════════════════════════════════════════════ -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-6 fade-up d4">

                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800">Order Summary</h3>
                            <span id="selectedTableBadge"
                                class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 hidden">—</span>
                        </div>

                        <!-- Order items -->
                        <div class="border-t border-b border-gray-100 py-4 mb-4">
                            <div id="orderItems" class="space-y-3 max-h-96 overflow-y-auto">
                                <div class="text-center py-12 text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-sm">No table selected</p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="subtotal" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax (13%)</span>
                                <span id="tax" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Service Charge (10%)</span>
                                <span id="service" class="font-semibold text-gray-800">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-800">Total</span>
                                <span id="total" class="text-blue-600">Rs. 0</span>
                            </div>
                        </div>

                        <!-- Checkout button -->
                        <button id="checkoutBtn" onclick="openCheckoutModal()" disabled
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Proceed to Checkout
                        </button>

                    </div>
                </div>

            </div>

        </div>

        <!-- ══════════════════════════════════════════════
                                         CHECKOUT MODAL
                                    ══════════════════════════════════════════════ -->
        <div id="checkoutModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeCheckoutModal()"></div>
            <div
                class="modal-panel relative bg-white w-full sm:max-w-md sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[92vh]">

                <!-- header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-50 rounded-xl p-2">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-800 text-base">Payment</h2>
                            <p class="text-xs text-gray-400" id="checkoutTableLabel">—</p>
                        </div>
                    </div>
                    <button onclick="closeCheckoutModal()"
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-2 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- body -->
                <div class="overflow-y-auto modal-scroll flex-1 px-5 py-5 space-y-5">

                    <!-- Total display -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white">
                        <p class="text-blue-200 text-xs font-semibold uppercase tracking-wider mb-1">Total Amount</p>
                        <p class="text-4xl font-extrabold" id="modalTotal">Rs. 0</p>
                    </div>

                    <!-- Payment method -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button class="method-btn selected" onclick="selectMethod(this, 'cash')">
                                <svg class="h-5 w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Cash
                            </button>
                            <button class="method-btn" onclick="selectMethod(this, 'card')">
                                <svg class="h-5 w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Card
                            </button>
                            <button class="method-btn" onclick="selectMethod(this, 'mobile')">
                                <svg class="h-5 w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Mobile Pay
                            </button>
                            <button class="method-btn" onclick="selectMethod(this, 'other')">
                                <svg class="h-5 w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Other
                            </button>
                        </div>
                    </div>

                    <!-- Amount received (cash only) -->
                    <div id="cashSection">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Amount Received</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rs.</span>
                            <input id="amountReceived" type="number" min="0" placeholder="0"
                                oninput="calculateChange()" class="field-input pl-10" />
                        </div>
                        <div id="changeDisplay" class="mt-3 hidden">
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <p class="text-xs text-green-600 font-semibold mb-1">Change to Return</p>
                                <p class="text-2xl font-extrabold text-green-700" id="changeAmount">Rs. 0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Notes <span
                                class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea id="paymentNotes" rows="2" placeholder="Add payment notes..." class="field-input resize-none"></textarea>
                    </div>

                </div>

                <!-- footer -->
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/80 flex-shrink-0 flex gap-3">
                    <button onclick="closeCheckoutModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition">Cancel</button>
                    <button onclick="completePayment()"
                        class="flex-[2] bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Complete Payment
                    </button>
                </div>

            </div>
        </div>

        <script>
            const token = localStorage.getItem('auth_token');
            const url = localStorage.getItem('restro_url');

            async function fetchTables() {


                const res = await fetch(`/api/v1/staff/${url}/tables/overview`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accepts': 'application/json',
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

            //    // ── DATA ─────────────────────────────────────────────────────
            //     const TABLES = [{
            //             id: 'T-01',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 1240
            //         },
            //         {
            //             id: 'T-02',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-03',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 2850
            //         },
            //         {
            //             id: 'T-04',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-05',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 680
            //         },
            //         {
            //             id: 'T-06',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-07',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 1950
            //         },
            //         {
            //             id: 'T-08',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-09',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-10',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 3200
            //         },
            //         {
            //             id: 'T-11',
            //             status: 'available',
            //             orders: 0,
            //             total: 0
            //         },
            //         {
            //             id: 'T-12',
            //             status: 'occupied',
            //             orders: 1,
            //             total: 890
            //         },
            //     ];

            const ORDERS = {
                'T-01': [{
                        id: 1,
                        name: 'Chicken Biryani',
                        qty: 2,
                        price: 450,
                        total: 900
                    },
                    {
                        id: 2,
                        name: 'Masala Tea',
                        qty: 4,
                        price: 50,
                        total: 200
                    },
                    {
                        id: 3,
                        name: 'Spring Rolls',
                        qty: 1,
                        price: 140,
                        total: 140
                    },
                ],
                'T-03': [{
                        id: 4,
                        name: 'Butter Chicken',
                        qty: 2,
                        price: 500,
                        total: 1000
                    },
                    {
                        id: 5,
                        name: 'Garlic Naan',
                        qty: 6,
                        price: 80,
                        total: 480
                    },
                    {
                        id: 6,
                        name: 'Dal Makhani',
                        qty: 2,
                        price: 320,
                        total: 640
                    },
                    {
                        id: 7,
                        name: 'Mango Lassi',
                        qty: 3,
                        price: 150,
                        total: 450
                    },
                    {
                        id: 8,
                        name: 'Gulab Jamun',
                        qty: 2,
                        price: 140,
                        total: 280
                    },
                ],
                'T-05': [{
                        id: 9,
                        name: 'Veg Momo',
                        qty: 2,
                        price: 200,
                        total: 400
                    },
                    {
                        id: 10,
                        name: 'Coca Cola',
                        qty: 2,
                        price: 80,
                        total: 160
                    },
                    {
                        id: 11,
                        name: 'Chocolate Brownie',
                        qty: 1,
                        price: 120,
                        total: 120
                    },
                ],
                'T-07': [{
                        id: 12,
                        name: 'Tandoori Platter',
                        qty: 1,
                        price: 850,
                        total: 850
                    },
                    {
                        id: 13,
                        name: 'Butter Naan',
                        qty: 4,
                        price: 100,
                        total: 400
                    },
                    {
                        id: 14,
                        name: 'Dal Bhat Set',
                        qty: 2,
                        price: 350,
                        total: 700
                    },
                ],
                'T-10': [{
                        id: 15,
                        name: 'Grilled Fish',
                        qty: 1,
                        price: 700,
                        total: 700
                    },
                    {
                        id: 16,
                        name: 'Pizza Margherita',
                        qty: 1,
                        price: 550,
                        total: 550
                    },
                    {
                        id: 17,
                        name: 'Fried Rice',
                        qty: 3,
                        price: 280,
                        total: 840
                    },
                    {
                        id: 18,
                        name: 'Fresh Lime Soda',
                        qty: 4,
                        price: 100,
                        total: 400
                    },
                    {
                        id: 19,
                        name: 'Ice Cream Sundae',
                        qty: 2,
                        price: 200,
                        total: 400
                    },
                    {
                        id: 20,
                        name: 'Chicken Wings',
                        qty: 2,
                        price: 350,
                        total: 700
                    },
                ],
                'T-12': [{
                        id: 21,
                        name: 'Chowmein Chicken',
                        qty: 2,
                        price: 250,
                        total: 500
                    },
                    {
                        id: 22,
                        name: 'Veg Pakoda',
                        qty: 2,
                        price: 120,
                        total: 240
                    },
                    {
                        id: 23,
                        name: 'Masala Tea',
                        qty: 3,
                        price: 50,
                        total: 150
                    },
                ],
            };

            let selectedTable = null;
            let selectedMethod = 'cash';

            // Quick search mode — shows all items, click to add as filter
            function showQuickSearch() {
                const q = document.getElementById('itemSearch').value.toLowerCase().trim();
                const results = document.getElementById('searchResults');

                if (selectedItemNames.size > 0) return; // Already in filter mode
                if (!q) {
                    results.innerHTML = '';
                    return;
                }

                // Collect all unique item names that match
                const itemSet = new Set();
                Object.keys(ORDERS).forEach(tableId => {
                    ORDERS[tableId].forEach(item => {
                        if (item.name.toLowerCase().includes(q)) {
                            itemSet.add(item.name);
                        }
                    });
                });

                if (itemSet.size === 0) {
                    results.innerHTML = '<p class="text-xs text-gray-400 py-2">No items found</p>';
                    return;
                }

                const items = [...itemSet];
                results.innerHTML = `
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 mb-2">
      <p class="text-xs text-blue-600 font-semibold mb-2">💡 Click an item to filter tables by it</p>
    </div>
    ${items.map(name => `
                                                                                  <div class="flex items-center justify-between bg-gray-50 hover:bg-blue-50 rounded-lg px-3 py-2 cursor-pointer transition group"
                                                                                    onclick="addItemToSearch('${name.replace(/'/g, "\'")}'); document.getElementById('itemSearch').value='';">
                                                                                    <div class="flex items-center gap-2">
                                                                                      <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                                                      </svg>
                                                                                      <p class="text-sm font-semibold text-gray-800">${name}</p>
                                                                                    </div>
                                                                                    <svg class="h-4 w-4 text-blue-500 opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                                    </svg>
                                                                                  </div>
                                                                                `).join('')}
  `;
            }

            // ── RENDER TABLES ─────────────────────────────────────────────
            async function renderTables() {

                const response = await fetchTables();
                if (!response) return;

                const tables = response.data.tables;
                console.log(tables)

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

                if (!tables.length) {
                    document.getElementById('tableGrid').innerHTML =
                        '<p class="text-center text-gray-400 py-4">No tables found</p>';
                    return;
                }


                const tableGridHTML = tables.map(table => {
                    const s = statusStyles[table.status]; // background, border, badge styles

                    return `
        <div onclick="selectTable(${table.id})"
             class="table-card ${s.bg} ${s.border} ${s.ring}
                    p-4 rounded-2xl border shadow-sm
                    cursor-pointer transition
                    hover:scale-[1.03] hover:shadow-lg">

            <div class="flex justify-between items-center">
                <span class="text-xl font-extrabold tracking-wide">
                    ${table.table_number}
                </span>
                <span class="text-[9px] px-2 py-1 rounded-full font-semibold uppercase ${s.badge}">
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
                }).join(''); // join to a single string

                document.getElementById('tableGrid').innerHTML = tableGridHTML;

            }

            // ── SELECT TABLE ──────────────────────────────────────────────
            async function selectTable(tableId) {
                alert(tableId)

                const res = await fetch(`/api/v1/staff/${url}/tables/${tableId}`, {
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

                console.log(data)



                const table = data.data.table;
                const orders = data.data.table.orders;

                if (!table || table.status !== 'occupied') {
                    showToast('This table has no active orders', 'warning');
                    return;
                }

                selectedTable = table;

                // load orders
                renderOrderItems(table);

                // show table badge
                document.getElementById('selectedTableBadge').textContent = 'T' + tableId;
                document.getElementById('selectedTableBadge').classList.remove('hidden');

                // enable checkout
                document.getElementById('checkoutBtn').disabled = false;
            }

            // ── RENDER ORDER ITEMS ────────────────────────────────────────
            function renderOrderItems(table) {
                const container = document.getElementById('orderItems');

                if (!table.orders?.length) {
                    container.innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm">No orders</p>
            </div>
        `;
                    updateSummary(0);
                    return;
                }

                // Flatten all order items from all orders
                const items = table.orders.flatMap(order =>
                    order.order_items.map(i => ({
                        name: i.menu_item?.name || 'Unnamed Item',
                        price: i.price || 0,
                        qty: i.quantity || 1,
                        total: (i.price || 0) * (i.quantity || 1),
                        status: i.status || 'open',
                        orderId: order.id
                    }))
                );

                container.innerHTML = items.map(item => `
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <p class="font-semibold text-gray-800 text-sm">${item.name}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Rs. ${item.price} × ${item.qty} 
                    ${item.orderId ? `(Order #${item.orderId})` : ''}
                </p>
            </div>
            <p class="font-bold text-gray-800 text-sm">Rs. ${item.total.toLocaleString()}</p>
        </div>
    `).join('');

                // Total subtotal across all orders
                const subtotal = items.reduce((sum, i) => sum + i.total, 0);
                updateSummary(subtotal);
            }


            // ── UPDATE SUMMARY ────────────────────────────────────────────
            function updateSummary(subtotal) {
                const tax = Math.round(subtotal * 0.13);
                const service = Math.round(subtotal * 0.10);
                const total = subtotal + tax + service;

                document.getElementById('subtotal').textContent = `Rs. ${subtotal.toLocaleString()}`;
                document.getElementById('tax').textContent = `Rs. ${tax.toLocaleString()}`;
                document.getElementById('service').textContent = `Rs. ${service.toLocaleString()}`;
                document.getElementById('total').textContent = `Rs. ${total.toLocaleString()}`;
            }

            // ── SEARCH BY ITEMS ───────────────────────────────────────────
            let selectedItemNames = new Set();

            function searchByItems() {
                const q = document.getElementById('itemSearch').value.toLowerCase().trim();
                const results = document.getElementById('searchResults');

                if (!q && selectedItemNames.size === 0) {
                    results.innerHTML = '';
                    return;
                }

                // Build search query — combine selected items + new input
                const searchTerms = [...selectedItemNames];
                if (q) searchTerms.push(q);

                // Find tables that have ALL selected items + match current input
                const tableCandidates = {};

                Object.keys(ORDERS).forEach(tableId => {
                    const tableItems = ORDERS[tableId];
                    const tableItemNames = tableItems.map(i => i.name.toLowerCase());

                    // Check if this table has all selected items
                    const hasAllSelected = [...selectedItemNames].every(sel =>
                        tableItemNames.some(ti => ti.includes(sel.toLowerCase()))
                    );

                    if (!hasAllSelected) return;

                    // If typing new query, check if any item in this table matches
                    if (q) {
                        const matchesNewQuery = tableItems.some(item =>
                            item.name.toLowerCase().includes(q)
                        );
                        if (!matchesNewQuery) return;
                    }

                    // Collect all matching items from this table
                    tableCandidates[tableId] = tableItems.filter(item => {
                        const itemName = item.name.toLowerCase();
                        // Include if it's a selected item OR matches new query
                        return [...selectedItemNames].some(sel => itemName.includes(sel.toLowerCase())) ||
                            (q && itemName.includes(q));
                    });
                });

                const tableIds = Object.keys(tableCandidates);

                if (tableIds.length === 0) {
                    results.innerHTML = '<p class="text-xs text-gray-400 py-2">No tables match all selected items</p>';
                    return;
                }

                // Show matching tables with their items
                results.innerHTML = tableIds.map(tableId => {
                    const items = tableCandidates[tableId];
                    const tableTotal = items.reduce((sum, i) => sum + i.total, 0);

                    return `
    <div class="bg-gray-50 hover:bg-blue-50 rounded-xl p-3 cursor-pointer transition border border-gray-100 hover:border-blue-200"
      onclick="selectTableFromSearch('${tableId}')">
      <div class="flex items-center justify-between mb-2">
        <p class="font-bold text-gray-800 text-sm">Table ${tableId}</p>
        <p class="text-sm font-bold text-blue-600">Rs. ${tableTotal.toLocaleString()}</p>
      </div>
      <div class="space-y-1">
        ${items.map(item => `
                                                                                      <div class="flex items-center justify-between text-xs">
                                                                                        <span class="text-gray-600">${item.name} <span class="text-gray-400">×${item.qty}</span></span>
                                                                                        <span class="text-gray-500 font-medium">Rs. ${item.total}</span>
                                                                                      </div>
                                                                                    `).join('')}
      </div>
    </div>`;
                }).join('');
            }

            function addItemToSearch(itemName) {
                selectedItemNames.add(itemName);
                renderSelectedItems();
                searchByItems();
            }

            function removeItemFromSearch(itemName) {
                selectedItemNames.delete(itemName);
                renderSelectedItems();

                // Clear input if no items left
                if (selectedItemNames.size === 0) {
                    document.getElementById('itemSearch').value = '';
                }

                searchByItems();
            }

            function renderSelectedItems() {
                const container = document.getElementById('selectedItemsContainer');
                const wrapper = document.getElementById('selectedItems');

                if (selectedItemNames.size === 0) {
                    wrapper.classList.add('hidden');
                    return;
                }

                wrapper.classList.remove('hidden');
                container.innerHTML = [...selectedItemNames].map(name => `
    <div class="selected-item-pill">
      <span>${name}</span>
      <div class="remove-btn" onclick="removeItemFromSearch('${name.replace(/'/g, "\'")}')">
        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </div>
    </div>
  `).join('');
            }

            function selectTableFromSearch(tableId) {
                // Keep the search state intact
                const input = document.getElementById('itemSearch');
                const currentQuery = input.value;

                selectTable(tableId);

                // Restore search query
                input.value = currentQuery;
            }

            // ── CLEAR SELECTION ───────────────────────────────────────────
            function clearSelection() {
                selectedTable = null;
                selectedItemNames.clear();
                document.getElementById('selectedTableBadge').classList.add('hidden');
                document.getElementById('checkoutBtn').disabled = true;
                document.getElementById('itemSearch').value = '';
                document.getElementById('searchResults').innerHTML = '';
                renderSelectedItems();
                renderOrderItems([]);
            }

            // ── CHECKOUT MODAL ────────────────────────────────────────────
            function openCheckoutModal() {
                if (!selectedTable) return;

                console.log(selectedTable)

                const totalText = document.getElementById('total').textContent;
                document.getElementById('modalTotal').textContent = totalText;
                document.getElementById('checkoutTableLabel').textContent = `Table ${selectedTable.table_number}`;

                document.getElementById('amountReceived').value = '';
                document.getElementById('changeDisplay').classList.add('hidden');
                document.getElementById('paymentNotes').value = '';

                // reset to cash
                document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
                document.querySelector('.method-btn').classList.add('selected');
                selectedMethod = 'cash';
                document.getElementById('cashSection').style.display = 'block';

                document.getElementById('checkoutModal').classList.replace('hidden', 'flex');
            }

            function closeCheckoutModal() {
                document.getElementById('checkoutModal').classList.replace('flex', 'hidden');
            }

            // ── PAYMENT METHOD ────────────────────────────────────────────
            function selectMethod(btn, method) {
                document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                selectedMethod = method;

                // only show amount received for cash
                document.getElementById('cashSection').style.display = method === 'cash' ? 'block' : 'none';
            }

            // ── CALCULATE CHANGE ──────────────────────────────────────────
            function calculateChange() {
                const totalStr = document.getElementById('total').textContent.replace(/[^\d]/g, '');
                const total = parseInt(totalStr) || 0;
                const received = parseInt(document.getElementById('amountReceived').value) || 0;

                if (received > 0 && received >= total) {
                    const change = received - total;
                    document.getElementById('changeAmount').textContent = `Rs. ${change.toLocaleString()}`;
                    document.getElementById('changeDisplay').classList.remove('hidden');
                } else {
                    document.getElementById('changeDisplay').classList.add('hidden');
                }
            }

            // ── COMPLETE PAYMENT ──────────────────────────────────────────
            async function completePayment() {
                const totalStr = document.getElementById('total').textContent.replace(/[^\d]/g, '');
                const total = parseInt(totalStr) || 0;

                if (selectedMethod === 'cash') {
                    const received = parseInt(document.getElementById('amountReceived').value) || 0;
                    if (received < total) {
                        showToast('Amount received is less than total', 'error');
                        return;
                    }
                }

                // simulate payment
                showToast(`Payment completed for ${selectedTable.table_number}`, 'success');

                await updateTableStatus(selectedTable.id, selectedTable.orders[0].id, 'available');

                closeCheckoutModal();
                clearSelection();
                await renderTables();

                alert(selectedTable);
            }

            async function updateTableStatus(tableId, orderId, status) {
                const res = await fetch(`/api/v1/staff/${url}/table/${tableId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status
                    })
                });

                const data = await res.json();
                if (data.success) {

                    await updateOrderStatus(tableId, orderId, 'completed');
                }
            }

            async function updateOrderStatus(tableId, orderId, status) {
                const res = await fetch(`/api/v1/staff/${url}/table/${tableId}/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status
                    })
                });

                const data = await res.json();
                if (data.success) {
                    showToast('Payment successful. Order closed and table is now available.', 'success');

                    // TODO: create invoice and print bill

                }
            }
            // ── INIT ──────────────────────────────────────────────────────
            async function init() {
                await renderTables();
            }
            if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
            else init();
        </script>


    @endsection




    {{-- 
    
    **two sources of updates** for your tables/orders:

1. **Initial/fetch updates** → `async function fetchTables()`
2. **Real-time updates** → WebSocket / SSE events

Both need to **update your shared `ORDERS` object**, so your UI (grid, modal, search) stays accurate.

---

### ✅ Recommended structure

```js
let ORDERS = {}; // global state for all table orders

// 1️⃣ Async fetch for initial load
async function fetchTables() {
    const res = await fetch('/api/v1/staff/.../tables', {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await res.json();
    if (!data.success) return;

    updateOrders(data.data.tables); // populate ORDERS
    renderTables();                 // render table grid
}

// 2️⃣ Real-time updates via WebSocket
const socket = new WebSocket('wss://example.com/orders');

socket.onmessage = (event) => {
    const tableUpdate = JSON.parse(event.data);
    updateOrders([tableUpdate]); // merge new/updated orders
    renderTables();

    if (selectedTable === tableUpdate.id) {
        renderOrderItems(tableUpdate); // refresh modal if open
    }
}

// 3️⃣ Shared helper for merging new orders
function updateOrders(tables) {
    tables.forEach(table => {
        if (!ORDERS[table.id]) ORDERS[table.id] = [];

        table.orders.forEach(newOrder => {
            const idx = ORDERS[table.id].findIndex(o => o.id === newOrder.id);
            if (idx === -1) {
                ORDERS[table.id].push(newOrder); // append new order
            } else {
                ORDERS[table.id][idx] = newOrder; // update existing
            }
        });
    });
}

// 4️⃣ Initial page load
fetchTables();
```

---

### ⚡ Key points

* **Always use `await fetchTables()`** to load initial state.
* **WebSocket events** only merge/append/update — they don’t replace the entire `ORDERS` object.
* Your **search**, **order modal**, and **table grid rendering** all work off the **same `ORDERS` object**.
* No need to make `fetchTables()` itself a WebSocket; it’s just for initial population.

---

    --}}
