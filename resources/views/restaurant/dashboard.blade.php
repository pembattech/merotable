@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')

    <style>
        /* chart card */
        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e9eef5;
            margin-bottom: 32px;
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

        /* ── Subscription pill ── */
        .subscription-pill {
            display: flex;
            align-items: stretch;
            border-radius: 10px;
            border: 1.5px solid #e0e3ef;
            background: #fff;
            overflow: hidden;
            cursor: default;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .subscription-pill:hover {
            border-color: #c5c9dc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .plan-label {
            padding: 7px 12px;
            background: #2563eb;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }

        .plan-tier {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1;
        }

        .plan-sub {
            font-size: 9px;
            font-weight: 500;
            opacity: 0.75;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .expiry-section {
            padding: 7px 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }

        .expiry-top {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9399b0;
            line-height: 1;
        }

        .expiry-days {
            font-size: 12px;
            font-weight: 700;
            color: #e67700;
            letter-spacing: -0.2px;
            line-height: 1;
        }

        /* ── Restaurant status ── */
        .restaurant-status {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 10px;
            border: 1.5px solid #bbf7d0;
            background: #f0fdf4;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
            user-select: none;
        }

        .restaurant-status:hover {
            background: #dcfce7;
            border-color: #86efac;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.12);
        }

        .status-indicator {
            position: relative;
            width: 8px;
            height: 8px;
            flex-shrink: 0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            position: absolute;
            inset: 0;
        }

        .status-ring {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 2px solid #16a34a;
            position: absolute;
            inset: 0;
            animation: ring-pulse 2s ease-out infinite;
            opacity: 0;
        }

        @keyframes ring-pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            100% {
                transform: scale(2.6);
                opacity: 0;
            }
        }

        .status-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .status-sub {
            font-size: 9px;
            font-weight: 500;
            color: #4ade80;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-title {
            font-size: 11px;
            font-weight: 700;
            color: #15803d;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Notification button ── */
        .notif-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1.5px solid #e0e3ef;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s, transform 0.15s;
            flex-shrink: 0;
        }

        .notif-btn:hover {
            background: #f5f6fb;
            border-color: #c5c9dc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .notif-btn:active {
            transform: scale(0.95);
        }

        .notif-btn svg {
            width: 16px;
            height: 16px;
            color: #5b6080;
            transition: color 0.2s;
        }

        .notif-btn:hover svg {
            color: #2563eb;
        }

        .notif-badge {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid #fff;
        }

        /* ── Top menu items ── */
        .top-items-container {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .top-item-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.1s;
        }

        .top-item-card:hover {
            transform: translateY(-2px);
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .item-rank {
            font-weight: bold;
            font-size: 0.875rem;
            color: #4b5563;
            min-width: 20px;
            text-align: center;
        }

        .item-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #111827;
        }

        .item-orders {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .item-revenue {
            font-weight: 600;
            font-size: 0.875rem;
            color: #111827;
        }

        /* ── Empty state ── */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 32px 16px;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #eff4ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .empty-icon svg {
            width: 26px;
            height: 26px;
        }

        .empty-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
        }

        .empty-desc {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
            max-width: 240px;
            line-height: 1.5;
        }

        /* ── Desktop overrides ── */
        @media (min-width: 768px) {
            .plan-tier {
                font-size: 13px;
            }

            .plan-sub {
                font-size: 10px;
            }

            .expiry-top {
                font-size: 10px;
            }

            .expiry-days {
                font-size: 13px;
            }

            .status-sub {
                font-size: 10px;
            }

            .status-title {
                font-size: 12px;
            }

            .notif-btn {
                width: 40px;
                height: 40px;
            }

            .notif-btn svg {
                width: 17px;
                height: 17px;
            }

            .item-rank {
                font-size: 1rem;
                min-width: 24px;
            }

            .item-name {
                font-size: 1rem;
            }

            .item-orders {
                font-size: 0.875rem;
            }

            .item-revenue {
                font-size: 1rem;
            }
        }
    </style>

    {{-- ── Header ── --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8 fade-up d1">
        <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Restaurant Overview</h1>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Subscription --}}
            <div class="subscription-pill" title="Your current plan">
                <div class="plan-label">
                    <span class="plan-tier" id="planTier">N/A</span>
                    <span class="plan-sub">Plan</span>
                </div>
                <div class="expiry-section">
                    <span class="expiry-top">Expires in</span>
                    <span class="expiry-days" id="expiryDays">N/A days</span>
                </div>
            </div>

            {{-- Restaurant Status --}}
            <div class="restaurant-status">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <div class="status-ring"></div>
                </div>
                <div class="status-text">
                    <span class="status-sub">Restaurant</span>
                    <span class="status-title">Open</span>
                </div>
            </div>

            {{-- Notifications --}}
            <button class="notif-btn" aria-label="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notif-badge"></span>
            </button>

        </div>
    </header>

    {{-- ── Stat cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10 fade-up d2">

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Today's Revenue</p>
            <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2" id="todayRevenue">Rs. 0</h3>
            <p class="text-xs text-green-600 mt-1 md:mt-2 font-bold">
                <span id="revenueTrend"></span>
                <span id="revenueChangePercent"></span> from yesterday
            </p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Active Orders</p>
            <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2" id="totalActiveOrders">0</h3>
            <p class="text-xs text-blue-600 mt-1 md:mt-2 font-bold">Live tracking active</p>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs md:text-sm text-gray-500 font-medium">Occupied Tables</p>
            <h3 class="text-2xl md:text-3xl font-bold mt-1 md:mt-2">
                <span id="totalOccupiedTables">0</span>/<span id="totalTables">0</span>
            </h3>
            <p class="text-xs text-gray-400 mt-1 md:mt-2 font-bold">Standard Capacity</p>
        </div>

    </div>

    {{-- ── Live Table Map (disabled) ── --}}
    {{-- <section class="mb-8 md:mb-10">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-base md:text-xl font-bold text-gray-800">Live Table Map</h2>
            <button class="text-xs md:text-sm text-blue-600 font-bold hover:underline">Refresh Status</button>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">

            <div class="bg-blue-600 text-white p-3 md:p-4 rounded-xl shadow-lg border-2 border-blue-700">
                <div class="flex justify-between items-start">
                    <span class="text-lg md:text-2xl font-black">T-01</span>
                    <span
                        class="text-[9px] md:text-[10px] bg-blue-800 px-1.5 md:px-2 py-0.5 rounded font-bold uppercase tracking-wider">
                        Occupied
                    </span>
                </div>
                <div class="mt-3 md:mt-4">
                    <p class="text-[10px] md:text-xs opacity-80">Order Amount</p>
                    <p class="text-sm md:text-base font-bold">Rs. 850</p>
                </div>
            </div>

            <div
                class="bg-white p-3 md:p-4 rounded-xl border border-gray-200 hover:border-blue-400 transition cursor-pointer group">
                <span class="text-lg md:text-2xl font-black text-gray-300 group-hover:text-blue-500">T-02</span>
                <p class="mt-3 md:mt-4 text-[10px] md:text-xs font-bold text-gray-400">Available</p>
            </div>

        </div>
    </section> --}}

    {{-- ── Top Selling Items ── --}}
    <div class="chart-card fade-up d3">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Top Selling Items</h3>
                <p class="text-xs text-gray-400 mt-0.5">By revenue this week</p>
            </div>
            <div id="topModeToggle" class="flex gap-1 bg-gray-100 rounded-lg p-1">
                <button onclick="setTopMode('revenue', this)"
                    class="top-mode-btn active-top px-3 py-1 rounded-md text-xs font-semibold text-white bg-gray-800 transition">Revenue</button>
                <button onclick="setTopMode('qty', this)"
                    class="top-mode-btn px-3 py-1 rounded-md text-xs font-semibold text-gray-500 transition">Quantity</button>
            </div>
        </div>
        <div class="space-y-3" id="topItems"></div>
    </div>

    {{-- TODO: Add Recent Orders, Staff on Duty --}}

    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const token = localStorage.getItem('auth_token');

            // ── Helpers ──
            const toast = (msg, type) =>
                typeof window.showToast === 'function' ? window.showToast(msg, type) : console.warn(msg);

            const escapeHtml = (str) => String(str ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[c]));

            const formatRs = (n) => 'Rs. ' + Number(n ?? 0).toLocaleString();

            // TOP SELLING ITEMS (weekly)
            let topMode = 'revenue';
            const palette = ['#2563eb', '#0ea5e9', '#6366f1', '#06b6d4', '#a78bfa'];

            let TOP_ITEMS = [];


            // Must be global because the buttons use inline onclick=""
            window.setTopMode = function (mode, btn) {
                topMode = mode;
                document.querySelectorAll('.top-mode-btn').forEach(b => {
                    b.classList.remove('active-top', 'bg-gray-800', 'text-white');
                    b.classList.add('text-gray-500');
                });
                btn.classList.add('active-top', 'bg-gray-800', 'text-white');
                btn.classList.remove('text-gray-500');
                renderTopItems();
            };

            function renderTopItems() {
                const container = document.getElementById('topItems');
                const toggle = document.getElementById('topModeToggle');

                // ---- Empty state ----
                if (!TOP_ITEMS || TOP_ITEMS.length === 0) {
                    toggle.classList.add('hidden');
                    container.innerHTML = `
                                            <div class="empty-state">
                                                <div class="empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M3 3v18h18M7 15l4-4 3 3 5-6" />
                                                    </svg>
                                                </div>
                                                <p class="empty-title">No sales yet this week</p>
                                                <p class="empty-desc">Your best-selling items will show up here once orders start coming in.</p>
                                            </div>`;
                    return;
                }

                toggle.classList.remove('hidden');

                // ---- Normal state ----
                const key = topMode === 'revenue' ? 'revenue' : 'qty';
                const sorted = [...TOP_ITEMS].sort((a, b) => b[key] - a[key]);
                const maxVal = sorted[0][key] || 1; // avoid divide-by-zero

                container.innerHTML = sorted.map((item, i) => `
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-gray-300 w-4">${i + 1}</span>
                                            <div class="flex-1">
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-sm font-semibold text-gray-700">${escapeHtml(item.name)}</span>
                                                    <span class="text-sm font-bold text-gray-900">
                                                        ${topMode === 'revenue' ? formatRs(item.revenue) : item.qty.toLocaleString() + ' sold'}
                                                    </span>
                                                </div>
                                                <div class="prog-bar">
                                                    <div class="prog-fill" style="width:${Math.round(item[key] / maxVal * 100)}%;background:${item.color};"></div>
                                                </div>
                                            </div>
                                        </div>`).join('');
            }

            // ══════════════════════════════════════════════
            // LOAD DASHBOARD DATA
            // ══════════════════════════════════════════════
            async function loadRestro() {
                try {
                    const res = await fetch(`/api/v1/owner/restaurant`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (res.status === 401) {
                        window.location.href = '/login';
                        return;
                    }

                    const data = await res.json();

                    if (!data.success) {
                        toast(data.message || 'Something went wrong ❌', 'error');
                        return;
                    }

                    const subscription = data.data.activeSubscription;

                    // No subscription or expired -> pricing page
                    if (!subscription || (subscription.daysLeft ?? 0) <= 0) {
                        window.location.href = '/pricing';
                        return;
                    }

                    const planTier = subscription.plan?.name ?? 'No Plan';
                    const expiryDays = parseInt(subscription.daysLeft) || 0;

                    const todayRevenue = Number(data.data.todayRevenue ?? 0);
                    const yesterdayRevenue = Number(data.data.yesterdayRevenue ?? 0);
                    const changePercent = data.data.revenueChangePercent ?? 0;
                    const isUp = todayRevenue >= yesterdayRevenue;

                    // ---- Dashboard values ----
                    document.getElementById('planTier').textContent = planTier;
                    document.getElementById('expiryDays').textContent = expiryDays + ' days';
                    document.getElementById('totalActiveOrders').textContent = data.data.activeOrders ?? 0;
                    document.getElementById('totalOccupiedTables').textContent = data.data.occupiedTables ?? 0;
                    document.getElementById('totalTables').textContent = data.data.tableCount ?? 0;
                    document.getElementById('todayRevenue').textContent = formatRs(todayRevenue);
                    document.getElementById('revenueChangePercent').textContent = changePercent + '%';

                    const trendEl = document.getElementById('revenueTrend');
                    trendEl.textContent = isUp ? '↑' : '↓';
                    const trendWrap = trendEl.parentElement;
                    trendWrap.classList.toggle('text-green-600', isUp);
                    trendWrap.classList.toggle('text-red-600', !isUp);

                    // ---- Top menu items (today) ----
                    TOP_ITEMS = data.data.topItems.map((it, i) => ({
                        name: it.name,
                        revenue: Number(it.revenue ?? 0),
                        qty: Number(it.total_orders ?? 0),
                        color: palette[i % palette.length]
                    }));
                    renderTopItems();

                } catch (error) {
                    console.error('Error:', error);
                    toast('Network error ❌', 'error');
                }
            }

            await loadRestro();
        });
    </script>

@endsection