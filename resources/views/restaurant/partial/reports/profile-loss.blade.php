@extends('restaurant.partial.reports.layout')

@section('title', 'Profit & Loss')
@section('heading', 'Profit & Loss')
@section('subheading', 'Income vs expenses and net profit')

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
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Gross Profit</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiGP">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiGPHint"></p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Expenses</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiExp">—</p>
            <div class="mt-2 flex items-center flex-wrap" id="kpiExpHint"></div>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Net Profit</p>
            <p class="text-2xl font-extrabold" id="kpiProfit">—</p>
            <div class="mt-2 flex items-center flex-wrap" id="kpiProfitHint"></div>
        </div>
    </div>

    <!-- KPI ROW 2 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card fade-up d3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Food Cost %</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiFood">—</p>
            <p class="text-xs text-gray-400 mt-1">Ingredients ÷ net sales · target 30–35%</p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Prime Cost %</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiPrime">—</p>
            <p class="text-xs text-gray-400 mt-1">Ingredients + wages · target ≤ 65%</p>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Break-even / Day</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiBE">—</p>
            <p class="text-xs text-gray-400 mt-1">Daily sales needed to cover costs</p>
        </div>
        <div class="stat-card fade-up d6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Profit / Order</p>
            <p class="text-xl font-extrabold text-gray-900" id="kpiPPO">—</p>
            <p class="text-xs text-gray-400 mt-1" id="kpiPPOHint"></p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d4">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Revenue vs Expenses</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Net sales, total expenses and profit</p>
                </div>
                <div class="hidden sm:flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm inline-block" style="background:#2563eb;"></span>Revenue</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm inline-block" style="background:#bfdbfe;"></span>Expenses</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-1 rounded-sm inline-block" style="background:#16a34a;"></span>Profit</span>
                </div>
            </div>
            <div style="height:260px;"><canvas id="trendChart"></canvas></div>
        </div>

        <div class="chart-card fade-up d5">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Where Each Rupee Goes</h3>
                <p class="text-xs text-gray-400 mt-0.5">Share of net sales</p>
            </div>
            <div style="height:170px;" class="mb-4"><canvas id="rupeeChart"></canvas></div>
            <div class="space-y-2" id="rupeeLegend"></div>
        </div>
    </div>

    <!-- STATEMENT + INSIGHTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d6">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Profit &amp; Loss Statement</h3>
                <p class="text-xs text-gray-400 mt-0.5">Percentages are relative to net sales</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Line item</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider w-20">% of sales</th>
                        </tr>
                    </thead>
                    <tbody id="stmtBody"></tbody>
                </table>
            </div>
        </div>

        <div class="chart-card fade-up d7">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">Quick Insights</h3>
                <p class="text-xs text-gray-400 mt-0.5">Auto-generated from this period</p>
            </div>
            <div class="space-y-4" id="insights"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CATS = Demo.CATS;

        function calc(from, to) {
            const dates = daysBetween(from, to);
            const t = { days: dates.length, gross: 0, discounts: 0, net: 0, food: 0, beverage: 0, other: 0, orders: 0, cats: {} };
            CATS.forEach((c) => { t.cats[c.key] = 0; });
            dates.forEach((d) => {
                const r = Demo.dayRevenue(d);
                t.gross += r.gross; t.discounts += r.discounts; t.net += r.net;
                t.food += r.food; t.beverage += r.beverage; t.other += r.other; t.orders += r.orders;
                Demo.dayExpenses(d).forEach((e) => { t.cats[e.catKey] += e.amount; });
            });
            t.cogs = t.cats.ingredients;
            t.gp = t.net - t.cogs;
            t.opex = sum(CATS.filter((c) => c.key !== 'ingredients').map((c) => t.cats[c.key]));
            t.totalExp = t.cogs + t.opex;
            t.profit = t.gp - t.opex;
            return t;
        }

        function stmtRow(r) {
            if (r.h) return `<tr><td colspan="3" class="pt-5 pb-1 text-xs font-bold uppercase tracking-wider text-gray-400">${r.h}</td></tr>`;
            const val = r.v < 0 ? '− ' + money(-r.v) : money(r.v);
            const bold = r.b || r.total;
            const bg = r.total ? (r.v >= 0 ? 'background:#f0fdf4;color:#15803d;' : 'background:#fef2f2;color:#dc2626;') : '';
            return `<tr class="${bold ? 'font-bold' : ''} ${bold ? 'border-t border-gray-100' : ''}" style="${bg}">
                <td class="py-2.5 pr-3 ${r.i ? 'pl-5 text-gray-600' : (r.total ? 'pl-3' : 'text-gray-800')}">${r.l}</td>
                <td class="py-2.5 text-right whitespace-nowrap">${val}</td>
                <td class="py-2.5 text-right whitespace-nowrap ${r.total ? 'pr-3' : 'text-gray-400'} w-20">${r.pct.toFixed(1)}%</td>
            </tr>`;
        }

        function render(from, to) {
            const t = calc(from, to);
            const [pf, pt] = prevRange(from, to);
            const p = calc(pf, pt);
            const margin = pct(t.profit, t.net);

            $('kpiNet').textContent = money(t.net);
            $('kpiNetHint').innerHTML = deltaBadge(t.net, p.net);
            $('kpiGP').textContent = money(t.gp);
            $('kpiGPHint').textContent = `${pct(t.gp, t.net).toFixed(1)}% gross margin`;
            $('kpiExp').textContent = money(t.totalExp);
            $('kpiExpHint').innerHTML = deltaBadge(t.totalExp, p.totalExp, true);
            $('kpiProfit').textContent = (t.profit < 0 ? '− ' : '') + money(Math.abs(t.profit));
            $('kpiProfit').style.color = t.profit >= 0 ? '#15803d' : '#dc2626';
            $('kpiProfitHint').innerHTML = `<span class="badge" style="background:#eff6ff;color:#1d4ed8;">${margin.toFixed(1)}% margin</span>` +
                (p.profit > 0 ? `<span class="ml-1.5">${deltaBadge(t.profit, p.profit)}</span>` : '');

            const fixedPerDay = t.opex / t.days;
            const gm = t.net ? t.gp / t.net : 0;
            $('kpiFood').textContent = pct(t.cogs, t.net).toFixed(1) + '%';
            $('kpiPrime').textContent = pct(t.cogs + t.cats.wages, t.net).toFixed(1) + '%';
            $('kpiBE').textContent = gm > 0 ? money(fixedPerDay / gm) : '—';
            $('kpiPPO').textContent = money(t.orders ? t.profit / t.orders : 0);
            $('kpiPPOHint').textContent = `${t.orders.toLocaleString()} orders`;

            /* statement */
            const rows = [
                { h: 'Revenue' },
                { l: 'Food sales', v: t.food, i: 1 },
                { l: 'Beverage sales', v: t.beverage, i: 1 },
                { l: 'Other income', v: t.other, i: 1 },
                { l: 'Gross sales', v: t.gross, b: 1 },
                { l: 'Less: discounts', v: -t.discounts, i: 1 },
                { l: 'Net sales', v: t.net, b: 1 },
                { h: 'Cost of goods sold' },
                { l: 'Ingredients', v: t.cogs, i: 1 },
                { l: 'Gross profit', v: t.gp, b: 1 },
                { h: 'Operating expenses' },
                ...CATS.filter((c) => c.key !== 'ingredients').map((c) => ({ l: c.short, v: t.cats[c.key], i: 1 })),
                { l: 'Total operating expenses', v: t.opex, b: 1 },
                { h: ' ' },
                { l: 'Net profit', v: t.profit, total: 1 },
            ].map((r) => ({ ...r, pct: r.h ? 0 : pct(Math.abs(r.v), t.net) }));
            $('stmtBody').innerHTML = rows.map(stmtRow).join('');
            window.__stmtRows = rows;

            /* trend chart */
            const buckets = bucketize(from, to);
            const rev = [], exp = [], prof = [];
            buckets.forEach((b) => {
                const r = sum(b.dates.map((d) => Demo.dayRevenue(d).net));
                const e = sum(b.dates.map((d) => sum(Demo.dayExpenses(d).map((x) => x.amount))));
                rev.push(r); exp.push(e); prof.push(r - e);
            });
            drawChart('trendChart', 'bar', {
                labels: buckets.map((b) => b.label),
                datasets: [
                    { label: 'Revenue', data: rev, backgroundColor: '#2563eb', borderRadius: 6, borderSkipped: false },
                    { label: 'Expenses', data: exp, backgroundColor: '#bfdbfe', borderRadius: 6, borderSkipped: false },
                    { type: 'line', label: 'Profit', data: prof, borderColor: '#16a34a', backgroundColor: '#16a34a', tension: 0.35, pointRadius: 3, borderWidth: 2 },
                ]
            }, {
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.dataset.label}: ${money(c.raw)}` } } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { callback: (v) => kFmt(v) } }
                }
            });

            /* rupee doughnut */
            const slices = CATS.map((c) => ({ name: c.short, value: t.cats[c.key], color: c.color }));
            if (t.profit > 0) slices.push({ name: 'Net profit', value: t.profit, color: '#16a34a' });
            drawChart('rupeeChart', 'doughnut', {
                labels: slices.map((s) => s.name),
                datasets: [{ data: slices.map((s) => s.value), backgroundColor: slices.map((s) => s.color), borderWidth: 0, hoverOffset: 6 }]
            }, { cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.label}: ${money(c.raw)}` } } } });
            $('rupeeLegend').innerHTML = slices.map((s) => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${s.color};"></div>
                        <span class="text-xs text-gray-600">${s.name}</span>
                    </div>
                    <span class="text-xs font-bold text-gray-800">${pct(s.value, t.net).toFixed(1)}%</span>
                </div>`).join('');

            /* insights */
            const perDay = daysBetween(from, to).map((d) => ({
                d, profit: Demo.dayRevenue(d).net - sum(Demo.dayExpenses(d).map((x) => x.amount))
            }));
            const best = perDay.reduce((a, b) => (b.profit > a.profit ? b : a));
            const worst = perDay.reduce((a, b) => (b.profit < a.profit ? b : a));
            const dayLabel = (d) => parseLocalDate(d).toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
            const topCost = CATS.map((c) => ({ c, v: t.cats[c.key] })).sort((a, b) => b.v - a.v)[0];
            const verdict = margin >= 25 ? ['Healthy margin', '#dcfce7', '▲', `Net margin of ${margin.toFixed(1)}% is strong for a restaurant.`]
                : margin >= 12 ? ['Okay margin', '#fef3c7', '●', `Net margin of ${margin.toFixed(1)}% is acceptable; watch food cost.`]
                : ['Thin margin', '#fee2e2', '▼', `Net margin of ${margin.toFixed(1)}% leaves little room for error.`];
            const item = (bg, icon, title, text) => `
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs flex-shrink-0" style="background:${bg};">${icon}</div>
                    <div><p class="text-sm font-semibold text-gray-800">${title}</p><p class="text-xs text-gray-500 mt-0.5">${text}</p></div>
                </div>`;
            $('insights').innerHTML = [
                item(verdict[1], verdict[2], verdict[0], verdict[3]),
                item('#dcfce7', '★', 'Best day', `${dayLabel(best.d)} · ${money(best.profit)} profit`),
                item('#fee2e2', '!', 'Weakest day', `${dayLabel(worst.d)} · ${worst.profit < 0 ? '− ' : ''}${money(Math.abs(worst.profit))} profit`),
                item('#eff6ff', '₨', 'Biggest cost', `${topCost.c.short} takes ${pct(topCost.v, t.net).toFixed(1)}% of net sales (${money(topCost.v)}).`),
            ].join('');
        }

        window.renderReport = render;

        $('exportBtn').addEventListener('click', () => {
            const rows = [['Line item', 'Amount', '% of net sales']];
            (window.__stmtRows || []).forEach((r) => { if (!r.h) rows.push([r.l, r.v, r.pct.toFixed(1)]); });
            downloadCsv(`profit_loss_${window.__range.from}_${window.__range.to}.csv`, rows);
        });
    </script>
@endpush