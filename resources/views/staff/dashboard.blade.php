<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Take Order · Chai Pasal</title>
    <!-- Tailwind Play CDN: fine for a prototype. For production, install Tailwind and build your CSS. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] } } } };
    </script>
</head>

<body class="bg-slate-100 font-sans text-slate-900 antialiased">

    <!-- Top bar -->
    <header class="flex flex-wrap items-center gap-x-8 gap-y-3 bg-[#131a2b] px-4 py-3 text-white sm:px-8">
        <div class="flex items-center gap-4">
            <div class="rounded-lg bg-white px-2.5 py-2 text-[11px] font-extrabold tracking-widest text-[#131a2b]">
                MEROTABLE</div>
            <div class="h-7 w-px bg-slate-600"></div>
            <div class="text-lg font-semibold">Chai Pasal</div>
        </div>
        <nav class="mx-auto flex gap-7 text-[15px] font-semibold">
            <a href="#" class="border-b-2 border-white py-1.5 text-white">Dashboard</a>
            <a href="#" class="py-1.5 text-slate-300 hover:text-white">Billing</a>
        </nav>
        <div class="flex items-center gap-7">
            <div class="text-right leading-tight">
                <div class="text-[10px] font-semibold tracking-widest text-slate-300">LOGGED IN AS</div>
                <div class="text-sm font-bold">Laxmi Tamang</div>
            </div>
            <button class="min-h-11 px-1 text-sm font-semibold text-red-300 hover:text-red-200">Logout</button>
        </div>
    </header>

    <main class="mx-auto flex w-full max-w-[1440px] flex-col gap-5 px-4 pb-12 pt-7 sm:px-8">

        <!-- Title row -->
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Take Order</h1>
                <p class="mt-1 text-sm text-slate-500">Choose how it is served, pick items and sizes, send to the
                    kitchen.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button id="resumeBtn" data-action="resume"
                    class="hidden min-h-11 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold hover:bg-slate-50"></button>
                <div id="typeSwitch" role="group" aria-label="Order type"
                    class="flex gap-1 rounded-2xl border border-slate-200 bg-white p-1"></div>
            </div>
        </div>

        <!-- Toast -->
        <div id="toast"></div>

        <div class="flex flex-col items-start gap-6 lg:flex-row">

            <!-- LEFT -->
            <div class="flex w-full min-w-0 flex-1 flex-col gap-5">
                <section id="leftTop" class="rounded-2xl border border-slate-200 bg-white p-5"></section>

                <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-wrap gap-3">
                        <div class="relative flex min-w-[240px] flex-1 items-center">
                            <svg class="pointer-events-none absolute left-3.5 h-[18px] w-[18px] text-slate-500"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-3.5-3.5" />
                            </svg>
                            <input id="search" type="text" aria-label="Search menu items"
                                placeholder="Search menu items..."
                                class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-3.5 text-[15px] placeholder:text-slate-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30">
                        </div>
                        <button id="vegBtn" data-action="veg" aria-pressed="false"
                            class="flex min-h-12 items-center gap-2.5 rounded-xl border px-4 text-sm font-bold"></button>
                    </div>
                    <div id="chips" role="group" aria-label="Categories" class="flex gap-2 overflow-x-auto pb-1"></div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-extrabold">Menu items</h2>
                        <span id="resultText" class="text-[13px] font-semibold text-slate-500"></span>
                    </div>
                    <div id="grid" class="grid grid-cols-[repeat(auto-fill,minmax(210px,1fr))] gap-3.5"></div>
                </section>
            </div>

            <!-- RIGHT: current order -->
            <aside
                class="flex w-full flex-col rounded-2xl border border-slate-200 bg-white lg:sticky lg:top-4 lg:w-[400px] lg:flex-none">
                <div class="flex flex-col gap-2 border-b border-slate-100 px-5 pb-3.5 pt-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-[19px] font-extrabold">Current order</h2>
                            <span id="count"
                                class="flex h-6 min-w-6 items-center justify-center rounded-full bg-blue-600 px-1.5 text-xs font-extrabold text-white">0</span>
                        </div>
                        <button id="clearBtn" data-action="clear"
                            class="hidden min-h-11 px-1 text-sm font-bold text-red-600 hover:text-red-700">Clear
                            all</button>
                    </div>
                    <div id="summary" class="text-[13px] font-semibold text-slate-500"></div>
                </div>

                <div id="lines" class="max-h-[380px] overflow-y-auto px-5"></div>

                <div class="flex flex-col gap-3.5 border-t border-slate-100 px-5 py-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="orderNote" class="text-[13px] font-bold text-slate-700">Kitchen note for the whole
                            order</label>
                        <textarea id="orderNote" rows="2" placeholder="Allergies, serve together, birthday..."
                            class="resize-y rounded-[10px] border border-slate-300 px-3 py-2.5 text-sm placeholder:text-slate-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30"></textarea>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-bold text-slate-700">Discount</span>
                        <div class="flex items-center gap-2">
                            <div class="flex overflow-hidden rounded-[10px] border border-slate-300">
                                <button id="discPct" data-action="disc-mode" data-v="pct"
                                    class="h-11 min-w-11 text-sm font-extrabold">%</button>
                                <button id="discAmt" data-action="disc-mode" data-v="amt"
                                    class="h-11 min-w-[52px] text-sm font-extrabold">Rs.</button>
                            </div>
                            <input id="disc" type="number" min="0" aria-label="Discount value" placeholder="0"
                                class="h-11 w-[84px] rounded-[10px] border border-slate-300 px-2.5 text-right text-[15px] font-bold focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30">
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-x-5">
                        <label class="flex min-h-11 items-center gap-2 text-sm font-semibold text-slate-700"><input
                                id="svc" type="checkbox" checked class="h-5 w-5 accent-blue-600"> Service charge
                            10%</label>
                        <label class="flex min-h-11 items-center gap-2 text-sm font-semibold text-slate-700"><input
                                id="vat" type="checkbox" checked class="h-5 w-5 accent-blue-600"> VAT 13%</label>
                    </div>

                    <div id="totals" class="flex flex-col gap-2 border-t border-slate-100 pt-3.5 text-sm"></div>
                    <div id="hint"></div>

                    <div class="flex gap-2.5">
                        <button id="holdBtn" data-action="hold"
                            class="flex min-h-[52px] items-center gap-2 rounded-xl border-[1.5px] border-slate-300 bg-white px-[18px] text-[15px] font-bold hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500 disabled:hover:bg-slate-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <path d="M8 5v14M16 5v14" />
                            </svg>Hold
                        </button>
                        <button id="sendBtn" data-action="send"
                            class="flex min-h-[52px] flex-1 items-center justify-center gap-2.5 rounded-xl bg-blue-600 text-[15px] font-extrabold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500 disabled:hover:bg-slate-200">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 12 20 4l-4 16-4-7-8-1z" />
                            </svg>Send to kitchen
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <script>
        /* ============================== SAMPLE DATA ============================== */
        const CATEGORIES = ['All Items', 'Desserts', 'Drinks', 'Main Course', 'Momo', 'Rice & Noodles', 'Snacks', 'Starters'];

        // price = the "Full" / "Medium" price (or the only price when the item has no sizes)
        // sizes: 'hf' = Half/Full, 'sml' = Small/Medium/Large, omitted = no size choice
        const MENU = [
            { id: 'bm', name: 'Buff Momo', cat: 'Momo', price: 170, veg: false, pop: true, sizes: 'hf' },
            { id: 'cm', name: 'Chicken Momo', cat: 'Momo', price: 180, veg: false, sizes: 'hf' },
            { id: 'vm', name: 'Veg Momo', cat: 'Momo', price: 150, veg: true, sizes: 'hf' },
            { id: 'jm', name: 'Jhol Momo', cat: 'Momo', price: 210, veg: false, sizes: 'hf' },
            { id: 'cc', name: 'Chicken Chowmein', cat: 'Rice & Noodles', price: 220, veg: false, pop: true, sizes: 'hf' },
            { id: 'vc', name: 'Veg Chowmein', cat: 'Rice & Noodles', price: 180, veg: true, sizes: 'hf' },
            { id: 'cfr', name: 'Chicken Fried Rice', cat: 'Rice & Noodles', price: 240, veg: false, sizes: 'hf' },
            { id: 'efr', name: 'Egg Fried Rice', cat: 'Rice & Noodles', price: 200, veg: false, sizes: 'hf' },
            { id: 'ccu', name: 'Chicken Curry', cat: 'Main Course', price: 350, veg: false, sizes: 'hf' },
            { id: 'db', name: 'Dal Bhat Set', cat: 'Main Course', price: 300, veg: true },
            { id: 'pbm', name: 'Paneer Butter Masala', cat: 'Main Course', price: 380, veg: true, sizes: 'hf' },
            { id: 'cs', name: 'Chicken Sekuwa', cat: 'Starters', price: 330, veg: false, sizes: 'hf' },
            { id: 'cch', name: 'Chicken Choila', cat: 'Starters', price: 290, veg: false, out: true },
            { id: 'cor', name: 'Crispy Corn', cat: 'Starters', price: 220, veg: true, sizes: 'hf' },
            { id: 'ff', name: 'French Fries', cat: 'Snacks', price: 160, veg: true, sizes: 'sml' },
            { id: 'sc', name: 'Samosa Chaat', cat: 'Snacks', price: 90, veg: true },
            { id: 'cw', name: 'Chicken Sandwich', cat: 'Snacks', price: 200, veg: false },
            { id: 'vp', name: 'Veg Pakoda', cat: 'Snacks', price: 120, veg: true, sizes: 'hf' },
            { id: 'mc', name: 'Masala Chai', cat: 'Drinks', price: 40, veg: true, pop: true, sizes: 'sml' },
            { id: 'mt', name: 'Milk Tea', cat: 'Drinks', price: 35, veg: true, sizes: 'sml' },
            { id: 'bc', name: 'Black Coffee', cat: 'Drinks', price: 70, veg: true, sizes: 'sml' },
            { id: 'sl', name: 'Sweet Lassi', cat: 'Drinks', price: 120, veg: true, sizes: 'sml' },
            { id: 'gj', name: 'Gulab Jamun', cat: 'Desserts', price: 90, veg: true },
            { id: 'kh', name: 'Kheer', cat: 'Desserts', price: 100, veg: true },
            { id: 'rm', name: 'Rasmalai', cat: 'Desserts', price: 130, veg: true }
        ];

        const TABLES = [
            { id: 'T1', seats: 2 }, { id: 'T2', seats: 2 }, { id: 'T3', seats: 4 }, { id: 'T4', seats: 4 },
            { id: 'T5', seats: 4, reserved: true }, { id: 'T6', seats: 6 }, { id: 'T7', seats: 6 }, { id: 'T8', seats: 8 }
        ];

        const SERVICE_RATE = 0.10, VAT_RATE = 0.13;

        /* ================================ STATE ================================== */
        const state = {
            type: 'dine',            // 'dine' | 'take' | 'delivery'
            table: null, guests: 2,
            cat: 'All Items', q: '', vegOnly: false,
            pick: {},                // selected size index per menu item id
            cart: [],                // { key, id, name, size, price, qty, note }
            noteOpen: null, discMode: 'pct',
            cust: { name: '', phone: '', addr: '' },
            held: [], toast: '',
            // Sent orders per table (running bill). Sample data for T2 and T6.
            orders: {
                T2: [{
                    n: 1, time: '12:40', status: 'Preparing', note: '', lines: [
                        { name: 'Buff Momo', size: 'Full', qty: 2, price: 170, note: '' },
                        { name: 'Masala Chai', size: 'Medium', qty: 2, price: 40, note: 'Less sugar' }]
                }],
                T6: [
                    {
                        n: 1, time: '12:15', status: 'Served', note: '', lines: [
                            { name: 'Chicken Chowmein', size: 'Full', qty: 1, price: 220, note: '' },
                            { name: 'Sweet Lassi', size: 'Medium', qty: 2, price: 120, note: '' }]
                    },
                    {
                        n: 2, time: '12:48', status: 'New', note: 'Serve with extra plates', lines: [
                            { name: 'French Fries', size: 'Large', qty: 1, price: 210, note: '' }]
                    }
                ]
            }
        };

        /* =============================== HELPERS ================================= */
        const $ = (id) => document.getElementById(id);
        const fmt = (n) => 'Rs. ' + Math.round(n).toLocaleString('en-IN');
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const r5 = (n) => Math.round(n / 5) * 5;

        function sizeList(m) {
            if (m.out) return null;
            if (m.sizes === 'hf') return [{ label: 'Half', price: r5(m.price * 0.6) }, { label: 'Full', price: m.price }];
            if (m.sizes === 'sml') return [{ label: 'Small', price: r5(m.price * 0.8) }, { label: 'Medium', price: m.price }, { label: 'Large', price: r5(m.price * 1.3) }];
            return null;
        }
        const selectedSize = (m) => { const l = sizeList(m); return l ? l[state.pick[m.id] ?? 1] : null; };
        const tableOrders = (id) => state.orders[id] || [];
        const isDine = () => state.type === 'dine';

        function calc() {
            const sub = state.cart.reduce((a, l) => a + l.price * l.qty, 0);
            const count = state.cart.reduce((a, l) => a + l.qty, 0);
            const dv = Math.max(0, Number($('disc').value || 0));
            const disc = Math.round(state.discMode === 'pct' ? sub * Math.min(dv, 100) / 100 : Math.min(dv, sub));
            const svc = $('svc').checked ? Math.round((sub - disc) * SERVICE_RATE) : 0;
            const vat = $('vat').checked ? Math.round((sub - disc + svc) * VAT_RATE) : 0;
            return { sub, count, disc, svc, vat, total: sub - disc + svc + vat };
        }

        function validation() {
            if (!state.cart.length) return 'Add at least one item to place the order.';
            if (isDine() && !state.table) return 'Choose a table to send this order.';
            if (state.type === 'delivery' && (!state.cust.phone.trim() || !state.cust.addr.trim())) return 'Delivery needs a phone number and an address.';
            return '';
        }

        function flash(msg) {
            state.toast = msg; renderToast();
            clearTimeout(flash.t);
            flash.t = setTimeout(() => { state.toast = ''; renderToast(); }, 5000);
        }

        /* =============================== RENDERERS =============================== */
        const STATUS_CLS = {
            New: 'bg-blue-50 text-blue-800',
            Preparing: 'bg-amber-100 text-amber-800',
            Served: 'bg-emerald-50 text-emerald-800'
        };

        function renderType() {
            const defs = [['dine', 'Dine-in'], ['take', 'Takeaway'], ['delivery', 'Delivery']];
            $('typeSwitch').innerHTML = defs.map(([v, label]) => {
                const on = state.type === v;
                return `<button data-action="type" data-v="${v}" aria-pressed="${on}" class="min-h-11 rounded-[10px] px-5 text-sm font-bold ${on ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100'}">${label}</button>`;
            }).join('');
        }

        function renderResume() {
            const b = $('resumeBtn');
            b.classList.toggle('hidden', !state.held.length);
            b.classList.toggle('flex', !!state.held.length);
            b.innerHTML = `<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M8 5v14M16 5v14"/></svg>Resume held (${state.held.length})`;
        }

        function renderLeftTop() {
            const el = $('leftTop');
            if (!isDine()) {
                const inp = 'h-12 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-[15px] placeholder:text-slate-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30';
                const lbl = 'text-[13px] font-bold text-slate-700';
                el.className = 'flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5';
                el.innerHTML = `
      <h2 class="text-base font-extrabold">Customer</h2>
      <div class="flex flex-wrap gap-4">
        <div class="flex min-w-[220px] flex-1 flex-col gap-1.5"><label for="cName" class="${lbl}">Name</label>
          <input id="cName" data-field="name" type="text" placeholder="Customer name" value="${esc(state.cust.name)}" class="${inp}"></div>
        <div class="flex min-w-[220px] flex-1 flex-col gap-1.5"><label for="cPhone" class="${lbl}">Phone</label>
          <input id="cPhone" data-field="phone" type="tel" placeholder="98XXXXXXXX" value="${esc(state.cust.phone)}" class="${inp}"></div>
      </div>
      ${state.type === 'delivery' ? `<div class="flex flex-col gap-1.5"><label for="cAddr" class="${lbl}">Delivery address</label>
        <input id="cAddr" data-field="addr" type="text" placeholder="Street, landmark, ward" value="${esc(state.cust.addr)}" class="${inp}"></div>` : ''}`;
                return;
            }

            const seatsOf = Object.fromEntries(TABLES.map((t) => [t.id, t.seats]));
            const over = state.table && state.guests > seatsOf[state.table];
            const tableBtns = TABLES.map((t) => {
                const n = tableOrders(t.id).length, busy = n > 0, on = state.table === t.id;
                let cls = 'border-slate-300 bg-white text-slate-900 hover:border-blue-600', sub = `${t.seats} seats`;
                if (busy) { cls = on ? 'border-red-600 bg-red-600 text-white' : 'border-red-200 bg-red-50 text-red-800 hover:border-red-400'; sub = `${n} ${n === 1 ? 'order' : 'orders'}`; }
                else if (t.reserved) { cls = 'cursor-not-allowed border-amber-200 bg-amber-50 text-amber-800'; sub = 'Reserved'; }
                else if (on) cls = 'border-blue-600 bg-blue-600 text-white';
                return `<button data-action="table" data-v="${t.id}" aria-pressed="${on}" ${t.reserved ? 'disabled' : ''} class="flex min-h-16 flex-col items-center justify-center gap-0.5 rounded-xl border-[1.5px] ${cls}">
      <span class="text-[17px] font-extrabold">${t.id}</span><span class="text-xs font-semibold">${sub}</span></button>`;
            }).join('');

            const rounds = tableOrders(state.table);
            let panel = '';
            if (state.table && rounds.length) {
                let tableTotal = 0;
                const roundsHtml = rounds.map((o, i) => {
                    let rt = 0;
                    const lines = o.lines.map((l) => {
                        rt += l.price * l.qty;
                        return `<div class="flex items-start justify-between gap-3 text-sm">
          <div class="min-w-0"><div class="font-bold">${l.qty} × ${esc(l.name)}
            ${l.size ? `<span class="ml-1 rounded-md bg-slate-100 px-[7px] py-0.5 text-xs font-bold text-slate-700">${esc(l.size)}</span>` : ''}</div>
            ${l.note ? `<div class="mt-0.5 text-xs font-semibold text-amber-800">Note: ${esc(l.note)}</div>` : ''}</div>
          <div class="font-bold">${fmt(l.price * l.qty)}</div></div>`;
                    }).join('');
                    tableTotal += rt;
                    return `<div class="flex flex-col gap-2.5 rounded-xl border border-slate-200 bg-white p-3.5">
        <div class="flex flex-wrap items-center justify-between gap-2.5">
          <div class="flex items-center gap-2.5"><span class="text-[15px] font-extrabold">Round ${o.n}</span>
            <span class="text-[13px] font-semibold text-slate-500">${esc(o.time)}</span>
            <span class="rounded-full px-2.5 py-[3px] text-xs font-extrabold ${STATUS_CLS[o.status]}">${o.status}</span></div>
          ${o.status !== 'Served' ? `<button data-action="serve" data-i="${i}" class="min-h-11 rounded-[10px] border border-slate-300 bg-white px-3.5 text-[13px] font-bold hover:bg-slate-50">Mark served</button>` : ''}
        </div>
        ${lines}
        ${o.note ? `<div class="rounded-lg bg-amber-50 px-2.5 py-2 text-[13px] font-semibold text-amber-800">Kitchen note: ${esc(o.note)}</div>` : ''}
        <div class="flex justify-between border-t border-slate-100 pt-2 text-sm font-extrabold"><span>Round total</span><span>${fmt(rt)}</span></div>
      </div>`;
                }).join('');
                panel = `<div class="flex flex-col gap-3.5 rounded-2xl border-[1.5px] border-red-200 bg-red-50/40 p-4">
      <div class="flex flex-wrap items-center justify-between gap-2.5">
        <div><h3 class="text-base font-extrabold">Table ${esc(state.table)} · running orders</h3>
          <div class="mt-0.5 text-[13px] font-semibold text-slate-500">${rounds.length} ${rounds.length === 1 ? 'round' : 'rounds'} · ${fmt(tableTotal)} before tax</div></div>
        <button data-action="close-table" class="min-h-11 rounded-xl border-[1.5px] border-red-600 bg-white px-4 text-sm font-bold text-red-800 hover:bg-red-50">Close table (paid)</button>
      </div>${roundsHtml}</div>`;
            }

            el.className = 'flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5';
            el.innerHTML = `
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap items-center gap-4">
        <h2 class="text-base font-extrabold">Table</h2>
        <div class="flex flex-wrap gap-3.5 text-xs font-semibold text-slate-500">
          <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-700"></span>Free</span>
          <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-red-700"></span>Occupied</span>
          <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-600"></span>Reserved</span>
        </div>
      </div>
      <div class="flex items-center gap-2.5">
        <span class="text-[13px] font-bold text-slate-700">Guests</span>
        <div class="flex items-center overflow-hidden rounded-xl border border-slate-300">
          <button data-action="guest" data-v="-1" aria-label="Fewer guests" class="flex h-11 w-11 items-center justify-center bg-white hover:bg-slate-50"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg></button>
          <div class="min-w-9 text-center text-base font-extrabold">${state.guests}</div>
          <button data-action="guest" data-v="1" aria-label="More guests" class="flex h-11 w-11 items-center justify-center bg-white hover:bg-slate-50"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></button>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-[repeat(auto-fill,minmax(104px,1fr))] gap-2.5">${tableBtns}</div>
    ${over ? `<div role="status" class="rounded-[10px] bg-amber-50 px-3 py-2.5 text-[13px] font-semibold text-amber-800">${esc(state.table)} seats ${seatsOf[state.table]}, but you entered ${state.guests} guests. Pick a bigger table or join tables.</div>` : ''}
    <div class="text-[13px] font-semibold text-slate-500">Tap an occupied table to see what it has ordered. New items you send will be added to its running bill.</div>
    ${panel}`;
        }

        function filteredMenu() {
            const q = state.q.trim().toLowerCase();
            return MENU.filter((m) => (!state.vegOnly || m.veg) && (state.cat === 'All Items' || m.cat === state.cat) && (!q || m.name.toLowerCase().includes(q)));
        }

        function renderChips() {
            const base = MENU.filter((m) => !state.vegOnly || m.veg);
            $('chips').innerHTML = CATEGORIES.map((c) => {
                const on = state.cat === c;
                const n = c === 'All Items' ? base.length : base.filter((m) => m.cat === c).length;
                return `<button data-action="cat" data-v="${esc(c)}" aria-pressed="${on}" class="flex min-h-11 flex-none items-center gap-2 rounded-xl border px-4 text-sm font-bold ${on ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'}">
      ${esc(c)}<span class="rounded-full px-[7px] py-0.5 text-xs font-bold ${on ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-700'}">${n}</span></button>`;
            }).join('');
            const v = $('vegBtn');
            v.setAttribute('aria-pressed', state.vegOnly);
            v.className = 'flex min-h-12 items-center gap-2.5 rounded-xl border px-4 text-sm font-bold ' + (state.vegOnly ? 'border-emerald-700 bg-emerald-50 text-emerald-900' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50');
            v.innerHTML = `<span class="flex h-3.5 w-3.5 items-center justify-center rounded-[3px] border-[1.5px] border-emerald-700 bg-white"><span class="h-1.5 w-1.5 rounded-full bg-emerald-700"></span></span>Veg only`;
        }

        function renderGrid() {
            const items = filteredMenu();
            $('resultText').textContent = `${items.length} ${items.length === 1 ? 'item' : 'items'}`;
            if (!items.length) {
                $('grid').innerHTML = `<div class="col-span-full py-10 text-center text-[15px] text-slate-500">No items match. Try a different word or category.</div>`;
                return;
            }
            $('grid').innerHTML = items.map((m) => {
                const sizes = sizeList(m), size = selectedSize(m);
                const price = size ? size.price : m.price, label = size ? size.label : '';
                const key = m.id + '|' + label;
                const line = state.cart.find((l) => l.key === key);
                const qty = line ? line.qty : 0;
                const inAny = state.cart.some((l) => l.id === m.id);
                const vegC = m.veg ? 'border-emerald-700' : 'border-red-700', vegD = m.veg ? 'bg-emerald-700' : 'bg-red-700';
                const sizePills = sizes ? `<div role="group" aria-label="Choose size" class="flex gap-1.5">${sizes.map((z, i) => {
                    const on = i === (state.pick[m.id] ?? 1);
                    return `<button data-action="size" data-id="${m.id}" data-i="${i}" aria-pressed="${on}" class="flex min-h-12 flex-1 flex-col items-center justify-center gap-px rounded-[10px] border-[1.5px] px-0.5 py-1 ${on ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-600'}">
        <span class="text-[13px] font-extrabold">${z.label}</span><span class="text-[11px] font-semibold">${fmt(z.price)}</span></button>`;
                }).join('')}</div>` : '';
                let action;
                if (m.out) action = `<button disabled class="min-h-11 cursor-not-allowed rounded-xl bg-slate-100 text-sm font-bold text-slate-500">Unavailable</button>`;
                else if (qty > 0) action = `<div class="flex items-center justify-between rounded-xl bg-blue-600/10">
      <button data-action="dec" data-key="${m.id}|${label}" aria-label="Remove one" class="flex h-11 w-11 items-center justify-center text-blue-600"><svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg></button>
      <span class="text-base font-extrabold">${qty}</span>
      <button data-action="add" data-id="${m.id}" aria-label="Add one more" class="flex h-11 w-11 items-center justify-center text-blue-600"><svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></button></div>`;
                else action = `<button data-action="add" data-id="${m.id}" class="flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 text-sm font-bold text-white hover:bg-blue-700">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Add</button>`;
                return `<div class="flex flex-col gap-2.5 rounded-[14px] border-[1.5px] p-3.5 ${inAny && !m.out ? 'border-blue-600' : 'border-slate-200'} ${m.out ? 'bg-slate-50' : 'bg-white'}">
      <div class="flex min-h-[22px] items-center gap-2">
        <span class="flex h-3.5 w-3.5 flex-none items-center justify-center rounded-[3px] border-[1.5px] ${vegC}"><span class="h-1.5 w-1.5 rounded-full ${vegD}"></span></span>
        ${m.pop && !m.out ? '<span class="rounded-md bg-amber-100 px-[7px] py-[3px] text-[11px] font-extrabold tracking-wide text-amber-800">POPULAR</span>' : ''}
        ${m.out ? '<span class="rounded-md bg-red-50 px-[7px] py-[3px] text-[11px] font-extrabold tracking-wide text-red-800">SOLD OUT</span>' : ''}
      </div>
      <div class="min-h-[42px] text-base font-bold leading-snug">${esc(m.name)}</div>
      <div class="text-[17px] font-extrabold text-blue-600">${fmt(price)}</div>
      ${sizePills}${action}</div>`;
            }).join('');
        }

        function renderCart() {
            const c = calc();
            $('count').textContent = c.count;
            $('clearBtn').classList.toggle('hidden', !state.cart.length);
            $('summary').textContent = isDine()
                ? `Dine-in · ${state.table ? 'Table ' + state.table : 'No table yet'} · ${state.guests} ${state.guests === 1 ? 'guest' : 'guests'}`
                : (state.type === 'take' ? 'Takeaway' : 'Delivery') + (state.cust.name.trim() ? ' · ' + state.cust.name.trim() : '');

            if (!state.cart.length) {
                $('lines').innerHTML = `<div class="flex flex-col items-center gap-2.5 py-9 text-center text-slate-500">
      <svg class="h-11 w-11 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1 12H6L5 8zM9 8a3 3 0 0 1 6 0"/></svg>
      <div class="text-[15px] font-bold">No items added</div><div class="text-[13px]">Tap Add on any menu item to start.</div></div>`;
                return;
            }
            const chip = 'min-h-9 rounded-full border border-slate-300 bg-white px-3 text-[13px] font-semibold text-slate-700 hover:bg-slate-50';
            $('lines').innerHTML = state.cart.map((l) => {
                const open = state.noteOpen === l.key;
                return `<div class="flex flex-col gap-2 border-b border-slate-100 py-3.5">
      <div class="flex items-center gap-2.5">
        <div class="min-w-0 flex-1"><div class="text-[15px] font-bold leading-snug">${esc(l.name)}</div>
          <div class="text-[13px] text-slate-500">${l.size ? esc(l.size) + ' · ' : ''}${fmt(l.price)} each</div></div>
        <div class="flex items-center overflow-hidden rounded-xl border border-slate-300">
          <button data-action="dec" data-key="${esc(l.key)}" aria-label="Remove one" class="flex h-10 w-10 items-center justify-center bg-white hover:bg-slate-50"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg></button>
          <span class="min-w-7 text-center font-extrabold">${l.qty}</span>
          <button data-action="inc" data-key="${esc(l.key)}" aria-label="Add one more" class="flex h-10 w-10 items-center justify-center bg-white hover:bg-slate-50"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></button>
        </div>
        <div class="w-[78px] text-right text-[15px] font-extrabold">${fmt(l.price * l.qty)}</div>
      </div>
      <div class="flex items-center justify-between">
        <button data-action="note-toggle" data-key="${esc(l.key)}" class="flex min-h-11 items-center gap-1.5 text-[13px] font-bold text-blue-600 hover:text-blue-700">
          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16v4z"/></svg>${l.note ? 'Edit note' : 'Add note'}</button>
        <button data-action="rm" data-key="${esc(l.key)}" aria-label="Remove item" class="flex h-11 w-11 items-center justify-center text-red-600 hover:text-red-700"><svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg></button>
      </div>
      ${l.note && !open ? `<div class="rounded-lg bg-amber-50 px-2.5 py-2 text-[13px] font-semibold text-amber-800">Note: ${esc(l.note)}</div>` : ''}
      ${open ? `<div class="flex flex-col gap-2">
        <input data-field="line-note" data-key="${esc(l.key)}" type="text" aria-label="Item note" placeholder="e.g. less spicy, no onion" value="${esc(l.note)}" class="h-11 rounded-[10px] border border-slate-300 px-3 text-sm placeholder:text-slate-500 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/30">
        <div class="flex flex-wrap gap-2">
          ${['Less spicy', 'Extra spicy', 'No onion'].map((t) => `<button data-action="quick" data-key="${esc(l.key)}" data-v="${t}" class="${chip}">${t}</button>`).join('')}
        </div></div>` : ''}
    </div>`;
            }).join('');
        }

        function renderTotals() {
            const c = calc(), hint = validation();
            const row = (a, b, cls = '') => `<div class="flex justify-between font-semibold text-slate-500"><span>${a}</span><span class="font-bold ${cls || 'text-slate-900'}">${b}</span></div>`;
            $('totals').innerHTML = row('Subtotal', fmt(c.sub))
                + (c.disc > 0 ? row('Discount', '- ' + fmt(c.disc), 'text-emerald-700') : '')
                + ($('svc').checked ? row('Service charge', fmt(c.svc)) : '')
                + ($('vat').checked ? row('VAT', fmt(c.vat)) : '')
                + `<div class="mt-1 flex items-baseline justify-between border-t-2 border-slate-900 pt-3"><span class="text-[17px] font-extrabold">Total</span><span class="text-2xl font-extrabold text-blue-600">${fmt(c.total)}</span></div>`;
            $('hint').innerHTML = hint ? `<div role="status" class="rounded-[10px] bg-amber-50 px-3 py-2.5 text-[13px] font-semibold text-amber-800">${esc(hint)}</div>` : '';
            $('sendBtn').disabled = !!hint;
            $('holdBtn').disabled = !state.cart.length;
            $('count').textContent = c.count;
        }

        function renderDiscMode() {
            const on = 'bg-blue-600 text-white', off = 'bg-white text-slate-700 hover:bg-slate-50';
            $('discPct').className = 'h-11 min-w-11 text-sm font-extrabold ' + (state.discMode === 'pct' ? on : off);
            $('discAmt').className = 'h-11 min-w-[52px] text-sm font-extrabold ' + (state.discMode === 'amt' ? on : off);
        }

        function renderToast() {
            $('toast').innerHTML = state.toast ? `<div role="status" class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">
    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg>
    <span class="flex-1">${esc(state.toast)}</span>
    <button data-action="dismiss" aria-label="Dismiss" class="flex h-11 w-11 items-center justify-center"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button></div>` : '';
        }

        function renderAll() { renderType(); renderResume(); renderLeftTop(); renderChips(); renderGrid(); renderCart(); renderTotals(); renderDiscMode(); renderToast(); }

        /* ================================ ACTIONS ================================ */
        function addItem(id) {
            const m = MENU.find((x) => x.id === id), s = selectedSize(m);
            const size = s ? s.label : '', price = s ? s.price : m.price, key = id + '|' + size;
            const l = state.cart.find((x) => x.key === key);
            if (l) l.qty++; else state.cart.push({ key, id, name: m.name, size, price, qty: 1, note: '' });
        }
        function decItem(key) {
            const l = state.cart.find((x) => x.key === key);
            if (!l) return;
            if (l.qty > 1) l.qty--; else state.cart = state.cart.filter((x) => x.key !== key);
        }
        const refreshOrder = () => { renderGrid(); renderCart(); renderTotals(); };
        const freshOrder = () => {
            state.cart = []; state.noteOpen = null; state.guests = 2;
            state.cust = { name: '', phone: '', addr: '' };
            $('orderNote').value = ''; $('disc').value = '';
        };

        const actions = {
            type: (d) => { state.type = d.v; renderType(); renderLeftTop(); renderCart(); renderTotals(); },
            table: (d) => { state.table = state.table === d.v ? null : d.v; renderLeftTop(); renderCart(); renderTotals(); },
            guest: (d) => { state.guests = Math.min(20, Math.max(1, state.guests + Number(d.v))); renderLeftTop(); renderCart(); },
            cat: (d) => { state.cat = d.v; renderChips(); renderGrid(); },
            veg: () => { state.vegOnly = !state.vegOnly; renderChips(); renderGrid(); },
            size: (d) => { state.pick[d.id] = Number(d.i); renderGrid(); },
            add: (d) => { addItem(d.id); refreshOrder(); },
            // re-adds the same size and price already on the cart line
            inc: (d) => { const l = state.cart.find((x) => x.key === d.key); if (l) l.qty++; refreshOrder(); },
            dec: (d) => { decItem(d.key); refreshOrder(); },
            rm: (d) => { state.cart = state.cart.filter((l) => l.key !== d.key); state.noteOpen = null; refreshOrder(); },
            'note-toggle': (d) => { state.noteOpen = state.noteOpen === d.key ? null : d.key; renderCart(); },
            quick: (d) => { const l = state.cart.find((x) => x.key === d.key); if (l) l.note = d.v; renderCart(); },
            clear: () => { state.cart = []; state.noteOpen = null; refreshOrder(); },
            'disc-mode': (d) => { state.discMode = d.v; renderDiscMode(); renderTotals(); },
            serve: (d) => { tableOrders(state.table)[Number(d.i)].status = 'Served'; renderLeftTop(); },
            'close-table': () => { const t = state.table; delete state.orders[t]; state.table = null; renderLeftTop(); renderCart(); renderTotals(); flash(`Table ${t} closed and freed.`); },
            hold: () => {
                if (!state.cart.length) return;
                state.held.push({
                    type: state.type, table: state.table, guests: state.guests, cart: state.cart, discMode: state.discMode,
                    orderNote: $('orderNote').value, disc: $('disc').value, cust: { ...state.cust }
                });
                state.table = null; freshOrder(); renderAll();
                flash('Order held. Tap Resume held to bring it back.');
            },
            resume: () => {
                if (state.cart.length) { flash('Hold or send the current order first, then resume.'); return; }
                const h = state.held.pop();
                Object.assign(state, { type: h.type, table: h.table, guests: h.guests, cart: h.cart, discMode: h.discMode, cust: h.cust, noteOpen: null });
                $('orderNote').value = h.orderNote; $('disc').value = h.disc; renderAll();
            },
            send: () => {
                if (validation()) return;
                const c = calc();
                let where = state.type === 'delivery' ? 'delivery' : 'takeaway', keep = null;
                if (isDine()) {
                    const prev = tableOrders(state.table);
                    state.orders[state.table] = prev.concat([{
                        n: prev.length + 1, status: 'New', note: $('orderNote').value.trim(),
                        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }),
                        lines: state.cart.map((l) => ({ name: l.name, size: l.size, qty: l.qty, price: l.price, note: l.note }))
                    }]);
                    where = 'Table ' + state.table; keep = state.table;
                }
                state.table = keep; freshOrder(); renderAll();
                flash(`Sent to kitchen: ${c.count} ${c.count === 1 ? 'item' : 'items'} for ${where} (${fmt(c.total)}).`);
            },
            dismiss: () => { state.toast = ''; renderToast(); }
        };
        /* ================================ EVENTS ================================= */
        document.addEventListener('click', (e) => {
            const b = e.target.closest('[data-action]');
            if (b && !b.disabled && actions[b.dataset.action]) actions[b.dataset.action](b.dataset);
        });
        document.addEventListener('input', (e) => {
            const t = e.target, f = t.dataset.field;
            if (t.id === 'search') { state.q = t.value; renderChips(); renderGrid(); }
            else if (t.id === 'disc') renderTotals();
            else if (f === 'line-note') { const l = state.cart.find((x) => x.key === t.dataset.key); if (l) l.note = t.value; }
            else if (f) { state.cust[f] = t.value; if (f === 'name') renderCart(); renderTotals(); }
        });
        document.addEventListener('change', (e) => { if (e.target.id === 'svc' || e.target.id === 'vat') renderTotals(); });

        renderAll();
    </script>
</body>

</html>