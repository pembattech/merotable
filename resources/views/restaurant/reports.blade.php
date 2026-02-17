@extends('layouts.app')

@section('title', 'Report | ' . config('app.name'))

@section('content')

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Financial Insights – MeroTable</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

            * {
                font-family: 'Plus Jakarta Sans', sans-serif;
                box-sizing: border-box;
            }

            :root {
                --blue: #2563eb;
                --blue-light: #eff6ff;
                --green: #16a34a;
                --red: #dc2626;
                --orange: #ea580c;
                --purple: #7c3aed;
            }

            body {
                background: #f0f4f8;
            }

            /* scrollbar */
            ::-webkit-scrollbar {
                width: 5px;
                height: 5px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            /* period tab */
            .period-btn {
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

            .period-btn.active {
                background: #1e293b;
                color: #fff;
            }

            /* stat card */
            .stat-card {
                background: #fff;
                border-radius: 16px;
                padding: 20px;
                border: 1px solid #e9eef5;
            }

            /* chart card */
            .chart-card {
                background: #fff;
                border-radius: 16px;
                padding: 24px;
                border: 1px solid #e9eef5;
            }

            /* heatmap cell */
            .hm-cell {
                border-radius: 4px;
                transition: transform 0.1s;
                cursor: default;
            }

            .hm-cell:hover {
                transform: scale(1.15);
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

            /* table row hover */
            .tx-row {
                transition: background 0.12s;
            }

            .tx-row:hover {
                background: #f8fafc;
            }

            /* badge */
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 2px 10px;
                border-radius: 999px;
                font-size: 0.72rem;
                font-weight: 700;
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

         

            .d1 {
                animation-delay: 0.05s;
            }

            .d2 {
                animation-delay: 0.1s;
            }

            .d3 {
                animation-delay: 0.15s;
            }

            .d4 {
                animation-delay: 0.2s;
            }

            .d5 {
                animation-delay: 0.25s;
            }

            .d6 {
                animation-delay: 0.3s;
            }

            .d7 {
                animation-delay: 0.35s;
            }

            .d8 {
                animation-delay: 0.4s;
            }

            .d9 {
                animation-delay: 0.45s;
            }

            .d10 {
                animation-delay: 0.5s;
            }
        </style>
    </head>

    <body class="min-h-screen p-5 md:p-8">

        <!-- ══════════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════════ -->
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7 fade-up d1">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Financial Insights</h1>
                    <p class="text-sm text-gray-400 mt-1">Momo House · Last updated just now</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Period toggle -->
                    <div class="bg-white border border-gray-200 rounded-xl p-1 flex gap-1">
                        <button class="period-btn active" onclick="setPeriod('weekly',this)">Weekly</button>
                        <button class="period-btn" onclick="setPeriod('monthly',this)">Monthly</button>
                        <button class="period-btn" onclick="setPeriod('yearly',this)">Yearly</button>
                    </div>
                    <!-- Export -->
                    <button onclick="exportReport()"
                        class="flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 1 — KPI STAT CARDS
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <!-- Net Sales -->
                <div class="stat-card fade-up d2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Net Sales</p>
                    <p class="text-2xl font-extrabold text-gray-900">Rs. 84,200</p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="badge" style="background:#dcfce7;color:#15803d;">↑ 8.2%</span>
                        <span class="text-xs text-gray-400">vs last week</span>
                    </div>
                </div>

                <!-- Avg Order Value -->
                <div class="stat-card fade-up d3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg. Order Value</p>
                    <p class="text-2xl font-extrabold text-gray-900">Rs. 620</p>
                    <p class="text-xs text-gray-400 mt-2">Stable per table</p>
                </div>

                <!-- Total Orders -->
                <div class="stat-card fade-up d4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Orders</p>
                    <p class="text-2xl font-extrabold text-gray-900">1,402</p>
                    <span class="badge mt-2" style="background:#dcfce7;color:#15803d;">Completed</span>
                </div>

                <!-- Cancellation Rate -->
                <div class="stat-card fade-up d5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Cancellation Rate</p>
                    <p class="text-2xl font-extrabold text-gray-900">3.4%</p>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="badge" style="background:#fee2e2;color:#dc2626;">↑ 0.6%</span>
                        <span class="text-xs text-gray-400">vs last week</span>
                    </div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 2 — Additional KPIs
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <div class="stat-card fade-up d3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gross Sales</p>
                    <p class="text-xl font-extrabold text-gray-900">Rs. 91,800</p>
                    <p class="text-xs text-gray-400 mt-1">Before discounts</p>
                </div>

                <div class="stat-card fade-up d4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Discounts Given</p>
                    <p class="text-xl font-extrabold text-orange-500">− Rs. 4,800</p>
                    <p class="text-xs text-gray-400 mt-1">Across 38 orders</p>
                </div>

                <div class="stat-card fade-up d5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Tax Collected</p>
                    <p class="text-xl font-extrabold text-gray-900">Rs. 2,800</p>
                    <p class="text-xs text-gray-400 mt-1">13% VAT</p>
                </div>

                <div class="stat-card fade-up d6">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg Items / Order</p>
                    <p class="text-xl font-extrabold text-gray-900">3.2</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="badge" style="background:#dcfce7;color:#15803d;">↑ 0.4</span>
                        <span class="text-xs text-gray-400">vs last week</span>
                    </div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 3 — REVENUE CHART + CATEGORY DONUT
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

                <!-- Revenue Trend Bar Chart (2/3) -->
                <div class="chart-card lg:col-span-2 fade-up d4">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Revenue Trend</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Daily revenue this week</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm inline-block"
                                    style="background:#2563eb;"></span>This week</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm inline-block"
                                    style="background:#bfdbfe;"></span>Last week</span>
                        </div>
                    </div>
                    <div style="height:240px;"><canvas id="revenueChart"></canvas></div>
                </div>

                <!-- Category Donut (1/3) -->
                <div class="chart-card fade-up d5">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Category Breakdown</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Revenue by category</p>
                    </div>
                    <div style="height:170px;" class="mb-4"><canvas id="categoryChart"></canvas></div>
                    <div class="space-y-2" id="categoryLegend"></div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 4 — TOP ITEMS + DAY OF WEEK
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

                <!-- Top Selling Items -->
                <div class="chart-card fade-up d5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Top Selling Items</h3>
                            <p class="text-xs text-gray-400 mt-0.5">By revenue this week</p>
                        </div>
                        <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                            <button onclick="setTopMode('revenue',this)"
                                class="top-mode-btn active-top px-3 py-1 rounded-md text-xs font-semibold text-white bg-gray-800 transition">Revenue</button>
                            <button onclick="setTopMode('qty',this)"
                                class="top-mode-btn px-3 py-1 rounded-md text-xs font-semibold text-gray-500 transition">Quantity</button>
                        </div>
                    </div>
                    <div class="space-y-3" id="topItems"></div>
                </div>

                <!-- Day of Week -->
                <div class="chart-card fade-up d6">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Day of Week Pattern</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Avg revenue per day</p>
                    </div>
                    <div style="height:220px;"><canvas id="dowChart"></canvas></div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 5 — HOURLY HEATMAP + PEAK HOURS
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

                <!-- Heatmap (2/3) -->
                <div class="chart-card lg:col-span-2 fade-up d6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Hourly Sales Heatmap</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Orders per hour by day</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span>Low</span>
                            <div class="flex gap-0.5">
                                <div class="w-4 h-4 rounded" style="background:#eff6ff;"></div>
                                <div class="w-4 h-4 rounded" style="background:#bfdbfe;"></div>
                                <div class="w-4 h-4 rounded" style="background:#60a5fa;"></div>
                                <div class="w-4 h-4 rounded" style="background:#2563eb;"></div>
                                <div class="w-4 h-4 rounded" style="background:#1e40af;"></div>
                            </div>
                            <span>High</span>
                        </div>
                    </div>
                    <div id="heatmap" class="overflow-x-auto"></div>
                </div>

                <!-- Peak Hours Summary (1/3) -->
                <div class="chart-card fade-up d7">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Peak Hours</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Busiest time slots</p>
                    </div>
                    <div class="space-y-4" id="peakHours"></div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 6 — TABLE PERFORMANCE + STAFF INSIGHTS
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

                <!-- Table Performance -->
                <div class="chart-card fade-up d7">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Table Performance</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Revenue & turnover by table</p>
                    </div>
                    <div class="space-y-3" id="tablePerf"></div>
                </div>

                <!-- Staff Insights -->
                <div class="chart-card fade-up d8">
                    <div class="mb-5">
                        <h3 class="font-bold text-gray-800 text-base">Staff Performance</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Orders handled this week</p>
                    </div>
                    <div class="space-y-3" id="staffPerf"></div>
                </div>

            </div>

            <!-- ══════════════════════════════════════════════════════
           ROW 7 — CANCELLATION BREAKDOWN
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

                <div class="chart-card fade-up d8">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Cancellation Reasons</h3>
                        <p class="text-xs text-gray-400 mt-0.5">This week · 48 cancelled</p>
                    </div>
                    <div style="height:160px;" class="mb-4"><canvas id="cancelChart"></canvas></div>
                    <div class="space-y-2" id="cancelLegend"></div>
                </div>

                <div class="chart-card lg:col-span-2 fade-up d9">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Recent Transactions</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Latest orders today</p>
                        </div>
                        <button class="text-blue-600 text-sm font-semibold hover:text-blue-700 transition">View Full
                            History →</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th
                                        class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Order ID</th>
                                    <th
                                        class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Table</th>
                                    <th
                                        class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
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

            <!-- ══════════════════════════════════════════════════════
           ROW 8 — PENDING vs COMPLETED + RUNNING TOTAL
      ══════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

                <div class="chart-card fade-up d8">
                    <div class="mb-4">
                        <h3 class="font-bold text-gray-800 text-base">Order Status Split</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Today's orders</p>
                    </div>
                    <div style="height:160px;" class="mb-4"><canvas id="statusChart"></canvas></div>
                    <div class="grid grid-cols-3 gap-2 text-center" id="statusSummary"></div>
                </div>

                <div class="chart-card lg:col-span-2 fade-up d9">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="font-bold text-gray-800 text-base">Monthly Running Total</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Cumulative revenue — are you on track?</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Month target</p>
                            <p class="font-bold text-gray-800">Rs. 3,50,000</p>
                        </div>
                    </div>
                    <div style="height:200px;"><canvas id="runningChart"></canvas></div>
                </div>

            </div>

        </div><!-- /max-w -->

        <!-- TOAST -->
        <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl bg-gray-900 text-white text-sm font-medium">
                <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span id="toastMsg">Exported!</span>
            </div>
        </div>

        <script>
            // ══════════════════════════════════════════════
            // DATA
            // ══════════════════════════════════════════════
            const DATA = {
                weekly: {
                    revenue: [8200, 11400, 9800, 13600, 15200, 14800, 11200],
                    revenuePrev: [7100, 10200, 8900, 12100, 13800, 13200, 10100],
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                },
                monthly: {
                    revenue: [62000, 71000, 58000, 84200, 91000, 88000, 79000, 95000, 101000, 88000, 76000, 84200],
                    revenuePrev: [55000, 63000, 52000, 76000, 82000, 79000, 71000, 86000, 93000, 80000, 69000, 76000],
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                },
                yearly: {
                    revenue: [820000, 910000, 760000, 1040000, 980000],
                    revenuePrev: [710000, 840000, 690000, 920000, 870000],
                    labels: ['2020', '2021', '2022', '2023', '2024'],
                }
            };

            const CATEGORIES = [{
                    name: 'Main Course',
                    pct: 38,
                    color: '#2563eb',
                    revenue: 31996
                },
                {
                    name: 'Appetizers',
                    pct: 22,
                    color: '#0ea5e9',
                    revenue: 18524
                },
                {
                    name: 'Beverages',
                    pct: 18,
                    color: '#06b6d4',
                    revenue: 15156
                },
                {
                    name: 'Specials',
                    pct: 14,
                    color: '#6366f1',
                    revenue: 11788
                },
                {
                    name: 'Desserts',
                    pct: 8,
                    color: '#a78bfa',
                    revenue: 6736
                },
            ];

            const TOP_ITEMS = [{
                    name: 'Chicken Biryani',
                    revenue: 18400,
                    qty: 368,
                    color: '#2563eb'
                },
                {
                    name: 'Buff Momo',
                    revenue: 14200,
                    qty: 710,
                    color: '#0ea5e9'
                },
                {
                    name: 'Butter Chicken',
                    revenue: 12600,
                    qty: 252,
                    color: '#6366f1'
                },
                {
                    name: 'Masala Tea',
                    revenue: 8200,
                    qty: 1640,
                    color: '#06b6d4'
                },
                {
                    name: 'Dal Bhat Set',
                    revenue: 7400,
                    qty: 211,
                    color: '#a78bfa'
                },
            ];

            const DOW = {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                values: [8200, 11400, 9800, 13600, 15200, 14800, 11200],
            };

            const HEATMAP_HOURS = ['9AM', '10AM', '11AM', '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM',
            '9PM'];
            const HEATMAP_DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const HEATMAP_DATA = [
                [2, 3, 8, 18, 32, 28, 12, 9, 14, 22, 31, 28, 16],
                [1, 2, 6, 15, 28, 24, 10, 7, 12, 19, 28, 25, 14],
                [3, 4, 9, 20, 35, 30, 14, 10, 15, 24, 33, 30, 18],
                [2, 3, 7, 17, 30, 27, 11, 8, 13, 21, 30, 27, 15],
                [4, 5, 12, 24, 38, 34, 16, 12, 18, 28, 38, 35, 22],
                [5, 7, 15, 28, 42, 40, 20, 15, 22, 32, 42, 40, 26],
                [3, 4, 10, 20, 35, 32, 14, 10, 16, 24, 35, 32, 18],
            ];

            const PEAK_HOURS = [{
                    time: '1:00 PM – 2:30 PM',
                    label: 'Lunch rush',
                    orders: 42,
                    pct: 100,
                    color: '#2563eb'
                },
                {
                    time: '7:00 PM – 9:00 PM',
                    label: 'Dinner peak',
                    orders: 38,
                    pct: 90,
                    color: '#6366f1'
                },
                {
                    time: '12:00 – 1:00 PM',
                    label: 'Pre-lunch',
                    orders: 24,
                    pct: 57,
                    color: '#0ea5e9'
                },
                {
                    time: '6:00 – 7:00 PM',
                    label: 'Early dinner',
                    orders: 19,
                    pct: 45,
                    color: '#a78bfa'
                },
            ];

            const TABLES = [{
                    id: 'T-01',
                    revenue: 9400,
                    turnover: 14,
                    avgTime: '42 min'
                },
                {
                    id: 'T-03',
                    revenue: 8800,
                    turnover: 13,
                    avgTime: '38 min'
                },
                {
                    id: 'T-08',
                    revenue: 8200,
                    turnover: 12,
                    avgTime: '44 min'
                },
                {
                    id: 'T-12',
                    revenue: 7600,
                    turnover: 11,
                    avgTime: '36 min'
                },
                {
                    id: 'T-07',
                    revenue: 7200,
                    turnover: 11,
                    avgTime: '39 min'
                },
                {
                    id: 'T-05',
                    revenue: 6400,
                    turnover: 9,
                    avgTime: '51 min'
                },
                {
                    id: 'T-02',
                    revenue: 5800,
                    turnover: 8,
                    avgTime: '47 min'
                },
                {
                    id: 'T-11',
                    revenue: 5200,
                    turnover: 8,
                    avgTime: '43 min'
                },
            ];

            const STAFF = [{
                    name: 'Ram Shrestha',
                    role: 'Waiter',
                    orders: 186,
                    cancels: 4,
                    revenue: 48200,
                    color: '#2563eb'
                },
                {
                    name: 'Sita Tamang',
                    role: 'Waiter',
                    orders: 164,
                    cancels: 6,
                    revenue: 41800,
                    color: '#6366f1'
                },
                {
                    name: 'Hari Poudel',
                    role: 'Captain',
                    orders: 142,
                    cancels: 2,
                    revenue: 38400,
                    color: '#0ea5e9'
                },
                {
                    name: 'Gita Rai',
                    role: 'Waiter',
                    orders: 128,
                    cancels: 8,
                    revenue: 32600,
                    color: '#a78bfa'
                },
                {
                    name: 'Bikash KC',
                    role: 'Waiter',
                    orders: 118,
                    cancels: 3,
                    revenue: 29800,
                    color: '#06b6d4'
                },
                {
                    name: 'Priya Sharma',
                    role: 'Waiter',
                    orders: 104,
                    cancels: 5,
                    revenue: 26400,
                    color: '#f59e0b'
                },
            ];

            const CANCEL_REASONS = [{
                    reason: 'Changed mind',
                    count: 18,
                    color: '#ef4444'
                },
                {
                    reason: 'Long wait time',
                    count: 13,
                    color: '#f97316'
                },
                {
                    reason: 'Wrong item',
                    count: 9,
                    color: '#eab308'
                },
                {
                    reason: 'Item unavailable',
                    count: 8,
                    color: '#a78bfa'
                },
            ];

            const TRANSACTIONS = [{
                    id: '#MT-8942',
                    table: 'T-03',
                    items: 'Buff Momo (2), Lemon Soda (1)',
                    amount: 380,
                    time: '14:20',
                    status: 'completed'
                },
                {
                    id: '#MT-8941',
                    table: 'T-08',
                    items: 'Chicken Thakali (4), Lassi (4)',
                    amount: 2400,
                    time: '14:15',
                    status: 'completed'
                },
                {
                    id: '#MT-8940',
                    table: 'T-12',
                    items: 'Masala Tea (6), Veg Pakoda (2)',
                    amount: 560,
                    time: '13:55',
                    status: 'completed'
                },
                {
                    id: '#MT-8939',
                    table: 'T-01',
                    items: 'Butter Chicken (2), Garlic Naan (4)',
                    amount: 1200,
                    time: '13:48',
                    status: 'completed'
                },
                {
                    id: '#MT-8938',
                    table: 'T-05',
                    items: 'Chicken Biryani (1)',
                    amount: 450,
                    time: '13:32',
                    status: 'cancelled'
                },
                {
                    id: '#MT-8937',
                    table: 'T-07',
                    items: 'Chef Special Thali (2), Mango Lassi (2)',
                    amount: 1600,
                    time: '13:10',
                    status: 'completed'
                },
                {
                    id: '#MT-8936',
                    table: 'T-02',
                    items: 'Veg Momo (3), Sprite (2)',
                    amount: 700,
                    time: '12:58',
                    status: 'completed'
                },
                {
                    id: '#MT-8935',
                    table: 'T-11',
                    items: 'Tandoori Platter (1), Butter Naan (3)',
                    amount: 1050,
                    time: '12:44',
                    status: 'completed'
                },
                {
                    id: '#MT-8934',
                    table: 'T-06',
                    items: 'Dal Bhat Set (2), Masala Tea (2)',
                    amount: 800,
                    time: '12:30',
                    status: 'completed'
                },
                {
                    id: '#MT-8933',
                    table: 'T-09',
                    items: 'Grilled Fish (1), Fried Rice (2), Cola (3)',
                    amount: 1340,
                    time: '12:18',
                    status: 'cancelled'
                },
                {
                    id: '#MT-8932',
                    table: 'T-04',
                    items: 'Chowmein Chicken (2), Spring Rolls (2)',
                    amount: 900,
                    time: '12:05',
                    status: 'completed'
                },
                {
                    id: '#MT-8931',
                    table: 'T-10',
                    items: 'Pizza Margherita (1), Garlic Bread (1)',
                    amount: 720,
                    time: '11:52',
                    status: 'completed'
                },
            ];

            const STATUS_DATA = {
                completed: 184,
                pending: 12,
                cancelled: 8
            };

            // Running total (cumulative through month)
            const RUNNING = {
                labels: Array.from({
                    length: 15
                }, (_, i) => `Feb ${i+1}`),
                actual: [8200, 19400, 29100, 41800, 54600, 68200, 80100, 95400, 110200, 124800, 142100, 158800, 172400,
                    189200, 207600
                ],
                target: [11666, 23333, 35000, 46666, 58333, 70000, 81666, 93333, 105000, 116666, 128333, 140000, 151666,
                    163333, 175000
                ],
            };

            // ══════════════════════════════════════════════
            // CHART DEFAULTS
            // ══════════════════════════════════════════════
            Chart.defaults.font.family = 'Plus Jakarta Sans';
            Chart.defaults.color = '#9ca3af';

            let revenueChart, categoryChart, dowChart, cancelChart, statusChart, runningChart;
            let currentPeriod = 'weekly';
            let topMode = 'revenue';

            // ══════════════════════════════════════════════
            // REVENUE BAR CHART
            // ══════════════════════════════════════════════
            function initRevenueChart(period) {
                const d = DATA[period];
                const ctx = document.getElementById('revenueChart').getContext('2d');
                if (revenueChart) revenueChart.destroy();
                revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: d.labels,
                        datasets: [{
                                label: 'This period',
                                data: d.revenue,
                                backgroundColor: '#2563eb',
                                borderRadius: 6,
                                borderSkipped: false
                            },
                            {
                                label: 'Last period',
                                data: d.revenuePrev,
                                backgroundColor: '#bfdbfe',
                                borderRadius: 6,
                                borderSkipped: false
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` Rs. ${ctx.raw.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f1f5f9'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    callback: v => 'Rs. ' + (v / 1000) + 'k'
                                }
                            }
                        }
                    }
                });
            }

            // ══════════════════════════════════════════════
            // CATEGORY DONUT
            // ══════════════════════════════════════════════
            function initCategoryChart() {
                const ctx = document.getElementById('categoryChart').getContext('2d');
                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: CATEGORIES.map(c => c.name),
                        datasets: [{
                            data: CATEGORIES.map(c => c.pct),
                            backgroundColor: CATEGORIES.map(c => c.color),
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw}%`
                                }
                            }
                        }
                    }
                });

                const leg = document.getElementById('categoryLegend');
                leg.innerHTML = CATEGORIES.map(c => `
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${c.color};"></div>
        <span class="text-xs text-gray-600">${c.name}</span>
      </div>
      <span class="text-xs font-bold text-gray-800">${c.pct}%</span>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // TOP ITEMS
            // ══════════════════════════════════════════════
            function renderTopItems() {
                const sorted = [...TOP_ITEMS].sort((a, b) =>
                    topMode === 'revenue' ? b.revenue - a.revenue : b.qty - a.qty
                );
                const maxVal = sorted[0][topMode === 'revenue' ? 'revenue' : 'qty'];
                document.getElementById('topItems').innerHTML = sorted.map((item, i) => `
    <div class="flex items-center gap-3">
      <span class="text-xs font-bold text-gray-300 w-4">${i+1}</span>
      <div class="flex-1">
        <div class="flex justify-between mb-1">
          <span class="text-sm font-semibold text-gray-700">${item.name}</span>
          <span class="text-sm font-bold text-gray-900">
            ${topMode==='revenue' ? 'Rs. '+item.revenue.toLocaleString() : item.qty+' sold'}
          </span>
        </div>
        <div class="prog-bar">
          <div class="prog-fill" style="width:${Math.round(item[topMode==='revenue'?'revenue':'qty']/maxVal*100)}%;background:${item.color};"></div>
        </div>
      </div>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // DAY OF WEEK
            // ══════════════════════════════════════════════
            function initDowChart() {
                const ctx = document.getElementById('dowChart').getContext('2d');
                const maxV = Math.max(...DOW.values);
                dowChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: DOW.labels,
                        datasets: [{
                            data: DOW.values,
                            backgroundColor: DOW.values.map(v => {
                                const ratio = v / maxV;
                                if (ratio > 0.9) return '#1e40af';
                                if (ratio > 0.7) return '#2563eb';
                                if (ratio > 0.5) return '#60a5fa';
                                return '#bfdbfe';
                            }),
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ' Rs. ' + ctx.raw.toLocaleString()
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f1f5f9'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    callback: v => 'Rs. ' + (v / 1000) + 'k'
                                }
                            }
                        }
                    }
                });
            }

            // ══════════════════════════════════════════════
            // HEATMAP
            // ══════════════════════════════════════════════
            function renderHeatmap() {
                const maxVal = Math.max(...HEATMAP_DATA.flat());
                const colors = ['#eff6ff', '#bfdbfe', '#60a5fa', '#2563eb', '#1e40af'];

                function getColor(v) {
                    const ratio = v / maxVal;
                    if (ratio < 0.2) return colors[0];
                    if (ratio < 0.4) return colors[1];
                    if (ratio < 0.6) return colors[2];
                    if (ratio < 0.8) return colors[3];
                    return colors[4];
                }

                let html = '<div style="display:grid;grid-template-columns:40px repeat(' + HEATMAP_HOURS.length +
                    ',1fr);gap:3px;min-width:480px;">';
                // header row
                html += '<div></div>';
                HEATMAP_HOURS.forEach(h => {
                    html += `<div class="text-center text-xs text-gray-400 pb-1 font-medium">${h}</div>`;
                });
                // data rows
                HEATMAP_DATA.forEach((row, di) => {
                    html +=
                        `<div class="text-xs text-gray-500 font-semibold flex items-center pr-1">${HEATMAP_DAYS[di]}</div>`;
                    row.forEach(v => {
                        const c = getColor(v);
                        const textC = v / maxVal > 0.5 ? '#fff' : '#374151';
                        html += `<div class="hm-cell flex items-center justify-center text-xs font-bold" title="${v} orders"
        style="height:32px;background:${c};color:${textC};">${v}</div>`;
                    });
                });
                html += '</div>';
                document.getElementById('heatmap').innerHTML = html;
            }

            // ══════════════════════════════════════════════
            // PEAK HOURS
            // ══════════════════════════════════════════════
            function renderPeakHours() {
                document.getElementById('peakHours').innerHTML = PEAK_HOURS.map(p => `
    <div>
      <div class="flex justify-between mb-1">
        <div>
          <p class="text-sm font-semibold text-gray-800">${p.time}</p>
          <p class="text-xs text-gray-400">${p.label}</p>
        </div>
        <span class="text-sm font-bold" style="color:${p.color}">${p.orders} orders</span>
      </div>
      <div class="prog-bar">
        <div class="prog-fill" style="width:${p.pct}%;background:${p.color};"></div>
      </div>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // TABLE PERFORMANCE
            // ══════════════════════════════════════════════
            function renderTablePerf() {
                const maxRev = Math.max(...TABLES.map(t => t.revenue));
                document.getElementById('tablePerf').innerHTML = TABLES.map(t => `
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
        <span class="text-xs font-bold text-blue-600">${t.id}</span>
      </div>
      <div class="flex-1">
        <div class="flex justify-between mb-1">
          <span class="text-sm font-semibold text-gray-700">Rs. ${t.revenue.toLocaleString()}</span>
          <span class="text-xs text-gray-400">${t.turnover}x · avg ${t.avgTime}</span>
        </div>
        <div class="prog-bar">
          <div class="prog-fill" style="width:${Math.round(t.revenue/maxRev*100)}%;background:#2563eb;"></div>
        </div>
      </div>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // STAFF PERFORMANCE
            // ══════════════════════════════════════════════
            function renderStaffPerf() {
                const maxOrders = Math.max(...STAFF.map(s => s.orders));
                document.getElementById('staffPerf').innerHTML = STAFF.map(s => `
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
        style="background:${s.color};">
        ${s.name.split(' ').map(w=>w[0]).join('')}
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex justify-between mb-1">
          <div class="min-w-0">
            <span class="text-sm font-semibold text-gray-800">${s.name}</span>
            <span class="text-xs text-gray-400 ml-1.5 bg-gray-100 px-1.5 py-0.5 rounded-md">${s.role}</span>
          </div>
          <div class="text-right flex-shrink-0 ml-2">
            <span class="text-sm font-bold text-gray-800">${s.orders} orders</span>
          </div>
        </div>
        <div class="flex justify-between items-center mb-1.5">
          <span class="text-xs text-gray-400">Rs. ${s.revenue.toLocaleString()} revenue</span>
          <span class="text-xs font-medium" style="color:#ef4444;">${s.cancels} cancel${s.cancels!==1?'s':''}</span>
        </div>
        <div class="prog-bar">
          <div class="prog-fill" style="width:${Math.round(s.orders/maxOrders*100)}%;background:${s.color};"></div>
        </div>
      </div>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // CANCELLATION CHART
            // ══════════════════════════════════════════════
            function initCancelChart() {
                const ctx = document.getElementById('cancelChart').getContext('2d');
                cancelChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: CANCEL_REASONS.map(r => r.reason),
                        datasets: [{
                            data: CANCEL_REASONS.map(r => r.count),
                            backgroundColor: CANCEL_REASONS.map(r => r.color),
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw}`
                                }
                            }
                        }
                    }
                });

                const total = CANCEL_REASONS.reduce((s, r) => s + r.count, 0);
                document.getElementById('cancelLegend').innerHTML = CANCEL_REASONS.map(r => `
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-2.5 h-2.5 rounded-full" style="background:${r.color};"></div>
        <span class="text-xs text-gray-600">${r.reason}</span>
      </div>
      <span class="text-xs font-bold text-gray-800">${r.count} <span class="text-gray-400 font-normal">(${Math.round(r.count/total*100)}%)</span></span>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // TRANSACTIONS TABLE
            // ══════════════════════════════════════════════
            function renderTransactions() {
                document.getElementById('txTable').innerHTML = TRANSACTIONS.map(tx => `
    <tr class="tx-row border-b border-gray-50">
      <td class="py-3 pr-3 font-semibold text-blue-600 text-sm">${tx.id}</td>
      <td class="py-3 pr-3 font-bold text-gray-800 text-sm">${tx.table}</td>
      <td class="py-3 pr-3 text-gray-500 text-sm max-w-[160px] truncate">${tx.items}</td>
      <td class="py-3 pr-3 text-right font-bold text-gray-800 text-sm">Rs. ${tx.amount.toLocaleString()}</td>
      <td class="py-3 text-right">
        <div class="flex items-center justify-end gap-2">
          <span class="text-gray-400 text-sm">${tx.time}</span>
          <span class="badge ${tx.status==='completed'?'':'opacity-70'}"
            style="background:${tx.status==='completed'?'#dcfce7':'#fee2e2'};color:${tx.status==='completed'?'#15803d':'#dc2626'};">
            ${tx.status}
          </span>
        </div>
      </td>
    </tr>`).join('');
            }

            // ══════════════════════════════════════════════
            // STATUS DONUT
            // ══════════════════════════════════════════════
            function initStatusChart() {
                const ctx = document.getElementById('statusChart').getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Pending', 'Cancelled'],
                        datasets: [{
                            data: [STATUS_DATA.completed, STATUS_DATA.pending, STATUS_DATA.cancelled],
                            backgroundColor: ['#16a34a', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.label}: ${ctx.raw}`
                                }
                            }
                        }
                    }
                });

                const total = STATUS_DATA.completed + STATUS_DATA.pending + STATUS_DATA.cancelled;
                document.getElementById('statusSummary').innerHTML = [{
                        label: 'Completed',
                        val: STATUS_DATA.completed,
                        color: '#16a34a'
                    },
                    {
                        label: 'Pending',
                        val: STATUS_DATA.pending,
                        color: '#f59e0b'
                    },
                    {
                        label: 'Cancelled',
                        val: STATUS_DATA.cancelled,
                        color: '#ef4444'
                    },
                ].map(s => `
    <div>
      <p class="text-lg font-extrabold" style="color:${s.color};">${s.val}</p>
      <p class="text-xs text-gray-400">${s.label}</p>
      <p class="text-xs font-semibold text-gray-600">${Math.round(s.val/total*100)}%</p>
    </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // RUNNING TOTAL LINE CHART
            // ══════════════════════════════════════════════
            function initRunningChart() {
                const ctx = document.getElementById('runningChart').getContext('2d');
                runningChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: RUNNING.labels,
                        datasets: [{
                                label: 'Actual',
                                data: RUNNING.actual,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37,99,235,0.08)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
                                pointBackgroundColor: '#2563eb'
                            },
                            {
                                label: 'Target',
                                data: RUNNING.target,
                                borderColor: '#d1d5db',
                                borderDash: [6, 4],
                                tension: 0,
                                fill: false,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` Rs. ${ctx.raw.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    color: '#f1f5f9'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    callback: v => 'Rs. ' + (v / 1000) + 'k'
                                }
                            }
                        }
                    }
                });
            }

            // ══════════════════════════════════════════════
            // PERIOD SWITCH
            // ══════════════════════════════════════════════
            function setPeriod(period, btn) {
                currentPeriod = period;
                document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                initRevenueChart(period);
            }

            // ══════════════════════════════════════════════
            // TOP MODE SWITCH
            // ══════════════════════════════════════════════
            function setTopMode(mode, btn) {
                topMode = mode;
                document.querySelectorAll('.top-mode-btn').forEach(b => {
                    b.classList.remove('active-top', 'bg-gray-800', 'text-white');
                    b.classList.add('text-gray-500');
                });
                btn.classList.add('active-top', 'bg-gray-800', 'text-white');
                btn.classList.remove('text-gray-500');
                renderTopItems();
            }

            // ══════════════════════════════════════════════
            // EXPORT
            // ══════════════════════════════════════════════
            function exportReport() {
                const toast = document.getElementById('toast');
                document.getElementById('toastMsg').textContent = 'Preparing PDF export...';
                toast.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('toastMsg').textContent = 'Report exported!';
                }, 1000);
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }

            // ══════════════════════════════════════════════
            // INIT ALL
            // ══════════════════════════════════════════════
            function initAll() {
                initRevenueChart('weekly');
                initCategoryChart();
                renderTopItems();
                initDowChart();
                renderHeatmap();
                renderPeakHours();
                renderTablePerf();
                renderStaffPerf();
                initCancelChart();
                renderTransactions();
                initStatusChart();
                initRunningChart();
            }

            // Run immediately if DOM ready, otherwise wait
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAll);
            } else {
                initAll();
            }
        </script>
    </body>

    </html>

@endsection
