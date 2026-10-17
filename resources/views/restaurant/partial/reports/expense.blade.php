@extends('restaurant.partial.reports.layout')

@section('title', 'Expenses')
@section('heading', 'Expenses')
@section('subheading', 'Where your money is going')

@section('actions')
    <button id="addExpBtn" class="btn btn-blue">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add expense
    </button>
    <button id="exportBtn" class="btn btn-dark">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export CSV
    </button>
@endsection

@section('content')
    <!-- KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card fade-up d2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Expenses</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiTotal">—</p>
            <div class="mt-2 flex items-center flex-wrap" id="kpiTotalHint"></div>
        </div>
        <div class="stat-card fade-up d3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Daily Average</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiAvg">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiAvgHint"></p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Biggest Category</p>
            <p class="text-2xl font-extrabold text-gray-900 truncate" id="kpiTop">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiTopHint"></p>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Expense Ratio</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiRatio">—</p>
            <p class="text-xs text-gray-400 mt-2">Expenses ÷ net sales</p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d4">
            <div class="mb-5">
                <h3 class="font-bold text-gray-800 text-base">Expense Trend</h3>
                <p class="text-xs text-gray-400 mt-0.5">Stacked by category</p>
            </div>
            <div style="height:260px;"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="chart-card fade-up d5">
            <div class="mb-4">
                <h3 class="font-bold text-gray-800 text-base">By Category</h3>
                <p class="text-xs text-gray-400 mt-0.5">Share of total expenses</p>
            </div>
            <div style="height:170px;" class="mb-4"><canvas id="catChart"></canvas></div>
            <div class="space-y-2" id="catLegend"></div>
        </div>
    </div>

    <!-- BUDGET + LIST -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="chart-card fade-up d6">
            <div class="mb-5">
                <h3 class="font-bold text-gray-800 text-base">Budget vs Actual</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="budgetHint">Daily budget × days in range</p>
            </div>
            <div class="space-y-4" id="budgetList"></div>
        </div>

        <div class="chart-card lg:col-span-2 fade-up d7">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Expense Entries</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="entryHint">—</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="search" id="searchBox" class="input auto" placeholder="Search description or vendor…" style="min-width:200px;">
                    <select id="catFilter" class="input auto"><option value="all">All categories</option></select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</th>
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Paid via</th>
                            <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="expBody"></tbody>
                </table>
            </div>
            <div class="text-center mt-4"><button id="moreBtn" class="btn btn-ghost btn-sm hidden">Show more</button></div>
        </div>
    </div>

    <!-- ADD EXPENSE MODAL -->
    <div class="modal-back" id="addModal">
        <div class="modal">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Add expense</h3>
            <p class="text-sm text-gray-500 mb-5">Demo only. Entries live in this page and are cleared on refresh.</p>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
                    <input type="date" id="fDate" class="input">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Amount (Rs.)</label>
                    <input type="number" id="fAmount" class="input" min="1" step="1">
                </div>
            </div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
            <select id="fCat" class="input mb-3"></select>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Description</label>
            <input type="text" id="fDesc" class="input mb-3" placeholder="e.g. Gas cylinder refill">
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Paid to</label>
                    <input type="text" id="fVendor" class="input" placeholder="Vendor">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Paid via</label>
                    <select id="fMethod" class="input">
                        <option>Cash</option><option>eSewa</option><option>Bank transfer</option><option>Card</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button class="btn btn-ghost" data-close-modal>Cancel</button>
                <button class="btn btn-blue" id="saveExp">Save expense</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CATS = Demo.CATS;
        const manual = [];          // entries added through the modal (client-side only)
        let entries = [];           // everything in the current range
        let visible = 15;

        function entriesFor(from, to) {
            const generated = daysBetween(from, to).flatMap((d) => Demo.dayExpenses(d));
            const added = manual.filter((e) => e.date >= from && e.date <= to);
            return [...generated, ...added];
        }

        function render(from, to) {
            entries = entriesFor(from, to);
            const days = daysBetween(from, to).length;
            const total = sum(entries.map((e) => e.amount));
            const [pf, pt] = prevRange(from, to);
            const prevTotal = sum(entriesFor(pf, pt).map((e) => e.amount));
            const revenue = sum(daysBetween(from, to).map((d) => Demo.dayRevenue(d).net));

            const byCat = {};
            CATS.forEach((c) => { byCat[c.key] = 0; });
            entries.forEach((e) => { byCat[e.catKey] += e.amount; });
            const ranked = CATS.map((c) => ({ c, v: byCat[c.key] })).sort((a, b) => b.v - a.v);

            $('kpiTotal').textContent = money(total);
            $('kpiTotalHint').innerHTML = deltaBadge(total, prevTotal, true);
            $('kpiAvg').textContent = money(total / days);
            $('kpiAvgHint').textContent = `Over ${days} day${days !== 1 ? 's' : ''}`;
            $('kpiTop').textContent = ranked[0].c.short;
            $('kpiTopHint').textContent = `${pct(ranked[0].v, total).toFixed(1)}% · ${money(ranked[0].v)}`;
            $('kpiRatio').textContent = pct(total, revenue).toFixed(1) + '%';

            /* stacked trend */
            const buckets = bucketize(from, to);
            drawChart('trendChart', 'bar', {
                labels: buckets.map((b) => b.label),
                datasets: CATS.map((c) => ({
                    label: c.short,
                    data: buckets.map((b) => sum(b.dates.flatMap((d) => Demo.dayExpenses(d)).filter((e) => e.catKey === c.key).map((e) => e.amount))),
                    backgroundColor: c.color, borderSkipped: false, stack: 's'
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
            drawChart('catChart', 'doughnut', {
                labels: CATS.map((c) => c.short),
                datasets: [{ data: CATS.map((c) => byCat[c.key]), backgroundColor: CATS.map((c) => c.color), borderWidth: 0, hoverOffset: 6 }]
            }, { cutout: '68%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.label}: ${money(c.raw)}` } } } });
            $('catLegend').innerHTML = CATS.map((c) => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${c.color};"></div>
                        <span class="text-xs text-gray-600">${c.short}</span>
                    </div>
                    <span class="text-xs font-bold text-gray-800">${money(byCat[c.key])} <span class="text-gray-400 font-normal">(${pct(byCat[c.key], total).toFixed(0)}%)</span></span>
                </div>`).join('');

            /* budget vs actual */
            $('budgetHint').textContent = `Daily budget × ${days} day${days !== 1 ? 's' : ''}`;
            $('budgetList').innerHTML = CATS.map((c) => {
                const budget = c.daily * days;
                const actual = byCat[c.key];
                const ratio = pct(actual, budget);
                const over = ratio > 100;
                const color = over ? '#dc2626' : (ratio > 90 ? '#f59e0b' : c.color);
                return `<div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-semibold text-gray-700">${c.short}</span>
                        <span class="text-xs ${over ? 'text-red-500 font-bold' : 'text-gray-400'}">${money(actual)} / ${money(budget)}</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:${Math.min(100, Math.round(ratio))}%;background:${color};"></div></div>
                    ${over ? `<p class="text-xs text-red-500 mt-1">Over budget by ${(ratio - 100).toFixed(0)}%</p>` : ''}
                </div>`;
            }).join('');

            visible = 15;
            renderList();
        }

        function filteredEntries() {
            const q = $('searchBox').value.trim().toLowerCase();
            const cf = $('catFilter').value;
            return entries
                .filter((e) => cf === 'all' || e.catKey === cf)
                .filter((e) => !q || e.desc.toLowerCase().includes(q) || e.vendor.toLowerCase().includes(q))
                .sort((a, b) => b.date.localeCompare(a.date) || b.amount - a.amount);
        }

        function renderList() {
            const list = filteredEntries();
            const shown = list.slice(0, visible);
            $('entryHint').textContent = `${list.length} entr${list.length === 1 ? 'y' : 'ies'} · ${money(sum(list.map((e) => e.amount)))}`;
            $('expBody').innerHTML = shown.length ? shown.map((e) => {
                const c = Demo.catByKey(e.catKey);
                return `<tr class="tx-row border-b border-gray-50">
                    <td class="py-3 pr-3 text-gray-500 whitespace-nowrap">${parseLocalDate(e.date).toLocaleDateString([], { month: 'short', day: 'numeric' })}</td>
                    <td class="py-3 pr-3 whitespace-nowrap">
                        <span class="badge" style="background:${c.color}1a;color:${c.color};">${c.short}</span>
                    </td>
                    <td class="py-3 pr-3">
                        <p class="font-semibold text-gray-800">${esc(e.desc)}${e.added ? ' <span class="badge" style="background:#dbeafe;color:#1d4ed8;">New</span>' : ''}</p>
                        <p class="text-xs text-gray-400">${esc(e.vendor || '—')}</p>
                    </td>
                    <td class="py-3 pr-3 text-gray-500 whitespace-nowrap">${esc(e.method)}</td>
                    <td class="py-3 text-right font-bold text-gray-800 whitespace-nowrap">${money(e.amount)}</td>
                </tr>`;
            }).join('') : `<tr><td colspan="5">${emptyState('No expenses match your filters.')}</td></tr>`;
            $('moreBtn').classList.toggle('hidden', list.length <= visible);
        }

        /* filters */
        CATS.forEach((c) => {
            ['catFilter', 'fCat'].forEach((id) => {
                const o = document.createElement('option');
                o.value = c.key; o.textContent = c.short;
                $(id).appendChild(o);
            });
        });
        $('searchBox').addEventListener('input', () => { visible = 15; renderList(); });
        $('catFilter').addEventListener('change', () => { visible = 15; renderList(); });
        $('moreBtn').addEventListener('click', () => { visible += 15; renderList(); });

        /* add expense modal */
        $('addExpBtn').addEventListener('click', () => {
            $('fDate').value = window.__range.to;
            $('fDate').max = getTodayDate();
            ['fAmount', 'fDesc', 'fVendor'].forEach((id) => { $(id).value = ''; });
            openModal('addModal');
            $('fAmount').focus();
        });

        $('saveExp').addEventListener('click', () => {
            const amount = Math.round(+$('fAmount').value);
            const date = $('fDate').value;
            const desc = $('fDesc').value.trim();
            if (!(amount > 0) || !date || !desc) { toast('Please enter a date, description and amount'); return; }
            manual.push({
                date, catKey: $('fCat').value, desc, vendor: $('fVendor').value.trim(),
                method: $('fMethod').value, amount, added: true
            });
            closeModal('addModal');
            const inRange = date >= window.__range.from && date <= window.__range.to;
            toast(inRange ? `Added ${money(amount)} expense` : 'Added, but its date is outside the current range');
            render(window.__range.from, window.__range.to);
        });

        window.renderReport = render;

        $('exportBtn').addEventListener('click', () => {
            const rows = [['Date', 'Category', 'Description', 'Paid to', 'Paid via', 'Amount']];
            filteredEntries().forEach((e) => rows.push([e.date, Demo.catByKey(e.catKey).short, e.desc, e.vendor, e.method, e.amount]));
            downloadCsv(`expenses_${window.__range.from}_${window.__range.to}.csv`, rows);
        });
    </script>
@endpush