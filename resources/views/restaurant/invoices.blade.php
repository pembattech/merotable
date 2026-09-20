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

        /* ── active filters bar ── */
        .filter-bar {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            transform: translateY(-4px);
            transition: grid-template-rows 220ms ease, opacity 200ms ease, transform 220ms ease, margin 220ms ease;
            margin-bottom: 0;
        }

        .filter-bar>.filter-bar-inner {
            overflow: hidden;
            min-height: 0;
        }

        .filter-bar.open {
            grid-template-rows: 1fr;
            opacity: 1;
            transform: translateY(0);
            margin-bottom: 20px;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #bfdbfe;
            color: #2563eb;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 6px 4px 10px;
            border-radius: 999px;
        }

        .filter-chip button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 999px;
            color: inherit;
            background: transparent;
            border: none;
            cursor: pointer;
            opacity: 0.7;
        }

        .filter-chip button:hover {
            opacity: 1;
            background: #eff6ff;
        }

        /* ── clear filters button (fades/scales in instead of popping) ── */
        .clear-filters-btn {
            opacity: 0;
            transform: scale(0.94);
            max-width: 0;
            padding-left: 0;
            padding-right: 0;
            margin: 0;
            overflow: hidden;
            white-space: nowrap;
            pointer-events: none;
            transition: opacity 200ms ease, transform 200ms ease, max-width 260ms ease, padding 260ms ease;
        }

        .clear-filters-btn.open {
            opacity: 1;
            transform: scale(1);
            max-width: 200px;
            padding-left: 14px;
            padding-right: 14px;
            pointer-events: auto;
        }

        /* ── invoice card entrance (used alongside the existing fade-up classes) ── */
        .card-enter {
            animation: cardIn 220ms ease both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .filter-bar,
            .clear-filters-btn,
            .card-enter {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
    </head>

    <!-- ── HEADER ── -->
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8">

        <div>
            <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">
                Invoices
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Manage all restaurant invoices
            </p>
        </div>

        <div class="flex items-center gap-3">

            <!-- Date Range -->
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2">

                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>

                <input type="date" id="dateFrom"
                    class="text-sm font-medium text-gray-600 outline-none border-none bg-transparent w-32">

                <span class="text-gray-300">—</span>

                <input type="date" id="dateTo"
                    class="text-sm font-medium text-gray-600 outline-none border-none bg-transparent w-32">
            </div>

            <!-- Clear filters (fades in only when a filter is active) -->
            <button id="clearFiltersBtn" onclick="clearAllFilters()"
                class="clear-filters-btn flex items-center gap-1.5 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-500 hover:border-red-300 hover:text-red-500 transition">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Clear filters
            </button>

            <!-- Export -->
            {{-- TODO: Already create a export function in [inovice controller] NOT IMPLEMENT! -- Fix! only export invoices shown on the screen  --}}
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

        <!-- Total Invoices -->
        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">

                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>

                </div>

                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Total Invoices
                    </p>

                    <p class="text-2xl font-extrabold text-gray-800" id="totalInvoices">
                        0
                    </p>
                </div>

            </div>
        </div>


        <!-- Paid -->
        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">

                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                </div>

                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Paid
                    </p>

                    <p class="text-2xl font-extrabold text-green-600" id="totalPaid">
                        Rs. 0
                    </p>
                </div>

            </div>
        </div>


        <!-- Unpaid -->
        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">

                    <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                </div>

                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Unpaid
                    </p>

                    <p class="text-2xl font-extrabold text-orange-500" id="totalUnpaid">
                        Rs. 0
                    </p>
                </div>

            </div>
        </div>


        <!-- Average -->
        <div class="bg-white border border-gray-100 rounded-2xl px-5 py-4">

            <div class="flex items-center gap-3 mb-3">

                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">

                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                </div>

                <div>
                    <p class="text-xs text-gray-400 font-medium">
                        Avg Invoice
                    </p>

                    <p class="text-2xl font-extrabold text-purple-600" id="avgInvoice">
                        Rs. 0
                    </p>
                </div>

            </div>
        </div>

    </div>


    <!-- ── ACTIVE FILTERS BAR (shows only while a filter is applied) ── -->
    <div id="filterBar" class="filter-bar">
        <div
            class="filter-bar-inner flex items-center gap-2 flex-wrap bg-blue-50 border border-blue-100 rounded-2xl px-4 py-2.5">

            <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8z" />
            </svg>

            <span class="text-xs font-semibold text-blue-600 mr-1">Filtered:</span>

            <div id="filterChips" class="flex items-center gap-2 flex-wrap"></div>

            <span id="filterCount" class="text-xs text-gray-500 ml-1"></span>

            <button onclick="clearAllFilters()" class="ml-auto text-xs font-semibold text-blue-600 hover:underline">
                Clear all
            </button>

        </div>
    </div>


    <!-- ── FILTERS / TABS ── -->
    <div class="flex items-center justify-between mb-5 fade-up d3">

        <div class="bg-white rounded-2xl border border-gray-100 p-1 flex gap-1">

            <button class="tab-btn active" onclick="setTab('all', this)">
                All
            </button>

            <button class="tab-btn" onclick="setTab('paid', this)">
                Paid
            </button>

            <button class="tab-btn" onclick="setTab('unpaid', this)">
                Unpaid
            </button>

            <button class="tab-btn" onclick="setTab('partial', this)">
                Partial
            </button>

        </div>


        <!-- Search -->
        <div class="flex items-center gap-3">

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


    <!-- ── EMPTY STATE ── -->
    <div id="emptyState" class="hidden text-center py-20">

        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">

            <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>

        </div>

        <p class="font-semibold text-gray-400">
            No invoices found
        </p>

        <p class="text-sm text-gray-300 mt-1">
            Try adjusting your filters
        </p>

    </div>


    <!-- ── PAGINATION ── -->
    <div id="pagination" class="flex items-center justify-center gap-4 mt-6 text-sm text-gray-600"></div>


    @include('staff.invoice')


    <script>

        const token = localStorage.getItem('auth_token');


        // ─────────────────────────────────────────────
        // STATE
        // ─────────────────────────────────────────────

        let INVOICES = [];

        let currentTab = 'all';

        let currentPage = 1;

        let isLoading = false;


        // ─────────────────────────────────────────────
        // DATE HELPERS
        // ─────────────────────────────────────────────

        function getToday() {

            const now = new Date();

            const offset = now.getTimezoneOffset();

            const localDate = new Date(
                now.getTime() - offset * 60 * 1000
            );

            return localDate.toISOString().split('T')[0];
        }


        function setDefaultDates() {

            const today = getToday();

            document.getElementById('dateFrom').value = today;

            document.getElementById('dateTo').value = today;

        }


        // ─────────────────────────────────────────────
        // GET INVOICES API
        // ─────────────────────────────────────────────

        async function getInvoices(params = {}) {

            const BASE_URL = "/api/v1/owner/restaurant/invoices";

            const query = new URLSearchParams();


            if (params.page) {
                query.append("page", params.page);
            }

            if (params.restaurant_id) {
                query.append("restaurant_id", params.restaurant_id);
            }

            if (params.payment_status) {
                query.append("payment_status", params.payment_status);
            }

            if (params.payment_method) {
                query.append("payment_method", params.payment_method);
            }

            if (params.date_from) {
                query.append("date_from", params.date_from);
            }

            if (params.date_to) {
                query.append("date_to", params.date_to);
            }

            if (params.search) {
                query.append("search", params.search);
            }

            if (params.per_page) {
                query.append("per_page", params.per_page);
            }

            if (params.sort_by) {
                query.append("sort_by", params.sort_by);
            }

            if (params.sort_order) {
                query.append("sort_order", params.sort_order);
            }


            const url = query.toString()
                ? `${BASE_URL}?${query.toString()}`
                : BASE_URL;


            const response = await fetch(url, {

                method: "GET",

                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": `Bearer ${token}`,
                },

            });


            if (!response.ok) {

                let error = {};

                try {
                    error = await response.json();
                } catch (e) {
                    // Ignore JSON parsing error
                }

                throw new Error(
                    error.message ??
                    `HTTP error: ${response.status}`
                );

            }


            const json = await response.json();


            return {
                invoices: json.data ?? [],
                summary: json.summary ?? {},
                meta: json.meta ?? {},
            };

        }


        // ─────────────────────────────────────────────
        // RENDER INVOICES
        // ─────────────────────────────────────────────

        function renderInvoices(list) {

            const grid = document.getElementById('invoicesGrid');

            const empty = document.getElementById('emptyState');

            const delays = [
                'd1',
                'd2',
                'd3',
                'd4',
                'd5',
                'd6'
            ];


            if (!list.length) {

                grid.innerHTML = '';

                empty.classList.remove('hidden');

                return;

            }


            empty.classList.add('hidden');


            grid.innerHTML = list.map((inv, i) => {

                const delay = delays[i % delays.length];

                // small stagger so the list eases in rather than jumping into place
                const enterDelay = Math.min(i * 25, 200);

                const paymentStatus =
                    inv.order?.paymentStatus ?? 'pending';

                const paymentMethod =
                    inv.order?.paymentMethod ?? 'cash';


                const statusClass =
                    `status-${paymentStatus}`;


                const statusText =
                    paymentStatus.charAt(0).toUpperCase() +
                    paymentStatus.slice(1);


                const methodText =
                    paymentMethod.charAt(0).toUpperCase() +
                    paymentMethod.slice(1);


                return `
                        <div
                            class="invoice-card fade-up ${delay} card-enter"
                            style="animation-delay: ${enterDelay}ms"
                            onclick="viewInvoice('${inv.invoiceNumber}')"
                        >

                            <div class="flex items-start justify-between mb-3">

                                <div>

                                    <p class="font-bold text-gray-800">
                                        ${inv.invoiceNumber}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        ${formatDate(
                    inv.order?.createdAt
                )}
                                    </p>

                                </div>


                                <div class="flex gap-1 flex-wrap justify-end">

                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-50">
                                        ${methodText}
                                    </span>

                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full ${statusClass}">
                                        ${statusText}
                                    </span>

                                </div>

                            </div>


                            <div class="space-y-2 mb-3">

                                <div class="flex justify-between text-sm">

                                    <span class="text-gray-500">
                                        Table
                                    </span>

                                    <span class="font-semibold text-gray-800">
                                        ${inv.order?.tableNumber ?? '-'}
                                    </span>

                                </div>


                                <div class="flex justify-between text-sm">

                                    <span class="text-gray-500">
                                        Order #
                                    </span>

                                    <span class="font-semibold text-gray-800">
                                        ${inv.order?.id ?? '-'}
                                    </span>

                                </div>


                                <div class="flex justify-between text-sm">

                                    <span class="text-gray-500">
                                        Items
                                    </span>

                                    <span class="font-semibold text-gray-800">
                                        ${inv.order?.items?.length ?? 0} items
                                    </span>

                                </div>

                            </div>


                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">

                                <span class="text-xs text-gray-400">
                                    ${inv.customer ?? 'Walk-in'}
                                </span>

                                <span class="text-lg font-extrabold text-gray-800">
                                    Rs. ${Number(
                    inv.order?.totalAmount ?? 0
                ).toLocaleString()}
                                </span>

                            </div>

                        </div>
                    `;

            }).join('');

        }


        // ─────────────────────────────────────────────
        // FILTER
        // ─────────────────────────────────────────────

        function filterInvoices() {

            const q =
                (
                    document.getElementById('searchInput').value ||
                    ''
                ).toLowerCase().trim();


            let filtered = INVOICES;


            if (currentTab !== 'all') {

                const status =
                    currentTab === 'unpaid'
                        ? 'pending'
                        : currentTab;


                filtered = filtered.filter(inv =>
                    inv.order?.paymentStatus === status
                );

            }


            if (q) {

                filtered = filtered.filter(inv => {

                    const invoiceNumber =
                        String(inv.invoiceNumber ?? '')
                            .toLowerCase();

                    const tableNumber =
                        String(inv.order?.tableNumber ?? '')
                            .toLowerCase();

                    const orderId =
                        String(inv.order?.id ?? '')
                            .toLowerCase();


                    return (
                        invoiceNumber.includes(q) ||
                        tableNumber.includes(q) ||
                        orderId.includes(q)
                    );

                });

            }


            renderInvoices(filtered);

            updateFilterBar(filtered.length);

        }


        // ─────────────────────────────────────────────
        // ACTIVE FILTERS BAR + CLEAR FILTERS
        // ─────────────────────────────────────────────

        function updateFilterBar(filteredCount) {

            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const search = document.getElementById('searchInput').value.trim();

            const today = getToday();

            // Only count the date as "filtered" if it's been changed away from
            // the default today-today view (or one side was cleared on purpose).
            const hasDate = !(dateFrom === today && dateTo === today);

            const hasTab = currentTab !== 'all';
            const hasSearch = search !== '';
            const anyFilter = hasDate || hasTab || hasSearch;

            const bar = document.getElementById('filterBar');
            const btn = document.getElementById('clearFiltersBtn');
            const chips = document.getElementById('filterChips');
            const count = document.getElementById('filterCount');

            // toggling a class (rather than hidden/flex) lets the CSS transition
            // ease the bar and button in/out instead of an abrupt jump
            bar.classList.toggle('open', anyFilter);
            btn.classList.toggle('open', anyFilter);

            chips.innerHTML = '';

            if (hasDate) {

                let label;

                if (dateFrom && dateTo) {
                    label = dateFrom === dateTo
                        ? `On ${formatDate(dateFrom + ' 00:00:00')}`
                        : `${formatDate(dateFrom + ' 00:00:00')} \u2013 ${formatDate(dateTo + ' 00:00:00')}`;
                } else if (dateFrom) {
                    label = `From ${formatDate(dateFrom + ' 00:00:00')}`;
                } else {
                    label = `Until ${formatDate(dateTo + ' 00:00:00')}`;
                }

                chips.appendChild(makeFilterChip(label, clearDateFilter));
            }

            if (hasTab) {
                chips.appendChild(makeFilterChip('Status: ' + capitalize(currentTab), resetTabFilter));
            }

            if (hasSearch) {
                chips.appendChild(makeFilterChip(`Search: "${search}"`, clearSearchFilter));
            }

            count.textContent = anyFilter
                ? `\u00b7 Showing ${filteredCount ?? INVOICES.length} of ${INVOICES.length} invoices`
                : '';
        }


        function makeFilterChip(label, onRemove) {

            const chip = document.createElement('span');
            chip.className = 'filter-chip';
            chip.innerHTML = `<span>${label}</span>`;

            const btn = document.createElement('button');
            btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" class="w-3 h-3"><path d="M18 6L6 18M6 6l12 12"/></svg>`;
            btn.addEventListener('click', onRemove);

            chip.appendChild(btn);

            return chip;
        }


        function clearDateFilter() {

            // document.getElementById('dateFrom').value = '';
            // document.getElementById('dateTo').value = '';

            setDefaultDates();

            currentPage = 1;

            fetchAndRender(currentPage);
        }


        function resetTabFilter() {

            currentTab = 'all';

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.tab-btn').classList.add('active');

            filterInvoices();
        }


        function clearSearchFilter() {

            document.getElementById('searchInput').value = '';

            filterInvoices();
        }


        function clearAllFilters() {

            setDefaultDates();
            document.getElementById('searchInput').value = '';

            currentTab = 'all';

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.tab-btn').classList.add('active');

            currentPage = 1;

            fetchAndRender(currentPage);
        }


        // ─────────────────────────────────────────────
        // TABS
        // ─────────────────────────────────────────────

        function setTab(tab, btn) {

            currentTab = tab;


            document
                .querySelectorAll('.tab-btn')
                .forEach(b =>
                    b.classList.remove('active')
                );


            btn.classList.add('active');


            filterInvoices();

        }


        // ─────────────────────────────────────────────
        // VIEW INVOICE
        // ─────────────────────────────────────────────

        function viewInvoice(invoiceNumber) {

            const invoiceData =
                INVOICES.find(
                    inv =>
                        inv.invoiceNumber === invoiceNumber
                );


            if (!invoiceData) {
                return;
            }


            openInvoiceModal(invoiceData);

        }


        // ─────────────────────────────────────────────
        // EXPORT
        // ─────────────────────────────────────────────

        function exportInvoices() {

            if (!INVOICES.length) {

                showToast(
                    'No invoices to export',
                    'error'
                );

                return;

            }


            showToast(
                'Exporting invoices...',
                'success'
            );


            const restaurantName =
                INVOICES[0]?.restaurant?.restaurantName
                    ?.replace(/\s+/g, '_') ??
                'restaurant';


            const rows = INVOICES.map(invoice => [

                invoice.invoiceNumber,

                formatDateTime(
                    invoice.order?.createdAt
                ),

                invoice.order?.tableNumber ?? '',

                invoice.order?.id ?? '',

                invoice.customer ?? 'Walk-in',

                invoice.order?.paymentMethod ?? '',

                capitalize(
                    invoice.order?.paymentStatus ?? ''
                ),

                invoice.order?.subtotal ?? 0,

                invoice.order?.taxPercentage ?? 0,

                invoice.order?.taxAmount ?? 0,

                invoice.order?.serviceChargePercentage ?? 0,

                invoice.order?.serviceCharge ?? 0,

                invoice.order?.discountAmount ?? 0,

                invoice.order?.totalAmount ?? 0,

                invoice.order?.paidAt
                    ? formatDateTime(invoice.order.paidAt)
                    : ''

            ].map(value =>

                `"${String(value).replace(/"/g, '""')}"`

            ).join(','));


            const csv = [

                [
                    "Invoice",
                    "Date",
                    "Table",
                    "Order",
                    "Customer",
                    "Payment Method",
                    "Status",
                    "Subtotal",
                    "Tax %",
                    "Tax",
                    "Service Charge %",
                    "Service Charge",
                    "Discount",
                    "Total",
                    "Paid At"
                ].join(','),

                ...rows

            ].join('\n');


            const blob = new Blob(
                [csv],
                {
                    type: "text/csv;charset=utf-8;"
                }
            );


            const url =
                URL.createObjectURL(blob);


            const a =
                document.createElement("a");


            a.href = url;


            a.download =
                `${restaurantName}-mt-invoices-${getToday()}.csv`;


            document.body.appendChild(a);

            a.click();

            a.remove();


            URL.revokeObjectURL(url);

        }


        // ─────────────────────────────────────────────
        // DATE CHANGE
        // ─────────────────────────────────────────────

        function handleDateChange() {

            const dateFrom =
                document.getElementById('dateFrom').value;

            const dateTo =
                document.getElementById('dateTo').value;


            // Make sure From is not after To (only relevant when both are set)
            if (dateFrom && dateTo && dateFrom > dateTo) {

                document.getElementById('dateTo').value =
                    dateFrom;

            }


            // Refetch even when a date was cleared, so "no date" correctly
            // shows all invoices and the filter bar updates either way.
            currentPage = 1;

            fetchAndRender(currentPage);

        }


        // ─────────────────────────────────────────────
        // FETCH + RENDER
        // ─────────────────────────────────────────────

        async function fetchAndRender(page = 1) {

            if (isLoading) {
                return;
            }


            isLoading = true;


            try {

                const dateFrom =
                    document.getElementById('dateFrom').value;

                const dateTo =
                    document.getElementById('dateTo').value;


                const {
                    invoices,
                    summary,
                    meta
                } = await getInvoices({

                    page,

                    per_page: 15,

                    date_from: dateFrom,

                    date_to: dateTo

                });


                INVOICES = invoices;


                // summary
                document.getElementById('totalInvoices').textContent =
                    summary.total_invoices ?? 0;


                document.getElementById('totalPaid').textContent =
                    `Rs. ${Number(
                        summary.paid_amount ?? 0
                    ).toLocaleString()}`;


                document.getElementById('totalUnpaid').textContent =
                    `Rs. ${Number(
                        summary.unpaid_amount ?? 0
                    ).toLocaleString()}`;


                document.getElementById('avgInvoice').textContent =
                    `Rs. ${Number(
                        summary.average_invoice ?? 0
                    ).toLocaleString(undefined, {
                        maximumFractionDigits: 2
                    })}`;


                filterInvoices();


                renderPagination(meta);

            } catch (error) {

                // console.error(
                //     'Failed to fetch invoices:',
                //     error
                // );


                showToast(
                    error.message ||
                    'Failed to load invoices',
                    'error'
                );

            } finally {

                isLoading = false;

            }

        }


        // ─────────────────────────────────────────────
        // PAGINATION
        // ─────────────────────────────────────────────

        function renderPagination(meta) {

            const container =
                document.getElementById('pagination');


            if (!meta || !meta.last_page || meta.last_page <= 1) {

                container.innerHTML = '';

                return;

            }


            container.innerHTML = `

                    <button
                        onclick="changePage(${meta.current_page - 1})"
                        ${meta.current_page === 1 ? 'disabled' : ''}
                        class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        Prev
                    </button>


                    <span>
                        Page ${meta.current_page}
                        of ${meta.last_page}
                        — Total: ${meta.total}
                    </span>


                    <button
                        onclick="changePage(${meta.current_page + 1})"
                        ${meta.current_page === meta.last_page ? 'disabled' : ''}
                        class="px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        Next
                    </button>

                `;

        }


        async function changePage(page) {

            if (
                isLoading ||
                page < 1
            ) {
                return;
            }


            currentPage = page;


            await fetchAndRender(
                currentPage
            );

        }


        // ─────────────────────────────────────────────
        // HELPERS
        // ─────────────────────────────────────────────

        function formatDate(date) {

            if (!date) {
                return '-';
            }


            const d = new Date(
                date.replace(' ', 'T')
            );


            if (isNaN(d.getTime())) {
                return '-';
            }


            return d.toLocaleDateString(
                'en-US',
                {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                }
            );

        }


        function formatDateTime(date) {

            if (!date) {
                return '';
            }


            const d = new Date(
                date.replace(' ', 'T')
            );


            if (isNaN(d.getTime())) {
                return '';
            }


            return d.toLocaleString(
                'en-US',
                {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

        }


        function capitalize(value) {

            if (!value) {
                return '';
            }


            return value.charAt(0).toUpperCase() +
                value.slice(1);

        }


        // ─────────────────────────────────────────────
        // DATE EVENTS
        // ─────────────────────────────────────────────

        document
            .getElementById('dateFrom')
            .addEventListener(
                'change',
                handleDateChange
            );


        document
            .getElementById('dateTo')
            .addEventListener(
                'change',
                handleDateChange
            );


        // ─────────────────────────────────────────────
        // INITIAL LOAD
        // ─────────────────────────────────────────────

        document.addEventListener(
            'DOMContentLoaded',
            async () => {

                // Default date range = today
                setDefaultDates();


                // Initial API request
                await fetchAndRender(
                    currentPage
                );


                // Refresh every 15 seconds
                setInterval(
                    () => {

                        if (!isLoading) {
                            fetchAndRender(
                                currentPage
                            );
                        }

                    },
                    15000
                );

            }
        );

    </script>

@endsection