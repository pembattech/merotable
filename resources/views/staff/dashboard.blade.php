@extends('layouts.staff')

@section('title', 'Order | ' . config('app.name'))

@section('content')

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
                            <input id="search" type="text" aria-label="Search menu items" placeholder="Search menu items..."
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

                    <div id="totals" class="flex flex-col gap-2 border-t border-slate-100 pt-3.5 text-sm"></div>
                    <div id="hint"></div>

                    <div class="flex gap-2.5">
                        <button id="holdBtn" data-action="hold"
                            class="flex min-h-[52px] items-center gap-2 rounded-xl border-[1.5px] border-slate-300 bg-white px-[18px] text-[15px] font-bold hover:bg-slate-50 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500 disabled:hover:bg-slate-100">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                stroke-linecap="round" aria-hidden="true">
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

        const slug = localStorage.getItem('restro_url');
        const token = localStorage.getItem('auth_token');

        const CATEGORIES = [];

        // price = the "Full" / "Medium" price (or the only price when the item has no sizes)
        // sizes: 'hf' = Half/Full, 'sml' = Small/Medium/Large, omitted = no size choice
        const MENU = [];

        const TABLES = [];

        let SERVICE_RATE = 0, VAT_RATE = 0;

        /* ================================ STATE ================================== */
        const state = {
            type: 'dine',            // 'dine' | 'take' | 'delivery'
            table: null, table_number: null, guests: 2,
            cat: 'All Items', q: '', vegOnly: false,
            pick: {},                // selected size index per menu item id
            cart: [],                // { key, id, name, size, price, qty, note }
            noteOpen: null, discMode: 'pct',
            cust: { name: '', phone: '', addr: '' },
            held: [],
            // Sent orders per table (running bill). Sample data for T2 and T6.
            orders: {
                // T2: [{
                //     n: 1, time: '12:40', status: 'Preparing', note: '', lines: [
                //         { name: 'Buff Momo', size: 'Full', qty: 2, price: 170, note: '' },
                //         { name: 'Masala Chai', size: 'Medium', qty: 2, price: 40, note: 'Less sugar' }]
                // }],
                // T6: [
                //     {
                //         n: 1, time: '12:15', status: 'Served', note: '', lines: [
                //             { name: 'Chicken Chowmein', size: 'Full', qty: 1, price: 220, note: '' },
                //             { name: 'Sweet Lassi', size: 'Medium', qty: 2, price: 120, note: '' }]
                //     },
                //     {
                //         n: 2, time: '12:48', status: 'New', note: 'Serve with extra plates', lines: [
                //             { name: 'French Fries', size: 'Large', qty: 1, price: 210, note: '' }]
                //     }
                // ]
            }
        };

        /* =============================== HELPERS ================================= */
        const $ = (id) => document.getElementById(id);
        const fmt = (n) => 'Rs. ' + Math.round(n).toLocaleString('en-NP');
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const r5 = (n) => Math.round(n / 5) * 5;

        function sizeList(m) {
            if (m.out || !m.sizeOptions?.length) return null;

            return m.sizeOptions.map(size => ({
                id: size.id,
                label: size.name,
                code: size.code,
                price: Number(size.price)
            }));
        }
        const selectedSize = (m) => { const l = sizeList(m); return l ? l[state.pick[m.id] ?? 1] : null; };
        const tableOrders = (id) => state.orders[id] || [];
        const isDine = () => state.type === 'dine';

        function calc() {
            const sub = state.cart.reduce((a, l) => a + (l.price * l.qty), 0);
            const count = state.cart.reduce((a, l) => a + l.qty, 0);
            const dv = Math.max(0, Number($('disc').value || 0));
            const disc = state.discMode === 'pct' ? Math.round(sub * Math.min(dv, 100) / 100) : Math.min(dv, sub);
            const taxable = sub - disc;
            const svc = Math.round(taxable * SERVICE_RATE);
            const vat = Math.round((taxable + svc) * VAT_RATE);

            return {
                sub,
                count,
                disc,
                svc,
                vat,
                total: taxable + svc + vat
            };
        }

        function validation() {
            if (!state.cart.length) return 'Add at least one item to place the order.';
            if (isDine() && !state.table) return 'Choose a table to send this order.';
            if (state.type === 'delivery' && (!state.cust.phone.trim() || !state.cust.addr.trim())) return 'Delivery needs a phone number and an address.';
            return '';
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

            const seatsOf = Object.fromEntries(TABLES.map((t) => [t.table_number, t.seats]));
            const over = state.table && state.guests > seatsOf[state.table];
            const tableBtns = TABLES.map(t => {
                const n = tableOrders(t.id).length;
                const busy = n > 0;
                const on = state.table === t.id;

                let cls = 'border-emerald-200 bg-emerald-50 text-emerald-800 hover:border-emerald-400';
                let sub = 'Available';

                if (t.status === 'occupied') {
                    cls = on
                        ? 'border-red-600 bg-red-600 text-white'
                        : 'border-red-200 bg-red-50 text-red-800 hover:border-red-400';

                    sub = `${n} ${n === 1 ? 'order' : 'orders'}`;
                } else if (t.status === 'reserved') {
                    cls = 'cursor-not-allowed border-amber-200 bg-amber-50 text-amber-800';
                    sub = 'Reserved';
                } else if (on) {
                    cls = 'border-blue-600 bg-blue-600 text-white';
                }

                return `<button
                                                                                                                                                    data-action="table"
                                                                                                                                                    data-v="${t.id}"
                                                                                                                                                    aria-pressed="${on}"
                                                                                                                                                    ${t.status === 'reserved' ? 'disabled' : ''}
                                                                                                                                                    class="flex min-h-16 flex-col items-center justify-center gap-0.5 rounded-xl border-[1.5px] ${cls}">
                                                                                                                                                    <span class="text-[17px] font-extrabold">${t.table_number}</span>
                                                                                                                                                    <span class="text-xs font-semibold">${sub}</span>
                                                                                                                                                </button>`;
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
                                                                                                                                                                                                                                <div><h3 class="text-base font-extrabold">Table ${esc(state.table_number)} · running orders</h3>
                                                                                                                                                                                                                                  <div class="mt-0.5 text-[13px] font-semibold text-slate-500">${rounds.length} ${rounds.length === 1 ? 'round' : 'rounds'} · ${fmt(tableTotal)} before tax</div></div>
                                                                                                                                                                                                                               </div>${roundsHtml}</div>`;
            }

            el.className = 'flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5';
            el.innerHTML = `
                                                                                                                                                                                                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                                                                                                                                                                                              <div class="flex flex-wrap items-center gap-4">
                                                                                                                                                                                                                                <h2 class="text-base font-extrabold">Table</h2>
                                                                                                                                                                                                                                <div class="flex flex-wrap gap-3.5 text-xs font-semibold text-slate-500">
                                                                                                                                                                                                                                  <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-700"></span>Available</span>
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
                // console.log(m.out, m.id, m.name, 'sizes', sizes, 'size', size, 'price', price);
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
                ? `Dine-in · ${state.table_number ? 'Table ' + state.table_number : 'No table yet'} · ${state.guests} ${state.guests === 1 ? 'guest' : 'guests'}`
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
                + (row('Service charge', fmt(c.svc)))
                + (row('VAT', fmt(c.vat)))
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

        function renderAll() { renderType(); renderResume(); renderLeftTop(); renderChips(); renderGrid(); renderCart(); renderTotals(); renderDiscMode(); }

        /* ================================ ACTIONS ================================ */

        function addItem(id) {
            const m = MENU.find(x => x.id == id);

            if (!m || m.out) return;

            const s = selectedSize(m);

            // Selected size details
            const size = s ? s.label : '';
            const price = s ? Number(s.price) : Number(m.price);
            const menu_item_size_id = s ? s.id : null;

            // Unique key for menu item + selected size
            const key = id + '|' + size;

            // Check if item already exists in cart
            const existing = state.cart.find(x => x.key === key);
            if (existing) {
                existing.qty++;
            } else {
                state.cart.push({
                    key,
                    id: m.id,
                    name: m.name,

                    menu_item_size_id,
                    size,

                    price,
                    qty: 1,
                    note: ''
                });
            }
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
        // ================= SEND ORDER =================

        let isSending = false;

        const send = async () => {
            if (isSending) return;

            if (validation()) return;

            const c = calc();

            let where = state.type === 'delivery'
                ? 'delivery'
                : 'takeaway';

            let keep = null;

            if (isDine()) {
                where = 'Table ' + state.table_number;
                keep = state.table;
            }

            isSending = true;

            try {
                // Submit order to Laravel API
                const response = await submitOrder();

                // Reset selected table and cart
                state.table = keep;

                freshOrder();

                // Reload tables to reflect the updated status
                await loadTables()

                // Immediately refresh all open orders
                await loadAllOpenOrders(false);

                // Update UI
                renderAll();

                showToast(
                    `Sent to kitchen: ${c.count} ${c.count === 1 ? 'item' : 'items'} for ${where} (${fmt(c.total)}).`
                );

                console.log('Order submitted:', response);

            } catch (error) {
                console.error('Order submission error:', error);

                showToast(
                    error.message || 'Failed to send order!',
                    'error'
                );

            } finally {
                isSending = false;
            }
        };

        const actions = {
            type: (d) => { state.type = d.v; renderType(); renderLeftTop(); renderCart(); renderTotals(); },
            // table: (d) => { state.table = state.table === d.v ? null : d.v; renderLeftTop(); renderCart(); renderTotals(); },
            table: async (d) => {
                const tableId = Number(d.v);

                // Toggle table selection
                if (state.table === tableId) {
                    state.table = null;
                    state.table_number = null;
                    renderLeftTop();
                    renderCart();
                    renderTotals();
                    return;
                }

                state.table = tableId;
                state.table_number = TABLES.find(t => t.id === tableId)?.table_number || null;

                const selectedTable = TABLES.find(t => t.id === tableId);

                if (!selectedTable) return;

                // Fetch running orders only for occupied tables
                if (selectedTable.status === 'occupied') {
                    await loadActiveOrders(tableId);
                } else {
                    state.orders[tableId] = [];
                }

                renderLeftTop();
                renderCart();
                renderTotals();
            },
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
            hold: () => {
                if (!state.cart.length) return;
                state.held.push({
                    type: state.type, table: state.table, guests: state.guests, cart: state.cart, discMode: state.discMode,
                    orderNote: $('orderNote').value, disc: $('disc').value, cust: { ...state.cust }
                });
                state.table = null; state.table_number = null; freshOrder(); renderAll();
                showToast('Order held. Tap Resume held to bring it back.');
            },
            resume: () => {
                if (state.cart.length) { showToast('Hold or send the current order first, then resume.'); return; }
                const h = state.held.pop();
                Object.assign(state, { type: h.type, table: h.table, guests: h.guests, cart: h.cart, discMode: h.discMode, cust: h.cust, noteOpen: null });
                $('orderNote').value = h.orderNote; $('disc').value = h.disc; renderAll();
            },
            send: () => send()
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


    </script>


    <script>

        async function loadTables(shouldRender = true, showErrors = true) {
            try {
                const res = await fetch(`/api/v1/staff/${slug}/tables`, {
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`
                    }
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (showErrors) {
                        showToast(
                            data.message || 'Something went wrong ❌',
                            'error'
                        );
                    }

                    return false;
                }

                TABLES.length = 0;

                data.data.forEach(t => {
                    TABLES.push({
                        id: t.id,
                        table_number: t.table_number,
                        area_name: t.area_name,
                        seats: t.seats ?? 4,
                        status: t.status
                    });
                });

                if (shouldRender) {
                    renderAll();
                }

                return true;

            } catch (error) {
                console.error('Load tables failed:', error);

                if (showErrors) {
                    showToast('Unable to load tables.', 'error');
                }

                return false;
            }
        }

        async function loadCategories() {
            const res = await fetch(`/api/v1/staff/${slug}/categories`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await res.json();

            if (!data.success) {
                showToast(data.message || 'Something went wrong', 'error');
                return;
            }

            CATEGORIES.length = 0;
            CATEGORIES.push('All Items');

            data.data.forEach(category => {
                CATEGORIES.push(category.name);
            });

        }

        async function loadMenu() {
            const res = await fetch(`/api/v1/staff/${slug}/menu`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await res.json();

            if (!data.success) {
                showToast(data.message || 'Something went wrong ❌', 'error');
                return;
            }

            MENU.length = 0;
            MENU.push(...data.data.map(item => ({
                id: item.id,
                name: item.name,
                cat: item.cat,
                price: item.price,
                veg: item.veg,
                pop: item.pop,
                out: item.out,
                sizes: item.sizes,
                sizeOptions: item.sizeOptions,
                description: item.description,
                image: item.image
            })));
        }

        async function loadActiveOrders(tableId) {
            try {
                const response = await fetch(
                    `/api/v1/staff/${slug}/order/table/${tableId}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            Authorization: `Bearer ${token}`
                        }
                    }
                );

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load orders.');
                }

                // Store only the KOT array
                state.orders[tableId] = data.kots || [];

                // Keep order summary if needed
                state.orderDetails = state.orderDetails || {};
                state.orderDetails[tableId] = {
                    order_id: data.order_id,
                    total: data.total,
                    status: data.status,
                    order_note: data.order_note,
                    table_status: data.table_status
                };

                renderAll()

            } catch (error) {
                console.error('Load active orders:', error);
                state.orders[tableId] = [];
                renderAll()
            }
        }

        async function loadAllOpenOrders(shouldRender = true) {
            try {
                const response = await fetch(
                    `/api/v1/staff/${slug}/orders/open`,
                    {
                        headers: {
                            Accept: 'application/json',
                            Authorization: `Bearer ${token}`
                        }
                    }
                );

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Failed to load open orders.');
                }

                // Replace old data so closed orders disappear
                state.orders = data.orders || {};
                state.orderDetails = data.order_details || {};

                if (shouldRender) {
                    renderAll();
                }

            } catch (error) {
                console.error('Load all open orders:', error);
            }
        }



        // ================= SUBMIT ORDER TO API =================
        async function submitOrder() {
            const tableId = isDine() ? state.table : null;

            let endpoint;

            // Check table status for dine-in
            if (isDine()) {
                const statusRes = await fetch(
                    `/api/v1/staff/${slug}/table/${tableId}`,
                    {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    }
                );

                const statusData = await statusRes.json();

                if (!statusRes.ok) {
                    throw new Error(statusData.message || 'Failed to check table status.');
                }

                if (statusData.tableStatus === 'occupied') {
                    endpoint = `/api/v1/staff/${slug}/add-items`;
                } else if (statusData.tableStatus === 'available') {
                    endpoint = `/api/v1/staff/${slug}/orders`;
                } else {
                    throw new Error('This table is reserved.');
                }
            } else {
                endpoint = `/api/v1/staff/${slug}/orders`;
            }


            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    id: tableId,

                    // General note for the entire order
                    note: $('orderNote').value.trim() || null,

                    // Individual order items
                    items: state.cart.map(item => ({
                        menu_item_id: item.id,

                        // Selected size ID, null for items without sizes
                        menu_item_size_id: item.menu_item_size_id ?? null,

                        quantity: item.qty,

                        // Individual item note
                        note: item.note || null
                    }))
                })
            });


            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Failed to submit order.');
            }

            return data;
        }

        let orderPollingInterval = null;
        let isPollingOrders = false;

        let tablePollingInterval = null;
        let isPollingTables = false;

        // Poll open orders every 5 seconds
        async function pollOpenOrders() {
            if (isPollingOrders || document.hidden) return;

            isPollingOrders = true;

            try {
                await loadAllOpenOrders();
            } catch (error) {
                console.error('Polling orders failed:', error);
            } finally {
                isPollingOrders = false;
            }
        }

        // Poll tables every 5 seconds
        async function pollTables() {
            if (isPollingTables || document.hidden) return;

            isPollingTables = true;

            try {
                const success = await loadTables(false, false);

                if (success) {
                    renderAll();
                }
            } catch (error) {
                console.error('Polling tables failed:', error);
            } finally {
                isPollingTables = false;
            }
        }

        function startOrderPolling() {
            stopOrderPolling();

            orderPollingInterval = setInterval(pollOpenOrders, 5000);
        }

        function stopOrderPolling() {
            if (orderPollingInterval) {
                clearInterval(orderPollingInterval);
                orderPollingInterval = null;
            }
        }

        function startTablePolling() {
            stopTablePolling();

            tablePollingInterval = setInterval(pollTables, 5000);
        }

        function stopTablePolling() {
            if (tablePollingInterval) {
                clearInterval(tablePollingInterval);
                tablePollingInterval = null;
            }
        }

        // Detect when the user returns to the browser tab.
        // If the page becomes visible again, immediately refresh open orders
        // instead of waiting for the next 5-second polling interval.
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                pollOpenOrders();
                pollTables();
            }
        });

        document.addEventListener('DOMContentLoaded', async () => {

            // Load billing settings first
            const loaded = await window.loadBillingSettings();

            if (!loaded) return;

            const settings = window.BILLING_SETTINGS;

            VAT_RATE = settings.taxEnabled
                ? settings.taxPercentage / 100
                : 0;

            SERVICE_RATE = settings.serviceChargeEnabled
                ? settings.serviceChargePercentage / 100
                : 0;

            await loadTables();
            await loadCategories();
            await loadMenu();

            // Load all open orders
            await loadAllOpenOrders(false);

            // Start polling after initial load
            startOrderPolling();
            startTablePolling();

            renderAll();
        });

    </script>


@endsection

@push('scripts')
    <script src="{{ asset('js/billing-settings.js') }}"></script>
@endpush