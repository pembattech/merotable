@extends('layouts.customer')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')

    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .skel {
            animation: pulse 1.4s ease infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .25
            }
        }

        .dot-preparing {
            animation: blink 1.2s ease infinite;
        }

        @keyframes ring {

            0%,
            100% {
                transform: rotate(0)
            }

            20% {
                transform: rotate(-25deg)
            }

            60% {
                transform: rotate(25deg)
            }
        }

        .ringing .bell-icon {
            animation: ring 0.5s ease 3;
        }

        @keyframes callPulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(124, 58, 237, .4);
            }

            70% {
                transform: scale(1.06);
                box-shadow: 0 0 0 30px rgba(124, 58, 237, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(124, 58, 237, 0);
            }
        }

        .call-pulse-anim {
            animation: callPulse .85s ease infinite;
        }

        .fab-hidden {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- Notif banner -->
    <div id="notifBanner"
        class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-5 text-sm text-gray-600">
        <span>🔕</span>
        <span class="flex-1 text-xs">Enable notifications so staff get alerted of your orders instantly.</span>
        <button onclick="requestNotifPermission()"
            class="flex-shrink-0 bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
            Enable
        </button>
    </div>

    <!-- Search -->
    <div class="relative mb-4">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input id="searchInput" type="text" placeholder="Search menu items…"
            class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition-all"
            oninput="filterMenu()">
    </div>

    <!-- Categories -->
    <div id="categories" class="flex gap-2 overflow-x-auto pb-1 mb-5 no-scrollbar">
    </div>

    <!-- Section label -->
    <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-3">Menu</p>

    <!-- Menu grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-14" id="menu"></div>

    <!-- My Orders -->
    <div id="myOrdersSection" class="mt-8 hidden">
        <p class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-3">My Orders</p>
        <div id="myOrders" class="flex flex-col gap-2"></div>
    </div>

    <!-- ══════════════════ CART FAB ══════════════════ -->
    <div id="cartFab" onclick="openSheet()"
        class="fab-hidden fixed bottom-5 left-1/2 -translate-x-1/2 z-30 w-[calc(100%-2rem)] max-w-sm
           bg-blue-600 hover:bg-blue-700 active:scale-[.98] rounded-2xl px-5 py-3.5
           flex items-center justify-between shadow-xl shadow-blue-300/50 cursor-pointer transition-all duration-200 border border-blue-500">
        <div class="flex items-center gap-3">
            <div id="fabCount" class="mono bg-white/20 text-white text-sm font-bold px-2.5 py-0.5 rounded-lg">0</div>
            <div>
                <div class="text-white text-sm font-bold leading-tight">View Order</div>
                <div id="fabSub" class="text-blue-100 text-xs mt-0.5">0 items</div>
            </div>
        </div>
        <div id="fabPrice" class="mono text-white font-bold text-base">Rs. 0</div>
    </div>

    <!-- ══════════════════ SHEET BACKDROP ══════════════════ -->
    <div id="sheetBackdrop" onclick="closeSheet()"
        class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-250">
    </div>

    <!-- ══════════════════ CART SHEET ══════════════════ -->
    <div id="cartSheet"
        class="fixed bottom-0 left-0 right-0 z-80 bg-white rounded-t-3xl max-h-[85vh] flex flex-col
           shadow-2xl translate-y-full transition-transform duration-300 ease-[cubic-bezier(.4,0,.2,1)]">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mt-3 flex-shrink-0"></div>
        <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100 flex-shrink-0">
            <h2 class="text-base font-extrabold text-gray-900">Your Order</h2>
            <button onclick="closeSheet()"
                class="w-8 h-8 grid place-items-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold transition-colors">✕</button>
        </div>
        <div id="sheetBody" class="flex-1 overflow-y-auto px-5 py-2"></div>
        <div class="px-5 pt-3 pb-6 border-t border-gray-100 bg-white flex-shrink-0">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500">Total</span>
                <span id="sheetTotal" class="mono text-lg font-extrabold text-gray-900">Rs. 0</span>
            </div>
            <button id="placeBtn" onclick="placeOrder()"
                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-[.98] text-white font-extrabold text-sm py-3.5 rounded-2xl shadow-lg shadow-blue-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                ✓ Place Order
            </button>
        </div>
    </div>

    <!-- ══════════════════ CALL STAFF OVERLAY ══════════════════ -->
    <div id="callOverlay"
        class="fixed inset-0 z-95 flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300">
        <div
            class="call-pulse-anim w-36 h-36 rounded-full bg-violet-100 border-2 border-violet-300 grid place-items-center text-6xl">
            🔔</div>
    </div>

    <!-- ══════════════════ SUCCESS OVERLAY ══════════════════ -->
    <div id="successOverlay"
        class="fixed inset-0 z-100 bg-white/80 backdrop-blur-md flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="successBox"
            class="text-center bg-white border border-gray-200 rounded-3xl shadow-2xl px-8 py-10 max-w-xs w-full mx-4 scale-95 transition-transform duration-300">
            <div
                class="w-16 h-16 rounded-full bg-green-50 border border-green-200 grid place-items-center text-3xl mx-auto mb-5">
                ✅</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Order Placed!</h3>
            <p id="successMsg" class="text-sm text-gray-500 leading-relaxed">Your order has been sent to the kitchen. We'll
                have it ready soon!</p>
            <button onclick="closeSuccess()"
                class="mt-6 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition-colors">
                Got it
            </button>
        </div>
    </div>

    <script>
        /* ═══ CONFIG ═══ */
        const tableId = new URLSearchParams(location.search).get('table') || localStorage.getItem('table_id') || '5';
        const tableNum = new URLSearchParams(location.search).get('table_num') || localStorage.getItem('table_num') ||
            tableId;

        document.getElementById('tableLabel').textContent = tableNum;

        /* ═══ STATE ═══ */
        let cart = [],
            allMenuItems = [],
            activeCategory = '',
            myOrders = [];

        /* ═══ NOTIFICATIONS ═══ */
        function checkNotifPermission() {
            if (!('Notification' in window) || Notification.permission === 'granted')
                document.getElementById('notifBanner').classList.add('hidden');
        }
        async function requestNotifPermission() {
            if (!('Notification' in window)) return;
            const perm = await Notification.requestPermission();
            if (perm === 'granted') {
                document.getElementById('notifBanner').classList.add('hidden');
                showToast('Notifications enabled 🔔', 'success');
            }
        }

        function sendStaffNotification(title, body) {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;
            try {
                new Notification(title, {
                    body,
                    tag: 'merotable-order'
                });
            } catch (e) {}
        }

        /* ═══ CALL STAFF ═══ */
        async function callStaff() {
            const btn = document.getElementById('callStaffBtn');
            if (btn.disabled) return;
            btn.disabled = true;
            btn.classList.add('ringing');
            const ov = document.getElementById('callOverlay');
            ov.style.opacity = '1';
            setTimeout(() => ov.style.opacity = '0', 2000);
            sendStaffNotification('🔔 Assistance Requested!', `Table ${tableNum} is calling for staff.`);
            try {
                await fetch(`/api/v1/staff/${slug}/call-staff`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        table_id: tableId
                    })
                });
            } catch (_) {}
            showToast('Staff has been called! 🔔', 'success');
            setTimeout(() => {
                btn.disabled = false;
                btn.classList.remove('ringing');
            }, 6000);
        }

        /* ═══ CATEGORIES ═══ */
        async function loadCategories(token) {
            try {
                const res = await fetch(`/api/v1/customer/qr/${token}/categories`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (!data.success) return;

                const wrap = document.getElementById('categories');
                wrap.innerHTML = '';

                /* ===== All Category (Default Active) ===== */
                const allBtn = document.createElement('button');
                allBtn.className =
                    'cat-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-600 text-white border border-blue-600 transition-all';
                allBtn.dataset.id = '';
                allBtn.textContent = 'All';
                allBtn.onclick = () => setCategory(token, '');
                wrap.appendChild(allBtn);

                /* ===== API Categories ===== */
                data.data.forEach(cat => {
                    const btn = document.createElement('button');

                    btn.className =
                        'cat-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:border-blue-400 hover:text-blue-600 transition-all';

                    btn.dataset.id = cat.id;
                    btn.textContent = cat.name;

                    btn.onclick = () => setCategory(token, cat.id);

                    wrap.appendChild(btn);
                });

            } catch (error) {
                console.error('Category load error:', error);
            }
        }

        function setCategory(token, id = '') {
            activeCategory = id;
            document.querySelectorAll('.cat-btn').forEach(b => {
                const active = String(b.dataset.id) === String(id);
                b.className = active ?
                    'cat-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-semibold bg-blue-600 text-white border border-blue-600 transition-all' :
                    'cat-btn whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-semibold bg-white text-gray-600 border border-gray-200 hover:border-blue-400 hover:text-blue-600 transition-all';
            });
            loadMenu(token, id);
        }

        /* ═══ MENU ═══ */
        function showSkeleton() {
            document.getElementById('menu').innerHTML = Array(6).fill(`
        <div class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col gap-3 shadow-sm">
          <div class="skel bg-gray-200 h-3 rounded-md w-full"></div>
          <div class="skel bg-gray-100 h-3 rounded-md w-2/5"></div>
          <div class="skel bg-gray-200 h-10 rounded-xl mt-1"></div>
        </div>`).join('');
        }

        async function loadMenu(token, categoryId = '') {
            showSkeleton();

            try {
                let url = `/api/v1/customer/qr/${token}/menu`;
                if (categoryId) url += `?category_id=${categoryId}`;

                const res = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (!data.success) {
                    console.error(data.message || 'Failed to load menu');
                    return;
                }

                allMenuItems = data.data;

            } catch (error) {
                console.error('Menu loading error:', error);
                allMenuItems = [];
            }

            renderMenu(allMenuItems);
        }

        function getCartQty(id) {
            return cart.find(i => i.id === id)?.qty ?? 0;
        }

        function renderMenu(items) {
            const grid = document.getElementById('menu');
            if (!items.length) {
                grid.innerHTML = `<div class="col-span-full text-center py-16 text-gray-400">
          <div class="text-5xl mb-3">🍽️</div>
          <p class="font-bold text-gray-600 mb-1">No items found</p>
          <p class="text-sm">Try a different search or category</p>
        </div>`;
                return;
            }
            grid.innerHTML = items.map(item => {
                const qty = getCartQty(item.id);
                const unavail = !item.isAvailable;

                const badge = unavail ?
                    `<span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md bg-red-50 text-red-400 border border-red-100 flex-shrink-0">Sold out</span>` :
                    qty > 0 ?
                    `<span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md bg-green-50 text-green-600 border border-green-100 flex-shrink-0">${qty}</span>` :
                    '';

                const controls = unavail ?
                    `<button disabled class="w-full py-2 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold cursor-not-allowed">Unavailable</button>` :
                    qty === 0 ?
                    `<button onclick="addToCart(${item.id},'${item.name.replace(/'/g,"\\'")}',${item.price})"
               class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[.97] text-white text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm shadow-blue-200 transition-all">
               <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
               Add to Order
             </button>` :
                    `<div class="flex items-center justify-between bg-gray-100 rounded-xl px-2 py-1">
               <button onclick="updateQty(${item.id},-1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-700 font-bold text-base grid place-items-center hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition-colors shadow-sm">−</button>
               <span class="mono font-bold text-gray-900 text-sm px-2">${qty}</span>
               <button onclick="updateQty(${item.id},1)" class="w-7 h-7 rounded-lg bg-white border border-gray-200 text-gray-700 font-bold text-base grid place-items-center hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors shadow-sm">+</button>
             </div>`;

                return `
          <div class="bg-white border p-5 ${unavail ? 'border-gray-100 opacity-55' : 'border-gray-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md hover:shadow-blue-100/60'} rounded-2xl flex flex-col gap-2.5 shadow-sm transition-all duration-200">
            <div class="flex items-start justify-between gap-1.5">
              <div class="text-sm font-bold text-gray-900 leading-snug flex-1">${item.name}</div>
              ${badge}
            </div>
            <div class="mono text-sm font-semibold text-blue-600">Rs. ${item.price}</div>
            ${controls}
          </div>`;
            }).join('');
        }

        function filterMenu() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            renderMenu(allMenuItems.filter(i => i.name.toLowerCase().includes(q)));
        }

        /* ═══ CART ═══ */
        function addToCart(id, name, price) {
            const found = cart.find(i => i.id === id);
            found ? found.qty++ : cart.push({
                id,
                name,
                price,
                qty: 1
            });
            syncCart();
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
            syncCart();
        }

        function syncCart() {
            const totalQty = cart.reduce((s, i) => s + i.qty, 0);
            const totalPrice = cart.reduce((s, i) => s + i.price * i.qty, 0);
            const fab = document.getElementById('cartFab');
            if (totalQty === 0) {
                fab.classList.add('fab-hidden');
            } else {
                fab.classList.remove('fab-hidden');
                document.getElementById('fabCount').textContent = totalQty;
                document.getElementById('fabSub').textContent = `${totalQty} item${totalQty !== 1 ? 's' : ''}`;
                document.getElementById('fabPrice').textContent = `Rs. ${totalPrice}`;
            }
            renderMenu(allMenuItems);
            renderSheet();
        }

        /* ═══ SHEET ═══ */
        function openSheet() {
            const bd = document.getElementById('sheetBackdrop');
            const sheet = document.getElementById('cartSheet');

            bd.classList.remove('opacity-0', 'pointer-events-none');
            bd.classList.add('opacity-100', 'pointer-events-auto');

            sheet.classList.remove('translate-y-full');
            sheet.classList.add('translate-y-0');

            document.body.style.overflow = 'hidden';
        }

        function closeSheet() {
            const bd = document.getElementById('sheetBackdrop');
            const sheet = document.getElementById('cartSheet');

            bd.classList.add('opacity-0', 'pointer-events-none');
            bd.classList.remove('opacity-100', 'pointer-events-auto');

            sheet.classList.remove('translate-y-0');
            sheet.classList.add('translate-y-full');

            document.body.style.overflow = '';
        }

        function renderSheet() {
            console.log(cart)
            const body = document.getElementById('sheetBody');
            if (!cart.length) {
                body.innerHTML = `<div class="text-center py-10 text-gray-400">
          <div class="text-5xl mb-3">🛒</div>
          <p class="font-bold text-gray-600 mb-1">Cart is empty</p>
          <p class="text-xs">Browse the menu and add items</p>
        </div>`;
            } else {
                body.innerHTML = cart.map(i => `
          <div class="flex items-center gap-3 py-3 border-b border-gray-100 last:border-0">
            <div class="flex-1 min-w-0">
              <div class="text-sm font-bold text-gray-900 truncate">${i.name}</div>
              <div class="mono text-xs text-gray-400 mt-0.5">Rs. ${i.price} × ${i.qty}</div>
            </div>
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl px-1.5 py-1 flex-shrink-0">
              <button onclick="updateQty(${i.id},-1)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 text-gray-700 font-bold text-sm grid place-items-center hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition-colors shadow-sm">−</button>
              <span class="mono text-sm font-bold text-gray-900 px-1.5">${i.qty}</span>
              <button onclick="updateQty(${i.id},1)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 text-gray-700 font-bold text-sm grid place-items-center hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-colors shadow-sm">+</button>
            </div>
            <div class="mono text-sm font-bold text-blue-600 min-w-[58px] text-right">Rs. ${i.price * i.qty}</div>
          </div>`).join('');
            }
            const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
            document.getElementById('sheetTotal').textContent = `Rs. ${total}`;
        }

        /* ═══ PLACE ORDER ═══ */
        async function placeOrder() {
            const path = window.location.pathname;
            const token = path.split('/').pop();

            if (!cart.length) return;

            const btn = document.getElementById('placeBtn');
            btn.disabled = true;
            btn.textContent = 'Placing…';

            const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

            const items = cart.map(i => ({
                menu_item_id: i.id,
                quantity: i.qty
            }));

            const ordered = [...cart];

            try {
                // /* ===== Send Order ===== */
                const orderRes = await fetch(`/api/v1/customer/qr/${token}/order`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        items: items
                    })
                });

                const orderData = await orderRes.json();

                if (!orderData.success) {
                    throw new Error(orderData.message || 'Order failed');
                }

                console.log(orderData);

                // TODO: needed work here
                /* ===== Send Staff Notification ===== */
                sendStaffNotification(
                    `🍽️ New Order — Table ${tableNum}`,
                    ordered.map(i => `${i.name} ×${i.qty}`).join(', ') +
                    ` | Total: Rs. ${total}`
                );

                /* ===== Save Order Locally ===== */
                const now = new Date();

                ordered.forEach(i => {
                    myOrders.unshift({
                        name: i.name,
                        price: i.price,
                        qty: i.qty,
                        status: 'pending',
                        time: now.toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        })
                    });
                });

                /* ===== Reset Cart ===== */
                cart = [];
                syncCart();
                closeSheet();
                renderMyOrders();

                /* ===== Success UI ===== */
                document.getElementById('successMsg').textContent =
                    `Your order has been sent to the kitchen! Total: Rs. ${total}`;

                const ov = document.getElementById('successOverlay');
                const bx = document.getElementById('successBox');

                ov.style.opacity = '1';
                ov.style.pointerEvents = 'auto';
                bx.style.transform = 'scale(1)';

            } catch (error) {

                console.error('Order error:', error);
                alert('Failed to place order. Please try again.');

            } finally {

                btn.disabled = false;
                btn.textContent = '✓ Place Order';

            }
        }

        function closeSuccess() {
            const ov = document.getElementById('successOverlay');
            const bx = document.getElementById('successBox');
            ov.style.opacity = '0';
            ov.style.pointerEvents = 'none';
            bx.style.transform = 'scale(0.95)';
        }

        /* ═══ MY ORDERS ═══ */
        const statusStyles = {
            pending: {
                dot: 'bg-amber-400',
                dotShadow: 'shadow-[0_0_0_4px_rgba(245,158,11,.15)]',
                badge: 'bg-amber-50 text-amber-500 border border-amber-100',
                label: 'Pending'
            },
            preparing: {
                dot: 'bg-blue-500 dot-preparing',
                dotShadow: 'shadow-[0_0_0_4px_rgba(59,130,246,.15)]',
                badge: 'bg-blue-50 text-blue-500 border border-blue-100',
                label: 'Preparing'
            },
            served: {
                dot: 'bg-green-500',
                dotShadow: 'shadow-[0_0_0_4px_rgba(34,197,94,.15)]',
                badge: 'bg-green-50 text-green-600 border border-green-100',
                label: 'Served'
            },
            cancelled: {
                dot: 'bg-red-400',
                dotShadow: 'shadow-[0_0_0_4px_rgba(239,68,68,.15)]',
                badge: 'bg-red-50 text-red-400 border border-red-100',
                label: 'Cancelled'
            },
        };

        function renderMyOrders() {
            const section = document.getElementById('myOrdersSection');
            if (!myOrders.length) {
                section.classList.add('hidden');
                return;
            }
            section.classList.remove('hidden');
            document.getElementById('myOrders').innerHTML = myOrders.map(o => {
                const st = statusStyles[o.status] || statusStyles.pending;
                return `
          <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 ${st.dot} ${st.dotShadow}"></div>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-bold text-gray-900">${o.name}</div>
              <div class="mono text-xs text-gray-400 mt-0.5">×${o.qty} · Rs. ${o.price * o.qty} · ${o.time}</div>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md ${st.badge}">${st.label}</span>
          </div>`;
            }).join('');
        }


        async function getRestaurantDetails() {
            const path = window.location.pathname;
            const token = path.split('/').pop();

            console.log(token);

            try {
                const res = await fetch(`/api/v1/customer/qr/${token}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (!data.success) {
                    showToast('Something went wrong ❌', 'error');
                    return;
                }

                localStorage.clear()

                localStorage.setItem('restro_name', data.data.name);
                localStorage.setItem('restro_url', data.data.slug);

            } catch (error) {
                console.error('Error:', error);
                showToast('Something went wrong ❌', 'error');
            }
        }

        function getToken() {
            const path = window.location.pathname;
            const token = path.split('/').pop();

            return token;
        }

        /* ═══ INIT ═══ */
        document.addEventListener('DOMContentLoaded', () => {
            checkNotifPermission();
            let qr_token = getToken();
            getRestaurantDetails();
            loadCategories(qr_token);
            loadMenu(qr_token);
        });
    </script>

@endsection
