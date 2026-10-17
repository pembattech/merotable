@extends('layouts.app')

@section('title', 'Menu | ' . config('app.name'))

@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }

        /* body {
                background: #f0f4f8;
            } */

        /* period toggle */
        .period-btn {
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
            background: transparent;
            white-space: nowrap;
        }

        .period-btn:hover {
            background: #f1f5f9;
        }

        .period-btn.active {
            background: #1e293b;
            color: #fff;
        }

        /* cards */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e9eef5;
        }

        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e9eef5;
        }

        .report-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px 18px;
            border: 1px solid #e9eef5;
            transition: all 0.15s;
            display: block;
        }

        .report-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08);
            transform: translateY(-1px);
        }

        .report-card:hover .arrow {
            color: #2563eb;
            transform: translateX(3px);
        }

        .arrow {
            color: #cbd5e1;
            transition: all 0.15s;
        }

        /* progress bar */
        .prog-bar {
            height: 6px;
            border-radius: 3px;
            background: #e9eef5;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.8s cubic-bezier(.4, 0, .2, 1);
        }

        .tx-row {
            transition: background 0.12s;
        }

        .tx-row:hover {
            background: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .date-input {
            padding: 7px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #1f2937;
            background: #fff;
        }

        .date-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        /* fade-in stagger */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            opacity: 0;
            animation: fadeUp 0.5s ease forwards;
        }

        .d1 {
            animation-delay: 0.05s;
        }

        .d2 {
            animation-delay: 0.10s;
        }

        .d3 {
            animation-delay: 0.15s;
        }

        .d4 {
            animation-delay: 0.20s;
        }

        .d5 {
            animation-delay: 0.25s;
        }

        .d6 {
            animation-delay: 0.30s;
        }

        .d7 {
            animation-delay: 0.35s;
        }

        .d8 {
            animation-delay: 0.40s;
        }

        #content.is-loading {
            opacity: 0.45;
            pointer-events: none;
        }

        #content {
            transition: opacity 0.2s;
        }
    </style>
    </head>

    @php
        // Adjust these URLs to your real report routes (or swap url() for route())
        $reportLinks = [
            [
                'title' => 'Profit & Loss',
                'desc' => 'Income vs expenses, net profit',
                'url' => url('reports/profit-loss'),
                'color' => '#2563eb',
                'bg' => '#eff6ff',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
            ],
            [
                'title' => 'Sales & Payments',
                'desc' => 'Sales by payment method',
                'url' => url('reports/sales-payments'),
                'color' => '#16a34a',
                'bg' => '#f0fdf4',
                'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
            ],
            [
                'title' => 'Unpaid Bills & Dues',
                'desc' => 'Outstanding customer balances',
                'url' => url('reports/dues'),
                'color' => '#ea580c',
                'bg' => '#fff7ed',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            [
                'title' => 'Expenses',
                'desc' => 'Spending by category',
                'url' => url('reports/expenses'),
                'color' => '#7c3aed',
                'bg' => '#f5f3ff',
                'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
            ],
        ];
    @endphp

    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 fade-up d1">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Financial Insights</h1>
                <p class="text-sm text-gray-400 mt-1"><span id="restroName" class="restroName"></span> · Last updated just
                    now</p>
            </div>
            <button id="exportBtn" disabled
                class="flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export CSV
            </button>
        </div>

        <!-- DATE FILTER -->
        <div
            class="bg-white border border-gray-200 rounded-2xl p-3 mb-3 flex flex-col lg:flex-row lg:items-center gap-3 fade-up d2">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-1 flex gap-1 overflow-x-auto" id="presets">
                <button class="period-btn" data-preset="today">Today</button>
                <button class="period-btn" data-preset="yesterday">Yesterday</button>
                <button class="period-btn" data-preset="last7">7 Days</button>
                <button class="period-btn" data-preset="last30">30 Days</button>
                <button class="period-btn" data-preset="month">This Month</button>
                <button class="period-btn" data-preset="lastMonth">Last Month</button>
                <button class="period-btn" data-preset="custom">Custom</button>
            </div>
            <div class="flex-1"></div>
            <div class="flex items-center gap-2">
                <input type="date" id="dateFrom" class="date-input" aria-label="From date">
                <span class="text-gray-400 text-sm">to</span>
                <input type="date" id="dateTo" class="date-input" aria-label="To date">
                <button id="applyBtn"
                    class="hidden bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition disabled:opacity-60 disabled:cursor-not-allowed">
                    Apply
                </button>
            </div>
        </div>

        <div id="reportMessage" class="text-xs text-gray-400 mb-4 min-h-[16px] px-1"></div>

        <!-- DETAILED REPORT LINKS -->
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1 fade-up d2">Detailed reports
        </p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach ($reportLinks as $i => $r)
                <a href="{{ $r['url'] }}" data-report-link class="report-card fade-up d{{ $i + 2 }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background:{{ $r['bg'] }};">
                            <svg class="h-5 w-5" fill="none" stroke="{{ $r['color'] }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $r['icon'] }}" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-sm truncate">{{ $r['title'] }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $r['desc'] }}</p>
                        </div>
                        <span class="arrow text-lg">→</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div id="content">

            <!-- KPI ROW 1 -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="stat-card fade-up d2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Sales</p>
                    <p class="text-2xl font-extrabold text-gray-900" id="kpiSales">—</p>
                    <p class="text-xs text-gray-400 mt-2" id="kpiSalesHint"></p>
                </div>
                <div class="stat-card fade-up d3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg. Order Value</p>
                    <p class="text-2xl font-extrabold text-gray-900" id="kpiAov">—</p>
                    <p class="text-xs text-gray-400 mt-2">Sales ÷ orders</p>
                </div>
                <div class="stat-card fade-up d4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Orders</p>
                    <p class="text-2xl font-extrabold text-gray-900" id="kpiOrders">—</p>
                    <span class="badge mt-2" id="kpiOrdersBadge" style="background:#dcfce7;color:#15803d;">—</span>
                </div>
                <div class="stat-card fade-up d5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Completion Rate</p>
                    <p class="text-2xl font-extrabold text-gray-900" id="kpiRate">—</p>
                    <p class="text-xs text-gray-400 mt-2" id="kpiRateHint"></p>
                </div>
            </div>

            <!-- KPI ROW 2 -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="stat-card fade-up d3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Items Sold</p>
                    <p class="text-xl font-extrabold text-gray-900" id="kpiItems">—</p>
                    <p class="text-xs text-gray-400 mt-1" id="kpiItemsHint"></p>
                </div>
                <div class="stat-card fade-up d4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg Items / Order</p>
                    <p class="text-xl font-extrabold text-gray-900" id="kpiAvgItems">—</p>
                    <p class="text-xs text-gray-400 mt-1">Across all orders</p>
                </div>
                <div class="stat-card fade-up d5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Best Seller</p>
                    <p class="text-xl font-extrabold text-gray-900 truncate" id="kpiTop">—</p>
                    <p class="text-xs text-gray-400 mt-1" id="kpiTopHint"></p>
                </div>
                <div class="stat-card fade-up d6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Table Revenue</p>
                    <p class="text-xl font-extrabold text-gray-900" id="kpiTable">—</p>
                    <p class="text-xs text-gray-400 mt-1">Per-table total (may include tax/charges)</p>
                </div>
            </div>

            <!-- ROW 3: TREND + TIME OF DAY -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                <div class="chart-card lg:col-span-2 fade-up d4">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Revenue Trend</h3>
                        <p class="text-xs text-gray-400 mt-0.5" id="trendSub">Revenue over the selected period</p>
                    </div>
                    <div style="height:240px;"><canvas id="revenueChart"></canvas></div>
                </div>

                <div class="chart-card fade-up d5">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Peak Hours</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Orders by time of day</p>
                    </div>
                    <div style="height:170px;" class="mb-4"><canvas id="peakChart"></canvas></div>
                    <div class="space-y-2" id="peakLegend"></div>
                </div>
            </div>

            <!-- ROW 4: TOP ITEMS + TABLES -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                <div class="chart-card fade-up d5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Top Selling Items</h3>
                            <p class="text-xs text-gray-400 mt-0.5">For the selected period</p>
                        </div>
                        <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                            <button data-mode="revenue"
                                class="top-mode-btn px-3 py-1 rounded-md text-xs font-semibold text-white bg-gray-800 transition">Revenue</button>
                            <button data-mode="qty"
                                class="top-mode-btn px-3 py-1 rounded-md text-xs font-semibold text-gray-500 transition">Quantity</button>
                        </div>
                    </div>
                    <div class="space-y-3" id="topItems"></div>
                </div>

                <div class="chart-card fade-up d6">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Table Performance</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Revenue &amp; orders by table</p>
                    </div>
                    <div class="space-y-3" id="tablePerf"></div>
                </div>
            </div>

            <!-- ROW 5: STATUS + TRANSACTIONS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
                <div class="chart-card fade-up d7">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Order Status Split</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Orders in this period</p>
                    </div>
                    <div style="height:160px;" class="mb-4"><canvas id="statusChart"></canvas></div>
                    <div class="grid grid-cols-3 gap-2 text-center" id="statusSummary"></div>
                </div>

                <div class="chart-card lg:col-span-2 fade-up d8">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Recent Transactions</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Latest orders in this period</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Order ID</th>
                                    <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Items</th>
                                    <th
                                        class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Time</th>
                                </tr>
                            </thead>
                            <tbody id="txTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const $ = (id) => document.getElementById(id);
        const CURRENCY = 'Rs.';
        const API_URL = '/api/v1/owner/restaurant/reports/financial-reports';
        const PALETTE = ['#2563eb', '#0ea5e9', '#6366f1', '#06b6d4', '#a78bfa', '#f59e0b', '#10b981', '#ec4899'];
        const STATUS_COLORS = { completed: '#16a34a', pending: '#f59e0b', cancelled: '#ef4444' };

        Chart.defaults.font.family = 'Plus Jakarta Sans';
        Chart.defaults.color = '#9ca3af';

        let currentReport = null;
        let charts = {};
        let topMode = 'revenue';
        let topItemsData = [];

        /* ---------- helpers ---------- */
        const pad = (n) => String(n).padStart(2, '0');
        // Local (not UTC) date so "today" is correct for the user's timezone
        const fmtDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        const money = (n) => `${CURRENCY} ${Number(n || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const parseLocalDate = (str) => { const [y, m, d] = str.split('-').map(Number); return new Date(y, m - 1, d); };
        const kFmt = (v) => v >= 1000 ? `${CURRENCY} ${+(v / 1000).toFixed(1)}k` : `${CURRENCY} ${v}`;

        function presetRange(name) {
            const now = new Date();
            const d = (o) => { const x = new Date(now); x.setDate(x.getDate() + o); return x; };
            switch (name) {
                case 'today': return [fmtDate(now), fmtDate(now)];
                case 'yesterday': return [fmtDate(d(-1)), fmtDate(d(-1))];
                case 'last7': return [fmtDate(d(-6)), fmtDate(now)];
                case 'last30': return [fmtDate(d(-29)), fmtDate(now)];
                case 'month': return [fmtDate(new Date(now.getFullYear(), now.getMonth(), 1)), fmtDate(now)];
                case 'lastMonth': return [
                    fmtDate(new Date(now.getFullYear(), now.getMonth() - 1, 1)),
                    fmtDate(new Date(now.getFullYear(), now.getMonth(), 0))
                ];
                default: return null;
            }
        }

        function setMessage(text, isError = false) {
            const el = $('reportMessage');
            el.textContent = text;
            el.classList.toggle('text-red-500', isError);
            el.classList.toggle('text-gray-400', !isError);
        }

        function setLoading(on) {
            $('applyBtn').disabled = on;
            $('content').classList.toggle('is-loading', on);
        }

        // Keep selected date range when navigating to detailed report pages
        function syncReportLinks(from, to) {
            document.querySelectorAll('[data-report-link]').forEach((a) => {
                const url = new URL(a.getAttribute('href'), window.location.origin);
                url.searchParams.set('date_from', from);
                url.searchParams.set('date_to', to);
                a.href = url.toString();
            });
        }

        /* ---------- data loading ---------- */
        async function loadReport(from, to) {
            from = from || $('dateFrom').value || getTodayDate();
            to = to || $('dateTo').value || getTodayDate();

            if (from > to) {
                setMessage('"From" date cannot be after "To" date.', true);
                return;
            }

            $('dateFrom').value = from;
            $('dateTo').value = to;
            setLoading(true);
            setMessage('Loading financial report...');

            try {
                const token = localStorage.getItem('auth_token');
                const headers = { 'Accept': 'application/json' };
                if (token) headers['Authorization'] = `Bearer ${token}`;

                const query = new URLSearchParams({ date_from: from, date_to: to });
                const response = await fetch(`${API_URL}?${query}`, {
                    method: 'GET', headers, credentials: 'same-origin'
                });

                const json = await response.json();
                if (!response.ok || !json.data.success) {
                    throw new Error(json.message || 'Unable to load financial report.');
                }

                currentReport = json.data;

                renderReport(currentReport, from, to);
                syncReportLinks(from, to);
                setMessage(`Report generated for ${from === to ? from : from + ' to ' + to}.`);
            } catch (error) {
                console.error(error);
                setMessage(error.message, true);
            } finally {
                setLoading(false);
            }
        }

        /* ---------- rendering ---------- */
        function renderReport(data, from, to) {
            const orders = data.orders || [];
            const topItems = (data.topItems || []).map((t) => ({
                // API nests item details inside `name`; handle both shapes
                name: t.name?.name ?? t.name,
                qty: Number(t.totalQuantity ?? t.name?.total_quantity ?? 0),
                revenue: Number(t.revenue ?? t.name?.revenue ?? 0)
            }));
            const peak = data.peakHours || [];
            const tables = data.tablePerformance || [];

            const totalSales = orders.reduce((s, o) => s + Number(o.totalAmount || 0), 0);
            const totalOrders = data.totalOrders ?? orders.length;
            const completed = orders.filter((o) => o.status === 'completed').length;
            const itemsSold = topItems.reduce((s, i) => s + i.qty, 0);
            const itemsRevenue = topItems.reduce((s, i) => s + i.revenue, 0);
            const tableRevenue = tables.reduce((s, t) => s + Number(t.totalRevenue || 0), 0);
            const best = [...topItems].sort((a, b) => b.revenue - a.revenue)[0];

            $('kpiSales').textContent = money(totalSales);
            $('kpiSalesHint').textContent = `Items subtotal: ${money(itemsRevenue)}`;
            $('kpiAov').textContent = money(totalOrders ? totalSales / totalOrders : 0);
            $('kpiOrders').textContent = totalOrders;
            $('kpiOrdersBadge').textContent = `${completed} completed`;
            $('kpiRate').textContent = totalOrders ? `${((completed / totalOrders) * 100).toFixed(1)}%` : '—';
            $('kpiRateHint').textContent = `${totalOrders - completed} not completed`;
            $('kpiItems').textContent = itemsSold;
            $('kpiItemsHint').textContent = `${topItems.length} unique items`;
            $('kpiAvgItems').textContent = totalOrders ? (itemsSold / totalOrders).toFixed(1) : '—';
            $('kpiTop').textContent = best ? best.name : '—';
            $('kpiTopHint').textContent = best ? `${best.qty} sold · ${money(best.revenue)}` : '';
            $('kpiTable').textContent = money(tableRevenue);

            topItemsData = topItems;
            renderTopItems();
            renderTablePerf(tables);
            renderPeak(peak);
            renderStatus(orders);
            renderTransactions(orders);
            renderTrend(orders, from, to);
            $('exportBtn').disabled = !orders.length;
        }

        function emptyState(msg) {
            return `<p class="text-sm text-gray-400 text-center py-8">${msg}</p>`;
        }

        function renderTopItems() {
            const key = topMode === 'revenue' ? 'revenue' : 'qty';
            const sorted = [...topItemsData].sort((a, b) => b[key] - a[key]);
            if (!sorted.length) { $('topItems').innerHTML = emptyState('No item sales in this period.'); return; }
            const maxVal = sorted[0][key] || 1;
            $('topItems').innerHTML = sorted.map((item, i) => `
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-300 w-4">${i + 1}</span>
                                    <div class="flex-1">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-semibold text-gray-700">${esc(item.name)}</span>
                                            <span class="text-sm font-bold text-gray-900">${topMode === 'revenue' ? money(item.revenue) : item.qty + ' sold'}</span>
                                        </div>
                                        <div class="prog-bar"><div class="prog-fill" style="width:${Math.round(item[key] / maxVal * 100)}%;background:${PALETTE[i % PALETTE.length]};"></div></div>
                                    </div>
                                </div>`).join('');
        }

        function renderTablePerf(tables) {
            if (!tables.length) { $('tablePerf').innerHTML = emptyState('No table data.'); return; }
            const sorted = [...tables].sort((a, b) => b.totalRevenue - a.totalRevenue);
            const maxRev = sorted[0].totalRevenue || 1;
            $('tablePerf').innerHTML = sorted.map((t) => `
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-blue-600">${esc(t.tableNumber)}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-semibold text-gray-700">${money(t.totalRevenue)}</span>
                                            <span class="text-xs text-gray-400">${t.totalOrders} order${t.totalOrders !== 1 ? 's' : ''}</span>
                                        </div>
                                        <div class="prog-bar"><div class="prog-fill" style="width:${Math.round(t.totalRevenue / maxRev * 100)}%;background:#2563eb;"></div></div>
                                    </div>
                                </div>`).join('');
        }

        function renderPeak(peak) {
            const colors = ['#0ea5e9', '#2563eb', '#6366f1', '#a78bfa'];
            const total = peak.reduce((s, p) => s + p.totalOrders, 0);
            drawChart('peakChart', 'doughnut', {
                labels: peak.map((p) => p.timeline),
                datasets: [{ data: peak.map((p) => p.totalOrders), backgroundColor: colors, borderWidth: 0, hoverOffset: 6 }]
            }, { cutout: '68%', plugins: { legend: { display: false } } });

            $('peakLegend').innerHTML = peak.map((p, i) => `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${colors[i % colors.length]};"></div>
                                        <span class="text-xs text-gray-600">${esc(p.timeline)}</span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-800">${p.totalOrders}
                                        <span class="text-gray-400 font-normal">(${total ? Math.round(p.totalOrders / total * 100) : 0}%)</span></span>
                                </div>`).join('');
        }

        function renderStatus(orders) {
            const counts = {};
            orders.forEach((o) => { counts[o.status] = (counts[o.status] || 0) + 1; });
            const keys = Object.keys(counts);
            const total = orders.length;
            const colorOf = (k) => STATUS_COLORS[k] || '#94a3b8';

            drawChart('statusChart', 'doughnut', {
                labels: keys.map((k) => k.charAt(0).toUpperCase() + k.slice(1)),
                datasets: [{ data: keys.map((k) => counts[k]), backgroundColor: keys.map(colorOf), borderWidth: 0, hoverOffset: 5 }]
            }, { cutout: '70%', plugins: { legend: { display: false } } });

            $('statusSummary').innerHTML = keys.length ? keys.map((k) => `
                                <div>
                                    <p class="text-lg font-extrabold" style="color:${colorOf(k)};">${counts[k]}</p>
                                    <p class="text-xs text-gray-400 capitalize">${esc(k)}</p>
                                    <p class="text-xs font-semibold text-gray-600">${Math.round(counts[k] / total * 100)}%</p>
                                </div>`).join('') : '';
        }

        function renderTransactions(orders) {
            if (!orders.length) {
                $('txTable').innerHTML = `<tr><td colspan="4">${emptyState('No orders in this period.')}</td></tr>`;
                return;
            }
            const sorted = [...orders].sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt)).slice(0, 12);
            $('txTable').innerHTML = sorted.map((o) => {
                const ok = o.status === 'completed';
                const dt = new Date(o.createdAt);
                const items = (o.orderItems || []).map((i) => `${esc(i.menuItem)} (${i.quantity})`).join(', ');
                return `
                                <tr class="tx-row border-b border-gray-50">
                                    <td class="py-3 pr-3 font-semibold text-blue-600 text-sm">${esc(o.id)}</td>
                                    <td class="py-3 pr-3 text-gray-500 text-sm max-w-[220px] truncate" title="${items}">${items}</td>
                                    <td class="py-3 pr-3 text-right font-bold text-gray-800 text-sm whitespace-nowrap">${money(o.totalAmount)}</td>
                                    <td class="py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="text-gray-400 text-sm whitespace-nowrap">${dt.toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                                            <span class="badge" style="background:${ok ? '#dcfce7' : '#fee2e2'};color:${ok ? '#15803d' : '#dc2626'};">${esc(o.status)}</span>
                                        </div>
                                    </td>
                                </tr>`;
            }).join('');
        }

        // One day → revenue by hour. Multiple days → revenue by day (gaps filled with 0).
        function renderTrend(orders, from, to) {
            let labels = [], values = [];
            if (from === to) {
                $('trendSub').textContent = 'Revenue by hour of day';
                const byHour = Array(24).fill(0);
                orders.forEach((o) => { byHour[new Date(o.createdAt).getHours()] += Number(o.totalAmount || 0); });
                const hours = orders.map((o) => new Date(o.createdAt).getHours());
                const lo = hours.length ? Math.max(0, Math.min(...hours) - 1) : 8;
                const hi = hours.length ? Math.min(23, Math.max(...hours) + 1) : 20;
                for (let h = lo; h <= hi; h++) {
                    labels.push(new Date(2000, 0, 1, h).toLocaleTimeString([], { hour: 'numeric' }));
                    values.push(byHour[h]);
                }
            } else {
                $('trendSub').textContent = 'Revenue by day';
                const byDay = {};
                orders.forEach((o) => {
                    const k = fmtDate(new Date(o.createdAt));
                    byDay[k] = (byDay[k] || 0) + Number(o.totalAmount || 0);
                });
                const end = parseLocalDate(to);
                for (let d = parseLocalDate(from); d <= end; d.setDate(d.getDate() + 1)) {
                    labels.push(d.toLocaleDateString([], { month: 'short', day: 'numeric' }));
                    values.push(byDay[fmtDate(d)] || 0);
                }
            }

            drawChart('revenueChart', 'bar', {
                labels,
                datasets: [{ label: 'Revenue', data: values, backgroundColor: '#2563eb', borderRadius: 6, borderSkipped: false }]
            }, {
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (c) => ` ${money(c.raw)}` } }
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true, ticks: { callback: (v) => kFmt(v) } }
                }
            });
        }

        function drawChart(id, type, data, options = {}) {
            if (charts[id]) charts[id].destroy();
            charts[id] = new Chart($(id).getContext('2d'), {
                type, data,
                options: { responsive: true, maintainAspectRatio: false, ...options }
            });
        }

        /* ---------- CSV export ---------- */
        function exportCsv() {
            if (!currentReport?.orders?.length) return;
            const rows = [['Order', 'Created At', 'Status', 'Items', 'Total']];
            currentReport.orders.forEach((o) => rows.push([
                o.id, o.createdAt, o.status,
                (o.orderItems || []).map((i) => `${i.menuItem} x${i.quantity}`).join('; '),
                o.totalAmount
            ]));
            const csv = rows.map((r) => r.map((c) => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = `orders_${$('dateFrom').value}_${$('dateTo').value}.csv`;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        /* ---------- wiring ---------- */
        function setActivePreset(name) {
            document.querySelectorAll('#presets .period-btn').forEach((b) =>
                b.classList.toggle('active', b.dataset.preset === name));
        }

        document.querySelectorAll('#presets .period-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const name = btn.dataset.preset;
                setActivePreset(name);

                // Show Apply button only for Custom preset
                document.getElementById('applyBtn').classList.toggle(
                    'hidden',
                    name !== 'custom'
                );

                if (name === 'custom') {
                    $('dateFrom').focus();
                    return;
                }

                const [from, to] = presetRange(name);
                loadReport(from, to);
            });
        });

        ['dateFrom', 'dateTo'].forEach((id) =>
            $(id).addEventListener('change', () => {
                setActivePreset('custom');
                $('applyBtn').classList.remove('hidden');
            })
        );


        $('applyBtn').addEventListener('click', () => {
            setActivePreset('custom');
            loadReport($('dateFrom').value, $('dateTo').value);
        });

        $('exportBtn').addEventListener('click', exportCsv);

        document.querySelectorAll('.top-mode-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                topMode = btn.dataset.mode;
                document.querySelectorAll('.top-mode-btn').forEach((b) => {
                    const on = b === btn;
                    b.classList.toggle('bg-gray-800', on);
                    b.classList.toggle('text-white', on);
                    b.classList.toggle('text-gray-500', !on);
                });
                renderTopItems();
            });
        });

        function init() {

            // Set the restaurant name
            document.getElementById('restroName').textContent = localStorage.getItem('user_name');

            // Honor ?date_from & ?date_to so links from other report pages keep the range
            const params = new URLSearchParams(window.location.search);
            const today = getTodayDate();
            const from = params.get('date_from') || today;
            const to = params.get('date_to') || today;
            $('dateFrom').max = $('dateTo').max = today;

            const match = ['today', 'yesterday', 'last7', 'last30', 'month', 'lastMonth'].find((p) => {
                const r = presetRange(p);
                return r[0] === from && r[1] === to;
            });
            setActivePreset(match || 'custom');
            loadReport(from, to);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    </script>




@endsection