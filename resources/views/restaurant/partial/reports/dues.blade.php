@extends('restaurant.partial.reports.layout')

@section('title', 'Unpaid Bills & Dues')
@section('heading', 'Unpaid Bills & Dues')
@section('subheading', 'Outstanding customer balances')

@section('no_filter')
    Dues are a live snapshot <strong class="text-gray-700">as of today</strong>, so the date filter doesn't apply on this page.
@endsection

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
    <!-- KPIs -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card fade-up d2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Outstanding</p>
            <p class="text-2xl font-extrabold text-red-500" id="kpiTotal">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiTotalHint"></p>
        </div>
        <div class="stat-card fade-up d3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Customers With Dues</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiCust">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiCustHint"></p>
        </div>
        <div class="stat-card fade-up d4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Overdue (&gt; 7 days)</p>
            <p class="text-2xl font-extrabold text-orange-500" id="kpiOverdue">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiOverdueHint"></p>
        </div>
        <div class="stat-card fade-up d5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Oldest Unpaid Bill</p>
            <p class="text-2xl font-extrabold text-gray-900" id="kpiOldest">—</p>
            <p class="text-xs text-gray-400 mt-2" id="kpiOldestHint"></p>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="chart-card lg:col-span-2 fade-up d4">
            <div class="mb-5">
                <h3 class="font-bold text-gray-800 text-base">Ageing of Unpaid Bills</h3>
                <p class="text-xs text-gray-400 mt-0.5">How long money has been outstanding</p>
            </div>
            <div style="height:240px;"><canvas id="ageChart"></canvas></div>
        </div>
        <div class="chart-card fade-up d5">
            <div class="mb-5">
                <h3 class="font-bold text-gray-800 text-base">Top Debtors</h3>
                <p class="text-xs text-gray-400 mt-0.5">Largest balances due</p>
            </div>
            <div class="space-y-3" id="topDebtors"></div>
        </div>
    </div>

    <!-- CUSTOMER TABLE -->
    <div class="chart-card fade-up d6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Customer Dues</h3>
                <p class="text-xs text-gray-400 mt-0.5">Click a customer to see their unpaid bills</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <input type="search" id="searchBox" class="input auto" placeholder="Search name or phone…" style="min-width:210px;">
                <select id="statusFilter" class="input auto">
                    <option value="all">All statuses</option>
                    <option value="current">Current (≤ 7 days)</option>
                    <option value="overdue">Overdue (8–30 days)</option>
                    <option value="critical">Critical (&gt; 30 days)</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bills</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Billed</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Paid</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Balance due</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Oldest</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-right pb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="custBody"></tbody>
            </table>
        </div>
    </div>

    <!-- COLLECT MODAL -->
    <div class="modal-back" id="collectModal">
        <div class="modal">
            <h3 class="font-bold text-gray-900 text-lg mb-1">Record payment</h3>
            <p class="text-sm text-gray-500 mb-5" id="collectFor">—</p>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Amount received</label>
            <input type="number" id="collectAmount" class="input mb-4" min="1" step="1">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Payment method</label>
            <select id="collectMethod" class="input mb-2">
                <option>Cash</option><option>eSewa</option><option>Fonepay QR</option><option>Khalti</option><option>Card</option><option>Bank transfer</option>
            </select>
            <p class="text-xs text-gray-400 mb-5">The payment is applied to the oldest bills first.</p>
            <div class="flex justify-end gap-2">
                <button class="btn btn-ghost" data-close-modal>Cancel</button>
                <button class="btn btn-blue" id="collectConfirm">Confirm payment</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // [billNo, table, daysAgo, amount, paidSoFar]
        const RAW = [
            { name: 'Ramesh Adhikari', phone: '9841-234567', bills: [['#MT-8421', 'T-08', 3, 2400, 0], ['#MT-8390', 'T-03', 9, 1800, 500]] },
            { name: 'Sunita Karki', phone: '9803-112233', bills: [['#MT-8402', 'T-12', 5, 3600, 0]] },
            { name: 'Everest Trekkers Pvt. Ltd.', phone: '9851-098765', bills: [['#MT-8211', 'T-01', 34, 9800, 2000], ['#MT-8150', 'T-05', 52, 7400, 0], ['#MT-8355', 'T-07', 14, 5200, 0]] },
            { name: 'Binod Shrestha', phone: '9818-445566', bills: [['#MT-8433', 'T-04', 1, 1350, 0]] },
            { name: 'Annapurna Staff Canteen', phone: '9860-778899', bills: [['#MT-7988', 'T-09', 68, 6400, 1000], ['#MT-8210', 'T-02', 35, 4500, 0]] },
            { name: 'Prakash Thapa', phone: '9845-667788', bills: [['#MT-8310', 'T-06', 16, 2100, 0]] },
            { name: 'Anita Gurung', phone: '9808-990011', bills: [['#MT-8440', 'T-10', 2, 980, 0]] },
            { name: 'Kathmandu Cycle Club', phone: '9841-556677', bills: [['#MT-8280', 'T-11', 22, 8200, 3000], ['#MT-8366', 'T-08', 11, 3400, 0]] },
            { name: 'Dipesh Rai', phone: '9812-321654', bills: [['#MT-8118', 'T-03', 47, 2750, 0]] },
            { name: 'Mina Tamang', phone: '9823-987654', bills: [['#MT-8422', 'T-07', 4, 1900, 0]] },
            { name: 'Sajan Maharjan', phone: '9849-135792', bills: [['#MT-8392', 'T-05', 8, 2300, 300]] },
        ];

        let customers = RAW.map((c, i) => ({
            id: i + 1, name: c.name, phone: c.phone,
            bills: c.bills.map(([no, table, ago, amount, paid]) => ({ no, table, ago, amount, paid }))
        }));
        const expanded = new Set();
        let collectingId = null;

        const balanceOf = (c) => sum(c.bills.map((b) => b.amount - b.paid));
        const unpaid = (c) => c.bills.filter((b) => b.amount - b.paid > 0);
        const oldestOf = (c) => Math.max(0, ...unpaid(c).map((b) => b.ago));
        const statusOf = (age) => age > 30 ? 'critical' : (age > 7 ? 'overdue' : 'current');
        const STATUS = {
            current: ['#dcfce7', '#15803d', 'Current'],
            overdue: ['#fef3c7', '#92400e', 'Overdue'],
            critical: ['#fee2e2', '#dc2626', 'Critical'],
        };
        const initials = (n) => n.split(' ').filter((w) => /^[A-Za-z]/.test(w)).slice(0, 2).map((w) => w[0]).join('').toUpperCase();
        const dateAgo = (n) => { const d = new Date(); d.setDate(d.getDate() - n); return d.toLocaleDateString([], { month: 'short', day: 'numeric' }); };

        function renderKpis() {
            const active = customers.filter((c) => balanceOf(c) > 0);
            const total = sum(active.map(balanceOf));
            const allUnpaid = active.flatMap(unpaid);
            const overdue = sum(allUnpaid.filter((b) => b.ago > 7).map((b) => b.amount - b.paid));
            const oldest = Math.max(0, ...allUnpaid.map((b) => b.ago));
            const oldestBill = allUnpaid.find((b) => b.ago === oldest);

            $('kpiTotal').textContent = money(total);
            $('kpiTotalHint').textContent = `Across ${allUnpaid.length} unpaid bills`;
            $('kpiCust').textContent = active.length;
            $('kpiCustHint').textContent = `Avg. ${money(active.length ? total / active.length : 0)} per customer`;
            $('kpiOverdue').textContent = money(overdue);
            $('kpiOverdueHint').textContent = `${pct(overdue, total).toFixed(0)}% of total outstanding`;
            $('kpiOldest').textContent = oldest ? `${oldest} days` : '—';
            $('kpiOldestHint').textContent = oldestBill ? `${oldestBill.no} · ${dateAgo(oldest)}` : 'Nothing outstanding';

            /* ageing */
            const buckets = [
                { l: '0–7 days', lo: 0, hi: 7, c: '#16a34a' }, { l: '8–15 days', lo: 8, hi: 15, c: '#84cc16' },
                { l: '16–30 days', lo: 16, hi: 30, c: '#f59e0b' }, { l: '31–60 days', lo: 31, hi: 60, c: '#f97316' },
                { l: '60+ days', lo: 61, hi: 9999, c: '#dc2626' },
            ];
            const vals = buckets.map((b) => sum(allUnpaid.filter((x) => x.ago >= b.lo && x.ago <= b.hi).map((x) => x.amount - x.paid)));
            drawChart('ageChart', 'bar', {
                labels: buckets.map((b) => b.l),
                datasets: [{ data: vals, backgroundColor: buckets.map((b) => b.c), borderRadius: 8, borderSkipped: false }]
            }, {
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${money(c.raw)}` } } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { color: '#f1f5f9' }, border: { display: false }, beginAtZero: true, ticks: { callback: (v) => kFmt(v) } }
                }
            });

            /* top debtors */
            const top = [...active].sort((a, b) => balanceOf(b) - balanceOf(a)).slice(0, 5);
            const max = top.length ? balanceOf(top[0]) : 1;
            $('topDebtors').innerHTML = top.length ? top.map((c, i) => `
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-gray-300 w-4">${i + 1}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between mb-1 gap-2">
                            <span class="text-sm font-semibold text-gray-700 truncate">${esc(c.name)}</span>
                            <span class="text-sm font-bold text-gray-900 whitespace-nowrap">${money(balanceOf(c))}</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:${Math.round(balanceOf(c) / max * 100)}%;background:${PALETTE[i % PALETTE.length]};"></div></div>
                    </div>
                </div>`).join('') : emptyState('All dues are cleared 🎉');
        }

        function renderTable() {
            const q = $('searchBox').value.trim().toLowerCase();
            const sf = $('statusFilter').value;
            const list = customers
                .filter((c) => balanceOf(c) > 0)
                .filter((c) => !q || c.name.toLowerCase().includes(q) || c.phone.replace(/-/g, '').includes(q.replace(/-/g, '')))
                .filter((c) => sf === 'all' || statusOf(oldestOf(c)) === sf)
                .sort((a, b) => balanceOf(b) - balanceOf(a));

            if (!list.length) {
                $('custBody').innerHTML = `<tr><td colspan="8">${emptyState('No customers match your filters.')}</td></tr>`;
                return;
            }

            $('custBody').innerHTML = list.map((c) => {
                const age = oldestOf(c);
                const st = STATUS[statusOf(age)];
                const open = expanded.has(c.id);
                const billed = sum(c.bills.map((b) => b.amount));
                const paid = sum(c.bills.map((b) => b.paid));
                const main = `
                <tr class="tx-row border-b border-gray-50 cursor-pointer" data-toggle="${c.id}">
                    <td class="py-3 pr-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:${PALETTE[c.id % PALETTE.length]};">${esc(initials(c.name))}</div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate">${esc(c.name)} <span class="text-gray-300 text-xs">${open ? '▾' : '▸'}</span></p>
                                <p class="text-xs text-gray-400">${esc(c.phone)}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-right text-gray-600">${unpaid(c).length}</td>
                    <td class="py-3 text-right text-gray-500 whitespace-nowrap">${money(billed)}</td>
                    <td class="py-3 text-right text-gray-500 whitespace-nowrap">${money(paid)}</td>
                    <td class="py-3 text-right font-bold text-red-500 whitespace-nowrap">${money(balanceOf(c))}</td>
                    <td class="py-3 text-right text-gray-500 whitespace-nowrap">${age} days</td>
                    <td class="py-3 text-right"><span class="badge" style="background:${st[0]};color:${st[1]};">${st[2]}</span></td>
                    <td class="py-3 text-right whitespace-nowrap">
                        <button class="btn btn-ghost btn-sm" data-remind="${c.id}">Remind</button>
                        <button class="btn btn-blue btn-sm" data-collect="${c.id}">Collect</button>
                    </td>
                </tr>`;
                if (!open) return main;
                const detail = `
                <tr class="border-b border-gray-50">
                    <td colspan="8" class="pb-4 pt-1">
                        <div class="ml-12 bg-gray-50 rounded-xl p-3">
                            <table class="w-full text-xs">
                                <thead><tr class="text-gray-400 uppercase tracking-wider">
                                    <th class="text-left pb-2 font-semibold">Bill</th><th class="text-left pb-2 font-semibold">Table</th>
                                    <th class="text-left pb-2 font-semibold">Date</th><th class="text-right pb-2 font-semibold">Amount</th>
                                    <th class="text-right pb-2 font-semibold">Paid</th><th class="text-right pb-2 font-semibold">Balance</th>
                                    <th class="text-right pb-2 font-semibold">Age</th>
                                </tr></thead>
                                <tbody>
                                    ${unpaid(c).sort((a, b) => b.ago - a.ago).map((b) => `
                                    <tr class="border-t border-gray-100">
                                        <td class="py-2 font-semibold text-blue-600">${esc(b.no)}</td><td class="py-2 text-gray-600">${esc(b.table)}</td>
                                        <td class="py-2 text-gray-500">${dateAgo(b.ago)}</td>
                                        <td class="py-2 text-right text-gray-600">${money(b.amount)}</td>
                                        <td class="py-2 text-right text-gray-500">${money(b.paid)}</td>
                                        <td class="py-2 text-right font-bold text-red-500">${money(b.amount - b.paid)}</td>
                                        <td class="py-2 text-right text-gray-500">${b.ago}d</td>
                                    </tr>`).join('')}
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>`;
                return main + detail;
            }).join('');
        }

        function renderAll() { renderKpis(); renderTable(); }

        /* table interactions (event delegation) */
        $('custBody').addEventListener('click', (e) => {
            const remind = e.target.closest('[data-remind]');
            const collect = e.target.closest('[data-collect]');
            if (remind) {
                const c = customers.find((x) => x.id === +remind.dataset.remind);
                toast(`Reminder sent to ${c.name} (demo)`);
                return;
            }
            if (collect) {
                const c = customers.find((x) => x.id === +collect.dataset.collect);
                collectingId = c.id;
                $('collectFor').textContent = `${c.name} · balance ${money(balanceOf(c))}`;
                $('collectAmount').value = balanceOf(c);
                $('collectAmount').max = balanceOf(c);
                openModal('collectModal');
                return;
            }
            const row = e.target.closest('[data-toggle]');
            if (row) {
                const id = +row.dataset.toggle;
                expanded.has(id) ? expanded.delete(id) : expanded.add(id);
                renderTable();
            }
        });

        $('collectConfirm').addEventListener('click', () => {
            const c = customers.find((x) => x.id === collectingId);
            let amount = Math.floor(+$('collectAmount').value);
            if (!c || !(amount > 0)) { toast('Enter a valid amount'); return; }
            amount = Math.min(amount, balanceOf(c));
            let left = amount;
            [...c.bills].sort((a, b) => b.ago - a.ago).forEach((b) => {   // oldest first
                const due = b.amount - b.paid;
                const pay = Math.min(due, left);
                b.paid += pay; left -= pay;
            });
            closeModal('collectModal');
            toast(`${money(amount)} received from ${c.name} via ${$('collectMethod').value}`);
            renderAll();
        });

        $('searchBox').addEventListener('input', renderTable);
        $('statusFilter').addEventListener('change', renderTable);

        $('exportBtn').addEventListener('click', () => {
            const rows = [['Customer', 'Phone', 'Unpaid bills', 'Billed', 'Paid', 'Balance due', 'Oldest (days)']];
            customers.filter((c) => balanceOf(c) > 0).forEach((c) => rows.push([
                c.name, c.phone, unpaid(c).length, sum(c.bills.map((b) => b.amount)), sum(c.bills.map((b) => b.paid)), balanceOf(c), oldestOf(c)
            ]));
            downloadCsv(`customer_dues_${getTodayDate()}.csv`, rows);
        });

        window.renderReport = renderAll;
    </script>
@endpush