@extends('layouts.app')

@section('title', 'Invoices | ' . config('app.name'))

@section('content')

    <style>
          /* invoice card */
        .invoice-card {
            background: #fff;
            border: 1px solid #e9eef5;
            border-radius: 16px;
            padding: 18px;
            cursor: pointer;
            transition: all 0.18s;
        }

        .invoice-card:hover {
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.10);
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        /* status badges */
        .status-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-partial {
            background: #fef3c7;
            color: #d97706;
        }

        .status-refund {
            background: #f3f4f6;
            color: #6b7280;
        }

        /* tab */
        .tab-btn {
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .tab-btn.active {
            background: #2563eb;
            color: #fff;
        }
    </style>
    </head>


    <!-- ── HEADER ── -->
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8">
        <div>
            <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Invoices</h1>
            <p class="text-sm text-gray-400 mt-1">Manage all restaurant invoices</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- date range -->
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <input type="date" id="dateFrom"
                    class="text-sm font-medium text-gray-600 outline-none border-none bg-transparent w-32"
                    value="2024-02-01">
                <span class="text-gray-300">—</span>
                <input type="date" id="dateTo"
                    class="text-sm font-medium text-gray-600 outline-none border-none bg-transparent w-32"
                    value="2024-02-29">
            </div>
            <!-- export -->
            <button onclick="exportInvoices()"
                class="flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export All
            </button>
        </div>
    </header>

    <!-- ── SUMMARY CARDS ── -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 fade-up d2">
        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Total Invoices</p>
                    <p class="text-2xl font-extrabold text-gray-800" id="totalInvoices">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Paid</p>
                    <p class="text-2xl font-extrabold text-green-600">Rs. 2.8M</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Unpaid</p>
                    <p class="text-2xl font-extrabold text-orange-500">Rs. 180K</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Avg Invoice</p>
                    <p class="text-2xl font-extrabold text-purple-600">Rs. 21K</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── FILTERS / TABS ── -->
    <div class="flex items-center justify-between mb-5 fade-up d3">
        <div class="bg-white rounded-2xl border border-gray-100 p-1 flex gap-1">
            <button class="tab-btn active" onclick="setTab('all',this)">All</button>
            <button class="tab-btn" onclick="setTab('paid',this)">Paid</button>
            <button class="tab-btn" onclick="setTab('unpaid',this)">Unpaid</button>
            <button class="tab-btn" onclick="setTab('partial',this)">Partial</button>
        </div>

        <div class="flex items-center gap-3">
            <!-- search -->
            <div class="relative">
                <input id="searchInput" type="text" placeholder="Search invoices..." oninput="filterInvoices()"
                    class="pl-9 pr-4 py-2.5 border border-gray-200 bg-white rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none w-64 transition">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- ── INVOICES GRID ── -->
    <div id="invoicesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <!-- empty state -->
    <div id="emptyState" class="hidden text-center py-20">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <p class="font-semibold text-gray-400">No invoices found</p>
        <p class="text-sm text-gray-300 mt-1">Try adjusting your filters</p>
    </div>


    <div id="pagination" class="flex items-center justify-center gap-4 mt-6 text-sm text-gray-600"></div>

    </div>

    @include('staff.invoice')

    <script>
        const token = localStorage.getItem('auth_token');

        async function getInvoices(params = {}) {
            const BASE_URL = "/api/v1/owner/restaurant/invoices";

            const query = new URLSearchParams();

            if (params.restaurant_id) query.append("restaurant_id", params.restaurant_id);
            if (params.payment_status) query.append("payment_status", params.payment_status);
            if (params.payment_method) query.append("payment_method", params.payment_method);
            if (params.date_from) query.append("date_from", params.date_from);
            if (params.date_to) query.append("date_to", params.date_to);
            if (params.search) query.append("search", params.search);
            if (params.per_page) query.append("per_page", params.per_page);
            if (params.sort_by) query.append("sort_by", params.sort_by);
            if (params.sort_order) query.append("sort_order", params.sort_order);

            const url = query.toString() ? `${BASE_URL}?${query}` : BASE_URL;

            const response = await fetch(url, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`, // your auth token here
                },
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message ?? `HTTP error: ${response.status}`);
            }

            const json = await response.json();

            return {
                invoices: json.data,
                meta: json.meta,
            };
        }

        let INVOICES = [];

        let currentTab = 'all';

        function renderInvoices(list) {

            const grid = document.getElementById('invoicesGrid');
            const empty = document.getElementById('emptyState');
            const delays = ['d1', 'd2', 'd3', 'd4', 'd5', 'd6'];

            if (!list.length) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            grid.innerHTML = list.map((inv, i) => {
                const delay = delays[i % delays.length];
                const statusClass = `status-${inv.paymentStatus}`;
                const statusText = inv.paymentStatus.charAt(0).toUpperCase() + inv.paymentStatus.slice(1);


                return `
    <div class="invoice-card fade-up ${delay}" onclick="viewInvoice('${inv.invoiceNumber}')">
      <div class="flex items-start justify-between mb-3">
        <div>
          <p class="font-bold text-gray-800">${inv.invoiceNumber}</p>
          <p class="text-xs text-gray-400 mt-1">${formatDate(inv.paidAt)}</p>
        </div>

        <div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-50">${inv.paymentMethod.charAt(0).toUpperCase() + inv.paymentMethod.slice(1)}</span>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full ${statusClass}">${statusText}</span>
      </div>
      </div>
      
      <div class="space-y-2 mb-3">
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Table</span>
          <span class="font-semibold text-gray-800">${inv.tableNumber}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Order #</span>
          <span class="font-semibold text-gray-800">${inv.orderId}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Items</span>
          <span class="font-semibold text-gray-800">${inv.orderItems.length} items</span>
        </div>
      </div>
      
      <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
        <span class="text-xs text-gray-400">${inv.customer?? 'Walk-in'}</span>
        <span class="text-lg font-extrabold text-gray-800">Rs. ${inv.totalAmount.toLocaleString()}</span>
      </div>
    </div>`;
            }).join('');
        }

        // ── FILTER ────────────────────────────────────────────────────
        function filterInvoices() {
            const q = (document.getElementById('searchInput').value || '').toLowerCase();
            let filtered = INVOICES;

            if (currentTab !== 'all') {
                filtered = filtered.filter(inv => inv.paymentStatus === currentTab);
            }

            if (q) {
                filtered = filtered.filter(inv =>
                    inv.invoiceNumber.toLowerCase().includes(q) ||
                    inv.tableNumber.toLowerCase().includes(q) ||
                    inv.orderId.toString().includes(q)
                );
            }

            renderInvoices(filtered);
        }

        function setTab(tab, btn) {
            currentTab = tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterInvoices();
        }

        function viewInvoice(invoice_number) {
            const invoiceData = INVOICES.find(inv => inv.invoiceNumber === invoice_number);
            console.log(invoiceData)
            if (!invoiceData) return;
            openInvoiceModal(invoiceData)
        }


        function exportInvoices() {
            alert('Exporting all invoices...');
        }

        // ── HELPERS ───────────────────────────────────────────────────
        function formatDate(iso) {
            const d = new Date(iso);
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }

        // state
        let currentPage = 1;
        let isLoading = false;

        async function fetchAndRender(page = 1) {
            const {
                invoices,
                meta
            } = await getInvoices({
                page,
                per_page: 15
            });

            INVOICES = invoices;

            document.getElementById('totalInvoices').textContent = INVOICES.length;

            renderInvoices(INVOICES);
            renderPagination(meta);
        }

        function renderPagination(meta) {
            const container = document.getElementById('pagination');

            container.innerHTML = `
        <button onclick="changePage(${meta.current_page - 1})" 
            ${meta.current_page === 1 ? 'disabled' : ''}>
            Prev
        </button>

        <span>Page ${meta.current_page} of ${meta.last_page} — Total: ${meta.total}</span>

        <button onclick="changePage(${meta.current_page + 1})"
            ${meta.current_page === meta.last_page ? 'disabled' : ''}>
            Next
        </button>
    `;
        }

        async function changePage(page) {
            if (isLoading) return;
            isLoading = true;

            currentPage = page;
            await fetchAndRender(currentPage);

            isLoading = false;
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await fetchAndRender(currentPage);

            // poll every 15 seconds — stay on current page
            setInterval(() => fetchAndRender(currentPage), 15000);
        });
    </script>

@endsection