<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reports') – MeroTable</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { background: #f0f4f8; }

        .nav-tab { padding: 10px 16px; font-size: .875rem; font-weight: 600; color: #6b7280; border-bottom: 2px solid transparent; white-space: nowrap; margin-bottom: -1px; transition: color .15s; }
        .nav-tab:hover { color: #1f2937; }
        .nav-tab.active { color: #2563eb; border-bottom-color: #2563eb; }

        .period-btn { padding: 6px 16px; border-radius: 8px; font-size: .85rem; font-weight: 600; color: #6b7280; transition: all .15s; cursor: pointer; border: none; background: transparent; white-space: nowrap; }
        .period-btn:hover { background: #f1f5f9; }
        .period-btn.active { background: #1e293b; color: #fff; }

        .stat-card { background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #e9eef5; }
        .chart-card { background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e9eef5; }

        .prog-bar { height: 6px; border-radius: 3px; background: #e9eef5; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 3px; transition: width .8s cubic-bezier(.4, 0, .2, 1); }

        .tx-row { transition: background .12s; }
        .tx-row:hover { background: #f8fafc; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 999px; font-size: .72rem; font-weight: 700; }

        .input { padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: .85rem; color: #1f2937; background: #fff; width: 100%; }
        .input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px #dbeafe; }
        .input.auto { width: auto; }

        .btn { display: inline-flex; align-items: center; gap: 8px; font-size: .85rem; font-weight: 600; padding: 9px 16px; border-radius: 12px; transition: all .15s; cursor: pointer; border: 1px solid transparent; }
        .btn-dark { background: #111827; color: #fff; }
        .btn-dark:hover { background: #374151; }
        .btn-blue { background: #2563eb; color: #fff; }
        .btn-blue:hover { background: #1d4ed8; }
        .btn-ghost { background: #fff; color: #374151; border-color: #e5e7eb; }
        .btn-ghost:hover { background: #f8fafc; }
        .btn-sm { padding: 5px 12px; font-size: .75rem; border-radius: 8px; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }

        .modal-back { position: fixed; inset: 0; background: rgba(15, 23, 42, .45); display: none; align-items: center; justify-content: center; padding: 16px; z-index: 50; }
        .modal-back.open { display: flex; }
        .modal { background: #fff; border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; max-height: 90vh; overflow-y: auto; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp .5s ease forwards; }
        .d1 { animation-delay: .05s; } .d2 { animation-delay: .10s; } .d3 { animation-delay: .15s; }
        .d4 { animation-delay: .20s; } .d5 { animation-delay: .25s; } .d6 { animation-delay: .30s; }
        .d7 { animation-delay: .35s; } .d8 { animation-delay: .40s; }

        #content { transition: opacity .2s; }
    </style>
</head>

@php
    $noFilter = $__env->hasSection('no_filter');
    // Adjust the paths to match your routes
    $tabs = [
        ['label' => 'Overview', 'path' => 'reports/financial-insights'],
        ['label' => 'Profit & Loss', 'path' => 'reports/profit-loss'],
        ['label' => 'Sales & Payments', 'path' => 'reports/sales-payments'],
        ['label' => 'Unpaid & Dues', 'path' => 'reports/dues'],
        ['label' => 'Expenses', 'path' => 'reports/expenses'],
    ];
@endphp

<body class="min-h-screen p-5 md:p-8">
    <div class="max-w-7xl mx-auto">

        <!-- REPORT TABS -->
        <nav class="flex gap-1 overflow-x-auto border-b border-gray-200 mb-5 fade-up d1" aria-label="Reports">
            @foreach ($tabs as $t)
                <a href="{{ url($t['path']) }}" data-nav-link
                    class="nav-tab {{ request()->is($t['path']) ? 'active' : '' }}">{{ $t['label'] }}</a>
            @endforeach
        </nav>

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 fade-up d1">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">@yield('heading')</h1>
                <p class="text-sm text-gray-400 mt-1">@yield('subheading')<span id="rangeLabel" class="font-semibold text-gray-500"></span></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge" style="background:#fef3c7;color:#92400e;">Demo data</span>
                @yield('actions')
            </div>
        </div>

        @if ($noFilter)
            <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 mb-5 text-sm text-gray-500 fade-up d2">
                @yield('no_filter')
            </div>
        @else
            <!-- DATE FILTER -->
            <div class="bg-white border border-gray-200 rounded-2xl p-3 mb-3 flex flex-col lg:flex-row lg:items-center gap-3 fade-up d2">
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
                <div class="flex flex-wrap items-center gap-2">
                    <input type="date" id="dateFrom" class="input auto" aria-label="From date">
                    <span class="text-gray-400 text-sm">to</span>
                    <input type="date" id="dateTo" class="input auto" aria-label="To date">
                    <button id="applyBtn" class="btn btn-blue">Apply</button>
                </div>
            </div>
            <div id="reportMessage" class="text-xs text-gray-400 mb-4 min-h-[16px] px-1"></div>
        @endif

        <div id="content">
            @yield('content')
        </div>
    </div>

    <!-- TOAST -->
    <div id="toast" class="fixed bottom-6 right-6 z-[60] hidden">
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl bg-gray-900 text-white text-sm font-medium">
            <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span id="toastMsg"></span>
        </div>
    </div>

    <script>
        /* =====================================================
           SHARED HELPERS
        ===================================================== */
        const NO_FILTER = @json($noFilter);
        const $ = (id) => document.getElementById(id);
        const CURRENCY = 'Rs.';
        const PALETTE = ['#2563eb', '#0ea5e9', '#6366f1', '#06b6d4', '#a78bfa', '#f59e0b', '#10b981', '#ec4899'];

        Chart.defaults.font.family = 'Plus Jakarta Sans';
        Chart.defaults.color = '#9ca3af';

        const pad = (n) => String(n).padStart(2, '0');
        const fmtDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        const parseLocalDate = (s) => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };
        const getTodayDate = () => fmtDate(new Date());
        const money = (n) => `${CURRENCY} ${Math.round(Number(n || 0)).toLocaleString()}`;
        const kFmt = (v) => Math.abs(v) >= 1000 ? `${CURRENCY} ${+(v / 1000).toFixed(1)}k` : `${CURRENCY} ${v}`;
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const sum = (a) => a.reduce((s, x) => s + x, 0);
        const pct = (a, b) => (b ? (a / b) * 100 : 0);
        const emptyState = (msg) => `<p class="text-sm text-gray-400 text-center py-8">${msg}</p>`;

        function deltaBadge(cur, prev, invert = false) {
            if (!prev) return '';
            const p = ((cur - prev) / prev) * 100;
            const up = p >= 0;
            const good = invert ? !up : up;
            return `<span class="badge" style="background:${good ? '#dcfce7' : '#fee2e2'};color:${good ? '#15803d' : '#dc2626'};">${up ? '↑' : '↓'} ${Math.abs(p).toFixed(1)}%</span><span class="text-xs text-gray-400 ml-1.5">vs prev. period</span>`;
        }

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

        function daysBetween(from, to) {
            const out = [];
            const end = parseLocalDate(to);
            for (let d = parseLocalDate(from); d <= end; d.setDate(d.getDate() + 1)) out.push(fmtDate(d));
            return out;
        }

        // Previous period of the same length, ending the day before `from`
        function prevRange(from, to) {
            const n = daysBetween(from, to).length;
            const pt = parseLocalDate(from); pt.setDate(pt.getDate() - 1);
            const pf = new Date(pt); pf.setDate(pf.getDate() - (n - 1));
            return [fmtDate(pf), fmtDate(pt)];
        }

        // Group dates into daily / weekly / monthly buckets depending on range length
        function bucketize(from, to) {
            const dates = daysBetween(from, to);
            const n = dates.length;
            const size = n <= 31 ? 1 : (n <= 120 ? 7 : 30);
            const out = [];
            for (let i = 0; i < n; i += size) {
                const ds = dates.slice(i, i + size);
                const d0 = parseLocalDate(ds[0]);
                const label = size === 30
                    ? d0.toLocaleDateString([], { month: 'short', year: '2-digit' })
                    : d0.toLocaleDateString([], { month: 'short', day: 'numeric' });
                out.push({ label, dates: ds });
            }
            return out;
        }

        function drawChart(id, type, data, options = {}) {
            if (window.__charts === undefined) window.__charts = {};
            if (window.__charts[id]) window.__charts[id].destroy();
            window.__charts[id] = new Chart($(id).getContext('2d'), {
                type, data,
                options: { responsive: true, maintainAspectRatio: false, ...options }
            });
        }

        function toast(msg) {
            $('toastMsg').textContent = msg;
            $('toast').classList.remove('hidden');
            clearTimeout(window.__toastT);
            window.__toastT = setTimeout(() => $('toast').classList.add('hidden'), 2600);
        }

        function downloadCsv(name, rows) {
            const csv = rows.map((r) => r.map((c) => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
            a.download = name;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        const openModal = (id) => $(id).classList.add('open');
        const closeModal = (id) => $(id).classList.remove('open');
        document.addEventListener('click', (e) => {
            if (e.target.classList && e.target.classList.contains('modal-back')) e.target.classList.remove('open');
            if (e.target.closest && e.target.closest('[data-close-modal]')) {
                e.target.closest('.modal-back').classList.remove('open');
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') document.querySelectorAll('.modal-back.open').forEach((m) => m.classList.remove('open'));
        });

        /* =====================================================
           DEMO DATA ENGINE
           Deterministic: the same date always produces the same
           numbers, so every report page agrees with the others.
           Replace with your real API calls when ready.
        ===================================================== */
        const Demo = (() => {
            const hashStr = (s) => { let h = 2166136261; for (let i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); } return h >>> 0; };
            const mulberry32 = (a) => () => { a |= 0; a = a + 0x6D2B79F5 | 0; let t = Math.imul(a ^ a >>> 15, 1 | a); t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t; return ((t ^ t >>> 14) >>> 0) / 4294967296; };
            const rngFor = (key) => mulberry32(hashStr(key));
            const r10 = (x) => Math.round(x / 10) * 10;

            const CATS = [
                { key: 'ingredients', name: 'Ingredients (COGS)', short: 'Ingredients', color: '#2563eb', daily: 13500 },
                { key: 'wages', name: 'Staff wages', short: 'Staff wages', color: '#6366f1', daily: 8300 },
                { key: 'rent', name: 'Rent', short: 'Rent', color: '#0ea5e9', daily: 3500 },
                { key: 'utilities', name: 'Utilities', short: 'Utilities', color: '#06b6d4', daily: 1400 },
                { key: 'marketing', name: 'Marketing', short: 'Marketing', color: '#a78bfa', daily: 700 },
                { key: 'maintenance', name: 'Maintenance', short: 'Maintenance', color: '#f59e0b', daily: 600 },
                { key: 'other', name: 'Other', short: 'Other', color: '#94a3b8', daily: 700 },
            ];

            const METHODS = [
                { key: 'cash', name: 'Cash', color: '#16a34a', avg: 480 },
                { key: 'esewa', name: 'eSewa', color: '#2563eb', avg: 690 },
                { key: 'fonepay', name: 'Fonepay QR', color: '#6366f1', avg: 650 },
                { key: 'khalti', name: 'Khalti', color: '#a78bfa', avg: 710 },
                { key: 'card', name: 'Card', color: '#f59e0b', avg: 980 },
                { key: 'credit', name: 'On credit', color: '#ef4444', avg: 1100 },
            ];

            const revCache = new Map();
            const expCache = new Map();

            function dayRevenue(ds) {
                if (revCache.has(ds)) return revCache.get(ds);
                const r = rngFor('rev' + ds);
                const dow = parseLocalDate(ds).getDay(); // 0 = Sun
                const boost = [1.05, 0.85, 0.9, 0.95, 1.05, 1.25, 1.3][dow];
                const gross = Math.round((30000 + r() * 20000) * boost / 100) * 100;
                const discounts = r10(gross * (0.02 + r() * 0.025));
                const net = gross - discounts;
                const food = r10(gross * 0.68);
                const beverage = r10(gross * 0.24);
                const other = gross - food - beverage;
                const orders = Math.max(1, Math.round(net / (520 + r() * 160)));

                const base = { cash: .38, esewa: .18, fonepay: .16, khalti: .07, card: .11, credit: .10 };
                const w = {}; let ws = 0;
                Object.keys(base).forEach((k) => { w[k] = base[k] * (0.8 + r() * 0.4); ws += w[k]; });
                const methods = {}; let acc = 0;
                Object.keys(base).forEach((k) => { if (k !== 'cash') { methods[k] = r10(net * w[k] / ws); acc += methods[k]; } });
                methods.cash = net - acc;

                const out = { date: ds, gross, discounts, net, food, beverage, other, orders, methods };
                revCache.set(ds, out);
                return out;
            }

            function dayExpenses(ds) {
                if (expCache.has(ds)) return expCache.get(ds);
                const r = rngFor('exp' + ds);
                const rev = dayRevenue(ds).net;
                const out = [];
                const add = (catKey, desc, vendor, method, amount) =>
                    out.push({ date: ds, catKey, desc, vendor, method, amount: r10(amount) });

                const ing = rev * (0.30 + r() * 0.07);
                add('ingredients', 'Chicken & buff supply', 'Himalayan Meat Suppliers', 'Cash', ing * 0.45);
                add('ingredients', 'Vegetables & greens', 'Kalimati Market', 'Cash', ing * 0.17);
                add('ingredients', 'Rice, flour & cooking oil', 'Bhat-Bhateni Wholesale', 'eSewa', ing * 0.24);
                add('ingredients', 'Dairy & spices', 'Local Dairy Co.', 'Cash', ing * 0.14);
                add('wages', 'Staff wages – daily accrual', 'Staff payroll', 'Bank transfer', 8000 + r() * 600);
                add('rent', 'Monthly rent – daily accrual', 'Landlord – Sharma', 'Bank transfer', 3500);
                add('utilities', 'Electricity & water – accrual', 'NEA / KUKL', 'eSewa', 1000 + r() * 200);
                if (r() < 0.16) add('utilities', 'LPG cylinder refill', 'Gas depot', 'Cash', 1800 + r() * 300);
                if (r() < 0.40) add('marketing', 'Facebook & Instagram ads', 'Meta Ads', 'Card', 500 + r() * 700);
                if (r() < 0.20) add('maintenance', 'Equipment repair', 'Local technician', 'Cash', 600 + r() * 1900);
                if (r() < 0.60) add('other', 'Cleaning & packaging supplies', 'Supplies shop', 'Cash', 400 + r() * 800);

                expCache.set(ds, out);
                return out;
            }

            return { CATS, METHODS, rngFor, r10, dayRevenue, dayExpenses, catByKey: (k) => CATS.find((c) => c.key === k) };
        })();

        /* =====================================================
           RANGE / FILTER WIRING
        ===================================================== */
        function setMessage(text, isError = false) {
            const el = $('reportMessage');
            if (!el) return;
            el.textContent = text;
            el.classList.toggle('text-red-500', isError);
            el.classList.toggle('text-gray-400', !isError);
        }

        function syncNavLinks(from, to) {
            document.querySelectorAll('[data-nav-link]').forEach((a) => {
                const url = new URL(a.getAttribute('href'), window.location.origin);
                url.searchParams.set('date_from', from);
                url.searchParams.set('date_to', to);
                a.href = url.toString();
            });
        }

        function setActivePreset(name) {
            document.querySelectorAll('#presets .period-btn').forEach((b) =>
                b.classList.toggle('active', b.dataset.preset === name));
        }

        function applyRange(from, to) {
            if (from > to) { setMessage('"From" date cannot be after "To" date.', true); return; }
            if (daysBetween(from, to).length > 400) { setMessage('Please pick a range of 400 days or less.', true); return; }

            window.__range = { from, to };
            if ($('dateFrom')) { $('dateFrom').value = from; $('dateTo').value = to; }
            if (!NO_FILTER) $('rangeLabel').textContent = ` · ${from === to ? from : from + ' → ' + to}`;
            syncNavLinks(from, to);
            setMessage(`Showing demo data for ${from === to ? from : from + ' to ' + to}.`);

            if (typeof window.renderReport === 'function') window.renderReport(from, to);

            const u = new URL(window.location.href);
            u.searchParams.set('date_from', from);
            u.searchParams.set('date_to', to);
            history.replaceState(null, '', u);
        }

        function initLayout() {
            const params = new URLSearchParams(window.location.search);
            const today = getTodayDate();
            const from = params.get('date_from') || today;
            const to = params.get('date_to') || today;

            if (NO_FILTER) {
                window.__range = { from, to };
                syncNavLinks(from, to);
                if (typeof window.renderReport === 'function') window.renderReport(from, to);
                return;
            }

            $('dateFrom').max = $('dateTo').max = today;

            document.querySelectorAll('#presets .period-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const name = btn.dataset.preset;
                    setActivePreset(name);
                    if (name === 'custom') { $('dateFrom').focus(); return; }
                    const [f, t] = presetRange(name);
                    applyRange(f, t);
                });
            });
            ['dateFrom', 'dateTo'].forEach((id) => $(id).addEventListener('change', () => setActivePreset('custom')));
            $('applyBtn').addEventListener('click', () => {
                setActivePreset('custom');
                applyRange($('dateFrom').value, $('dateTo').value);
            });

            const match = ['today', 'yesterday', 'last7', 'last30', 'month', 'lastMonth'].find((p) => {
                const r = presetRange(p);
                return r[0] === from && r[1] === to;
            });
            setActivePreset(match || 'custom');
            applyRange(from, to);
        }

        // Runs after the page's own script (pushed below) has defined window.renderReport
        document.addEventListener('DOMContentLoaded', initLayout);
    </script>

    @stack('scripts')
</body>

</html>