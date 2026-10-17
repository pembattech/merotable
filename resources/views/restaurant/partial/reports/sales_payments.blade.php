@extends('restaurant.partial.reports.layout')

@section('title', 'Sales & Payments')
@section('heading', 'Sales & Payments')
@section('subheading', 'How much you sold and how customers paid')

@section('actions')
    <button id="exportBtn" class="btn btn-dark">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export CSV
    </button>
@endsection

@section('content')
    <!-- KPI ROW 1 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div class="stat-card fade-up d2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Net Sales</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiNet">—</p>
            <div class="mt-2 flex items-center flex-wrap" id="kpiNetHint"></div>
        </div>
        <div class="stat-card fade-up d3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Collected</p>
            <p class="text-2xl font-extrabold text-green-600" id="kpiCollected">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiCollectedHint"></p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">On Credit (Unpaid)</p>
            <p class="text-2xl font-extrabold text-red-500" id="kpiCredit">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiCreditHint"></p>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Avg. Bill</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiAvg">—</p>
            <p class="text-xs text-gray-400 mt-2">Net sales ÷ orders</p>
        </div>
    </div>

    <!-- KPI ROW 2 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card fade-up d3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Orders</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiOrders">—</p>
            <p class="text-xs text-gray-400 mt-1" id="kpiOrdersHint"></p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Discounts Given</p>
            <p class="text-xl font-extrabold text-orange-500" id="kpiDisc">—</p>
            <p class="text-xs text-gray-400 mt-1" id="kpiDiscHint"></p>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Digital Payments</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiDigital">—</p>
            <p class="text-xs text-gray-400 mt-1">eSewa, Fonepay, Khalti &amp; card</p>
        </div>
        <div class="stat-card fade-up d6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Top Method</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiTopMethod">—</p>
            <p class="text-xs text-gray-400 mt-1" id="kpiTopMethodHint"></p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d4">
            <div class="mb-5">
                <h3 class="font-bold text-gray-800 text-base">Sales by Payment Method</h3>
                <p class="text-xs text-gray-400 mt-0.5">Stacked by how customers paid</p>
            </div>
            <div style="height:260px;"><canvas id="methodChart"></canvas></div>
        </div>

        <div class="chart-card fade-up d5">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Payment Mix</h3>
                <p class="text-xs text-gray-400 mt-0.5">Share of net sales</p>
            </div>
            <div style="height:170px;" class="mb-4"><canvas id="mixChart"></canvas></div>
            <div class="space-y-2" id="mixLegend"></div>
        </div>
    </div>

    <!-- TABLE + SALES MIX -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d6">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Payment Method Summary</h3>
                <p class="text-xs text-gray-400 mt-0.5">Transactions, amounts and average ticket</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Method</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Txns</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="text-left pb-3 pl-6 text-xs font-semibold text-gray-400 uppercase tracking-wider w-40">Share</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Avg ticket</th>
                        </tr>
                    </thead>
                    <tbody id="methodBody"></tbody>
                </table>
            </div>
        </div>

        <div class="chart-card fade-up d7">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Sales Mix</h3>
                <p class="text-xs text-gray-400 mt-0.5">By category and order type</p>
            </div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Category</p>
            <div class="space-y-3 mb-6" id="catMix"></div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Order type</p>
            <div class="space-y-3" id="typeMix"></div>
        </div>
    </div>

    <!-- RECENT PAYMENTS -->
    <div class="chart-card fade-up d8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Latest Payments</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="payHint">—</p>
            </div>
            <select id="payFilter" class="input auto">
                <option value="all">All methods</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order ID</th>
                        <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Table</th>
                        <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Method</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Time</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody id="payBody"></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const METHODS = Demo.METHODS;
        const DIGITAL = ['esewa', 'fonepay', 'khalti', 'card'];
        let payments = [];

        function calc(from, to) {
            const dates = daysBetween(from, to);
            const t = { days: dates.length, gross: 0, discounts: 0, net: 0, food: 0, beverage: 0, other: 0, orders: 0, m: {} };
            METHODS.forEach((m) => { t.m[m.key] = 0; });
            dates.forEach((d) => {
                const r = Demo.dayRevenue(d);
                t.gross += r.gross; t.discounts += r.discounts; t.net += r.net;
                t.food += r.food; t.beverage += r.beverage; t.other += r.other; t.orders += r.orders;
                METHODS.forEach((m) => { t.m[m.key] += r.methods[m.key]; });
            });
            t.credit = t.m.credit;
            t.collected = t.net - t.credit;
            t.digital = sum(DIGITAL.map((k) => t.m[k]));
            return t;
        }

        function bar(label, value, total, color, right) {
            return `<div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-semibold text-gray-700">${label}</span>
                    <span class="text-sm font-bold text-gray-900">${right}</span>
                </div>
                <div class="prog-bar"><div class="prog-fill" style="width:${Math.round(pct(value, total))}%;background:${color};"></div></div>
            </div>`;
        }

        function buildPayments(to) {
            const r = Demo.rngFor('pay' + to);
            const day = Demo.dayRevenue(to);
            const weights = METHODS.map((m) => day.methods[m.key]);
            const wTotal = sum(weights);
            const pick = () => {
                let x = r() * wTotal;
                for (let i = 0; i < METHODS.length; i++) { x -= weights[i]; if (x <= 0) return METHODS[i]; }
                return METHODS[0];
            };
            let minutes = 21 * 60 + 30;
            const out = [];
            for (let i = 0; i < 14; i++) {
                const m = pick();
                const amount = Demo.r10(m.avg * (0.4 + r() * 1.6));
                let status = 'paid';
                if (m.key === 'credit') status = r() < 0.3 ? 'partial' : 'pending';
                minutes -= 6 + Math.floor(r() * 24);
                out.push({
                    id: `#MT-${9400 - i * 3 - Math.floor(r() * 3)}`,
                    table: `T-${pad(1 + Math.floor(r() * 14))}`,
                    method: m, amount, status,
                    time: `${pad(Math.floor(minutes / 60))}:${pad(minutes % 60)}`
                });
            }
            return out;
        }

        function renderPayments() {
            const f = $('payFilter').value;
            const rows = payments.filter((p) => f === 'all' || p.method.key === f);
            const badge = {
                paid: ['#dcfce7', '#15803d'], partial: ['#fef3c7', '#92400e'], pending: ['#fee2e2', '#dc2626']
            };
            $('payBody').innerHTML = rows.length ? rows.map((p) => `
                <tr class="tx-row border-b border-gray-50">
                    <td class="py-3 pr-3 font-semibold text-blue-600">${esc(p.id)}</td>
                    <td class="py-3 pr-3 font-bold text-gray-800">${esc(p.table)}</td>
                    <td class="py-3 pr-3">
                        <span class="inline-flex items-center gap-2 text-gray-600">
                            <span class="w-2.5 h-2.5 rounded-full" style="background:${p.method.color};"></span>${p.method.name}
                        </span>
                    </td>
                    <td class="py-3 pr-3 text-right font-bold text-gray-800 whitespace-nowrap">${money(p.amount)}</td>
                    <td class="py-3 pr-3 text-right text-gray-400">${p.time}</td>
                    <td class="py-3 text-right"><span class="badge" style="background:${badge[p.status][0]};color:${badge[p.status][1]};">${p.status}</span></td>
                </tr>`).join('') : `<tr><td colspan="6">${emptyState('No payments for this method.')}</td></tr>`;
        }

        function render(from, to) {
            const t = calc(from, to);
            const [pf, pt] = prevRange(from, to);
            const p = calc(pf, pt);
            const ranked = METHODS.map((m) => ({ m, v: t.m[m.key] })).sort((a, b) => b.v - a.v);

            $('kpiNet').textContent = money(t.net);
            $('kpiNetHint').innerHTML = deltaBadge(t.net, p.net);
            $('kpiCollected').textContent = money(t.collected);
            $('kpiCollectedHint').textContent = `${pct(t.collected, t.net).toFixed(1)}% of net sales`;
            $('kpiCredit').textContent = money(t.credit);
            $('kpiCreditHint').textContent = `${pct(t.credit, t.net).toFixed(1)}% still to collect`;
            $('kpiAvg').textContent = money(t.orders ? t.net / t.orders : 0);
            $('kpiOrders').textContent = t.orders.toLocaleString();
            $('kpiOrdersHint').textContent = `${(t.orders / t.days).toFixed(0)} per day`;
            $('kpiDisc').textContent = '− ' + money(t.discounts);
            $('kpiDiscHint').textContent = `${pct(t.discounts, t.gross).toFixed(1)}% of gross sales`;
            $('kpiDigital').textContent = money(t.digital);
            $('kpiTopMethod').textContent = ranked[0].m.name;
            $('kpiTopMethodHint').textContent = `${pct(ranked[0].v, t.net).toFixed(1)}% · ${money(ranked[0].v)}`;

            /* stacked chart */
            const buckets = bucketize(from, to);
            drawChart('methodChart', 'bar', {
                labels: buckets.map((b) => b.label),
                datasets: METHODS.map((m) => ({
                    label: m.name,
                    data: buckets.map((b) => sum(b.dates.map((d) => Demo.dayRevenue(d).methods[m.key]))),
                    backgroundColor: m.color, borderSkipped: false, stack: 's'
                }))
            }, {
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true } },
                    tooltip: { callbacks: { label: (c) => ` ${c.dataset.label}: ${money(c.raw)}` } }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, border: { display: false } },
                    y: { stacked: true, grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { callback: (v) => kFmt(v) } }
                }
            });

            /* doughnut */
            drawChart('mixChart', 'doughnut', {
                labels: METHODS.map((m) => m.name),
                datasets: [{ data: METHODS.map((m) => t.m[m.key]), backgroundColor: METHODS.map((m) => m.color), borderWidth: 0, hoverOffset: 6 }]
            }, { cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.label}: ${money(c.raw)}` } } } });
            $('mixLegend').innerHTML = METHODS.map((m) => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${m.color};"></div>
                        <span class="text-xs text-gray-600">${m.name}</span>
                    </div>
                    <span class="text-xs font-bold text-gray-800">${pct(t.m[m.key], t.net).toFixed(1)}%</span>
                </div>`).join('');

            /* method table */
            const txns = METHODS.map((m) => Math.max(0, Math.round(t.m[m.key] / m.avg)));
            $('methodBody').innerHTML = METHODS.map((m, i) => `
                <tr class="tx-row border-b border-gray-50">
                    <td class="py-3 pr-3">
                        <span class="inline-flex items-center gap-2 font-semibold text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full" style="background:${m.color};"></span>${m.name}
                        </span>
                    </td>
                    <td class="py-3 text-right text-gray-600">${txns[i].toLocaleString()}</td>
                    <td class="py-3 text-right font-bold text-gray-800 whitespace-nowrap">${money(t.m[m.key])}</td>
                    <td class="py-3 pl-6">
                        <div class="flex items-center gap-2">
                            <div class="prog-bar flex-1"><div class="prog-fill" style="width:${Math.round(pct(t.m[m.key], t.net))}%;background:${m.color};"></div></div>
                            <span class="text-xs text-gray-500 w-10 text-right">${pct(t.m[m.key], t.net).toFixed(0)}%</span>
                        </div>
                    </td>
                    <td class="py-3 text-right text-gray-500 whitespace-nowrap">${money(txns[i] ? t.m[m.key] / txns[i] : 0)}</td>
                </tr>`).join('') + `
                <tr class="font-bold">
                    <td class="pt-3">Total</td>
                    <td class="pt-3 text-right">${sum(txns).toLocaleString()}</td>
                    <td class="pt-3 text-right whitespace-nowrap">${money(t.net)}</td><td></td><td></td>
                </tr>`;

            /* sales mix */
            const grossTotal = t.gross;
            $('catMix').innerHTML = [
                bar('Food', t.food, grossTotal, '#2563eb', money(t.food)),
                bar('Beverages', t.beverage, grossTotal, '#0ea5e9', money(t.beverage)),
                bar('Other', t.other, grossTotal, '#a78bfa', money(t.other)),
            ].join('');

            const rr = Demo.rngFor('type' + from + to);
            const dine = 0.66 + rr() * 0.1, take = 0.16 + rr() * 0.06, deliv = Math.max(0.04, 1 - dine - take);
            const norm = dine + take + deliv;
            $('typeMix').innerHTML = [
                ['Dine-in', dine, '#16a34a'], ['Takeaway', take, '#f59e0b'], ['Delivery', deliv, '#6366f1']
            ].map(([n, w, c]) => bar(n, w, norm, c, `${money(t.net * w / norm)} · ${(w / norm * 100).toFixed(0)}%`)).join('');

            /* payments */
            payments = buildPayments(to);
            $('payHint').textContent = `Most recent transactions on ${to}`;
            renderPayments();

            window.__salesRows = METHODS.map((m, i) => [m.name, txns[i], t.m[m.key], pct(t.m[m.key], t.net).toFixed(1)]);
        }

        // Populate filter options once
        METHODS.forEach((m) => {
            const o = document.createElement('option');
            o.value = m.key; o.textContent = m.name;
            $('payFilter').appendChild(o);
        });
        $('payFilter').addEventListener('change', renderPayments);

        window.renderReport = render;

        $('exportBtn').addEventListener('click', () => {
            downloadCsv(`sales_payments_${window.__range.from}_${window.__range.to}.csv`,
                [['Method', 'Transactions', 'Amount', '% of net sales'], ...(window.__salesRows || [])]);
        });
    </script>
@endpush