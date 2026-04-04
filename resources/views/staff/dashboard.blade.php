@extends('layouts.staff')

@section('title', 'Take Order | ' . config('app.name'))

@section('content')
<style>
    .scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
    <div class="">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-lg md:text-2xl font-extrabold text-gray-900">Take Order</h1>
                <p class="text-xs md:text-sm text-gray-400 mt-0.5">Select items and assign to a table</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT: Menu & Categories (2/3 width) -->
                <div class="lg:col-span-2 space-y-3 md:space-y-4">

                    <!-- Search Bar -->
                    <div class="bg-white rounded-lg shadow-sm p-3 md:p-4">
                        <div class="relative">
                            <input id="searchInput" type="text" placeholder="Search menu items..."
                                class="w-full pl-10 pr-3 py-2 md:py-2.5 text-sm md:text-base outline-0 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                onkeyup="filterMenu()">

                            <svg class="absolute left-3 top-2.5 md:top-3 h-4 w-4 md:h-5 md:w-5 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-sm p-3 md:p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 md:mb-3">Categories</h3>

                        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide" id="categories"></div>
                    </div>

                    <!-- Menu Items -->
                    <div class="bg-white rounded-lg shadow-sm p-3 md:p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 md:mb-4">Menu Items</h3>

                        <div id="menu"
                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Cart (1/3 width) -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- Current Order Card -->
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Current Order</h3>
                            <span id="cartCount"
                                class="bg-blue-600 text-white text-xs font-semibold px-2.5 py-1 rounded-full">0</span>
                        </div>

                        <!-- Table Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Table</label>
                            <select id="tableSelect" onchange="onTableChange()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-0">
                                <option value="">Choose a table...</option>
                            </select>
                        </div>

                        <!-- Cart Items -->
                        <div class="border-t border-b border-gray-200 py-4 mb-4">
                            <div id="cart" class="space-y-3 max-h-96 overflow-y-auto">
                                <div class="text-center py-8 text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <p class="text-sm">No items added</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span id="subtotal" class="font-semibold">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Items</span>
                                <span id="totalItems" class="font-semibold">0</span>
                            </div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t">
                                <span>Total</span>
                                <span id="total" class="text-blue-600">Rs. 0</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            <button onclick="submitOrder()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Place Order
                            </button>
                            <button onclick="clearCart()"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-lg transition duration-200">
                                Clear Cart
                            </button>
                        </div>
                    </div>

                    <!-- Previously Ordered Items Card -->
                    <div id="previousOrdersCard" class="bg-white rounded-lg shadow-sm p-5 hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Previous Orders</h3>
                            <span class="text-xs text-gray-500">Table <span id="selectedTableNumber">-</span></span>
                        </div>

                        <div id="previousOrders" class="space-y-3 max-h-80 overflow-y-auto">
                            <!-- Previous orders will be loaded here -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ====== CANCEL ORDER MODAL ======  -->
    <div id="cancelModal"
    class="fixed inset-0 z-50 hidden flex items-end sm:items-center justify-center">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        onclick="closeCancelModal()"></div>

    <!-- Modal Box -->
    <div
        class="relative bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full max-w-md mx-2 sm:mx-4 overflow-hidden animate-modal max-h-[90vh] flex flex-col">

        <!-- Header -->
        <div class="bg-red-500 px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-full p-2">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-base sm:text-lg">Cancel Order</h2>
                    <p class="text-red-100 text-xs sm:text-sm"
                        id="cancelModalSubtitle">Order item</p>
                </div>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div class="p-4 sm:p-6 overflow-y-auto">

            <!-- Reasons -->
            <div class="mb-4 sm:mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3">
                    Why is this order being cancelled?
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2"
                    id="reasonButtons">

                    <button onclick="selectReason(this, 'Customer changed mind')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        😕 Changed mind
                    </button>

                    <button onclick="selectReason(this, 'Item unavailable')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        🚫 Item unavailable
                    </button>

                    <button onclick="selectReason(this, 'Wrong item ordered')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        ❌ Wrong item
                    </button>

                    <button onclick="selectReason(this, 'Customer leaving')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        🚶 Customer leaving
                    </button>

                    <button onclick="selectReason(this, 'Long waiting time')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        ⏱️ Long wait time
                    </button>

                    <button onclick="selectReason(this, 'Other')"
                        class="reason-btn text-left px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:border-red-400 hover:bg-red-50 transition">
                        📝 Other
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-5 sm:mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Additional notes
                    <span class="text-gray-400 font-normal">(optional)</span>
                </label>

                <textarea id="cancelNotes" rows="3"
                    placeholder="Add any extra details..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent resize-none">
                </textarea>
            </div>

            <!-- Error -->
            <p id="cancelError"
                class="text-red-500 text-xs mb-3 hidden">
                Please select a reason before cancelling.
            </p>
        </div>

        <!-- Sticky Buttons -->
        <div class="p-4 border-t bg-white flex gap-2 sm:gap-3">
            <button onclick="closeCancelModal()"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm sm:text-base font-semibold py-2 rounded-lg transition">
                Go Back
            </button>

            <button onclick="confirmCancel()"
                class="flex-1 bg-red-500 hover:bg-red-600 text-white text-sm sm:text-base font-semibold py-2 rounded-lg transition flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
            </button>
        </div>

    </div>
</div>

    @include('staff.attendance')

    <style>
        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-modal {
            animation: modalIn 0.2s ease-out forwards;
        }
    </style>
    </div>

    </div>
    </div>
    </div>

    <script>
        let cart = [];
        let allMenuItems = [];
        let activeCategory = '';
        let selectedTableId = null;

        const slug = localStorage.getItem('restro_url');
        const token = localStorage.getItem('auth_token');

        // ================= CATEGORY LOGIC =================
        async function loadCategories() {
            const res = await fetch(`/api/v1/staff/${slug}/categories`, {
                method: 'GET',
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

            let html = `
        <button id="cat-all"
            onclick="setActiveCategory('')"
            class="cat-btn px-4 py-2 rounded-lg bg-blue-600 text-white font-medium text-sm whitespace-nowrap transition duration-200">
            All Items
        </button>
    `;

            data.data.forEach(cat => {
                html += `
            <button id="cat-${cat.id}"
                onclick="setActiveCategory(${cat.id})"
                class="cat-btn px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-medium text-sm whitespace-nowrap hover:bg-gray-200 transition duration-200">
                ${cat.name}
            </button>`;
            });

            document.getElementById('categories').innerHTML = html;
        }

        // Highlight active category
        function setActiveCategory(id = '') {
            activeCategory = id;

            document.querySelectorAll('.cat-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });

            const activeBtn = document.getElementById(`cat-${id || 'all'}`);
            if (activeBtn) {
                activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
                activeBtn.classList.add('bg-blue-600', 'text-white');
            }

            loadMenu(id);
        }

        // ================= MENU LOGIC =================
        function showSkeleton() {
            let skeleton = '';
            for (let i = 0; i < 6; i++) {
                skeleton += `
        <div class="border border-gray-200 rounded-lg p-4 animate-pulse">
            <div class="h-5 bg-gray-200 rounded mb-3"></div>
            <div class="h-4 bg-gray-100 rounded w-2/3 mb-4"></div>
            <div class="h-9 bg-gray-200 rounded"></div>
        </div>`;
            }
            document.getElementById('menu').innerHTML = skeleton;
        }

        async function loadMenu(categoryId = '') {
            showSkeleton();

            let url = `/api/v1/staff/${slug}/menu`;
            if (categoryId) url += `?category_id=${categoryId}`;

            const res = await fetch(url, {
                method: 'GET',
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

            allMenuItems = data.data;

            renderMenu(allMenuItems);
        }

        function getCartQty(id) {
            const item = cart.find(i => i.id === id);
            return item ? item.qty : 0;
        }

        // Render menu items
        function renderMenu(items) {
            let html = '';

            if (items.length === 0) {
                html = `
            <div class="col-span-2 text-center py-12 text-gray-400">
                <svg class="mx-auto h-16 w-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="font-medium">No items found</p>
                <p class="text-sm mt-1">Try adjusting your search or category filter</p>
            </div>
        `;
            } else {
                items.forEach(item => {
                    const qty = getCartQty(item.id);

                    html += `
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 ${!item.isAvailable ? 'opacity-60' : ''}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 mb-1">${item.name}</h4>
                        <p class="text-blue-600 font-bold text-lg">Rs. ${item.price}</p>
                    </div>
                    ${!item.isAvailable ? `
                                                                                                    <span class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-1 rounded">Sold Out</span>
                                                                                                ` : qty > 0 ? `
                                                                                                    <span class="bg-green-100 text-green-600 text-xs font-semibold px-2 py-1 rounded">${qty} in cart</span>
                                                                                                ` : ''}
                </div>

                ${
                    !item.isAvailable
                    ? `<button disabled
                                                                                                        class="w-full bg-gray-300 text-gray-500 py-2.5 rounded-lg cursor-not-allowed font-medium">
                                                                                                        Unavailable
                                                                                                   </button>`
                    : qty === 0
                    ? `<button
                                                                                                        class="w-full bg-gradient-to-br from-indigo-600 to-blue-600 text-white py-2.5 rounded-lg hover:ring-2 hover:ring-indigo-400 transition duration-200 font-medium flex items-center justify-center gap-2"
                                                                                                        onclick="addToCart(${item.id}, '${item.name.replace(/'/g, "\\'")}', ${item.price})">
                                                                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                                                        </svg>
                                                                                                        Add to Cart
                                                                                                   </button>`
                    : `<div class="flex items-center justify-between gap-2">
                                                                                                        <button onclick="updateQty(${item.id}, -1)"
                                                                                                            class="flex-1 px-3 py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-semibold transition duration-200">
                                                                                                            −
                                                                                                        </button>

                                                                                                        <span class="font-bold text-lg px-4">${qty}</span>

                                                                                                        <button onclick="updateQty(${item.id}, 1)"
                                                                                                            class="flex-1 px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition duration-200">
                                                                                                            +
                                                                                                        </button>
                                                                                                   </div>`
                }
            </div>`;
                });
            }

            document.getElementById('menu').innerHTML = html;
        }

        // ================= CART LOGIC =================
        function addToCart(id, name, price) {
            const found = cart.find(i => i.id === id);
            if (found) {
                found.qty++;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    qty: 1
                });
            }

            console.log(id, name, price)

            renderCart();
            renderMenu(allMenuItems); // Re-render menu to show updated quantities
        }

        function updateQty(id, change) {
            const item = cart.find(i => i.id === id);
            if (!item) return;

            item.qty += change;

            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }

            renderCart();
            renderMenu(allMenuItems); // Re-render menu to show updated quantities
        }

        function renderCart() {
            let html = '';
            let subtotal = 0;
            let totalItems = 0;

            if (cart.length === 0) {
                document.getElementById('cart').innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-sm">No items added</p>
            </div>
        `;
            } else {
                cart.forEach(i => {
                    const itemTotal = i.price * i.qty;
                    subtotal += itemTotal;
                    totalItems += i.qty;

                    html += `
            <div class="flex justify-between items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                <div class="flex-1">
                    <p class="font-medium text-gray-800 text-sm">${i.name}</p>
                    <p class="text-xs text-gray-500 mt-1">Rs. ${i.price} × ${i.qty}</p>
                </div>

                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg">
                        <button onclick="updateQty(${i.id}, -1)"
                            class="px-2 py-1 hover:bg-gray-200 rounded-l-lg transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <span class="px-2 font-semibold text-sm min-w-[24px] text-center">${i.qty}</span>
                        <button onclick="updateQty(${i.id}, 1)"
                            class="px-2 py-1 hover:bg-gray-200 rounded-r-lg transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                            </svg>
                        </button>
                    </div>
                    <p class="font-bold text-sm text-gray-800 min-w-[60px] text-right">Rs. ${itemTotal}</p>
                </div>
            </div>`;
                });

                document.getElementById('cart').innerHTML = html;
            }

            // Update summary
            document.getElementById('cartCount').textContent = totalItems;
            document.getElementById('subtotal').textContent = `Rs. ${subtotal}`;
            document.getElementById('total').textContent = `Rs. ${subtotal}`;
            document.getElementById('totalItems').textContent = totalItems;
        }

        function clearCart() {
            if (cart.length === 0) return;

            if (confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                renderCart();
                renderMenu(allMenuItems);
            }
        }

        // ================= SEARCH BAR =================
        function filterMenu() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const filtered = allMenuItems.filter(i => i.name.toLowerCase().includes(q));
            renderMenu(filtered);
        }

        // ================= TABLES =================
        async function loadTables() {
            const res = await fetch(`/api/v1/staff/${slug}/tables`, {
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

            let html = '<option value="">Choose a table...</option>';
            data.data.forEach(t => {
                html += `<option value="${t.id}">Table ${t.table_number}</option>`;
            });
            document.getElementById('tableSelect').innerHTML = html;
        }

        // ================= TABLE CHANGE HANDLER =================
        function onTableChange() {
            const tableId = parseInt(document.getElementById('tableSelect').value);
            selectedTableId = tableId;


            if (!tableId) {
                document.getElementById('previousOrdersCard').classList.add('hidden');
                return;
            }

            document.getElementById('selectedTableNumber').textContent = `T${tableId}`;

            // Load previous orders for this table
            loadPreviousOrders(tableId);
        }

        async function loadPreviousOrders(tableId) {

            try {

                const response = await fetch(
                    `/api/v1/staff/${slug}/order/table/${tableId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`,
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();

                if (!data.items || data.items.length === 0) {
                    document.getElementById('previousOrdersCard').classList.add('hidden');
                    return;
                }

                document.getElementById('previousOrdersCard').classList.remove('hidden');

                let html = '';
                data.items.forEach((order, index) => {
                    if (order.item_status === 'cancelled') {

                        html += `
            <div class="border border-gray-200 rounded-lg p-3 bg-gray-50 opacity-60">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-medium text-gray-500 text-sm line-through">${order.name}</p>
                        <p class="text-xs text-gray-400 mt-1">Rs. ${order.price} × ${order.quantity}</p>
                    </div>
                    <span class="bg-red-100 text-red-500 text-xs font-semibold px-2 py-1 rounded">Cancelled</span>
                </div>
                ${order.cancelReason ? `<p class="text-xs text-gray-400 mt-2 italic">Reason: ${order.cancelReason}</p>` : ''}
            </div>`;
                        return;
                    }

                    // TODO: add more status color
                    order.item_status === 'served' ?
                        'bg-green-100 text-green-700' :
                        'bg-orange-100 text-orange-700';


                    // Only allow cancellation for non-served items
                    const canCancel = order.item_status !== 'served';


                    html += `
        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                    <p class="font-medium text-gray-800 text-sm">${order.name}</p>
                    <p class="text-xs text-gray-500 mt-1">Rs. ${order.price} × ${order.quantity} = Rs. ${order.price * order.quantity}</p>
                </div>
                <span class="${order.item_status} text-xs font-semibold px-2 py-1 rounded capitalize">
                    ${order.item_status}
                </span>
            </div>
            <div class="flex justify-between items-center">
                <p class="text-xs text-gray-400">${order.time}</p>
                <div class="flex items-center gap-2">
                    <button
                        onclick="reorderItem(${order.menu_item_id}, '${order.name.replace(/'/g, "\\'")}', ${order.price}, this)"
                        class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Reorder
                    </button>
                    ${canCancel ? `
                                                                        <span class="text-gray-300">|</span>
                                                                        <button
                                                                            onclick="openCancelModal(${selectedTableId}, ${index}, '${order.name.replace(/'/g, "\\'")}')"
                                                                            class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1 transition">
                                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                            Cancel
                                                                        </button>` : ''}
                </div>
            </div>
        </div>`;
                });

                document.getElementById('previousOrders').innerHTML = html;

            } catch (error) {
                console.error('Failed to fetch order:', error);
            }

        }

        function reorderItem(id, name, price) {
            addToCart(id, name, price);

            // Show feedback
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<svg class="h-3 w-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>`;

            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 500);
        }


        // ================= CANCEL ORDER MODAL =================
        let cancelContext = {
            tableId: null,
            orderIndex: null
        };
        let selectedReason = '';

        function openCancelModal(tableId, orderIndex, itemName) {
            cancelContext = {
                tableId,
                orderIndex
            };
            selectedReason = '';

            document.getElementById('cancelModalSubtitle').textContent = itemName;
            document.getElementById('cancelNotes').value = '';
            document.getElementById('cancelError').classList.add('hidden');

            // Reset reason buttons
            document.querySelectorAll('.reason-btn').forEach(btn => {
                btn.classList.remove('border-red-400', 'bg-red-50', 'text-red-700', 'font-semibold');
                btn.classList.add('border-gray-200', 'text-gray-700');
            });

            const modal = document.getElementById('cancelModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            cancelContext = {
                tableId: null,
                orderIndex: null
            };
            selectedReason = '';
        }

        function selectReason(btn, reason) {
            selectedReason = reason;
            document.getElementById('cancelError').classList.add('hidden');

            document.querySelectorAll('.reason-btn').forEach(b => {
                b.classList.remove('border-red-400', 'bg-red-50', 'text-red-700', 'font-semibold');
                b.classList.add('border-gray-200', 'text-gray-700');
            });

            btn.classList.add('border-red-400', 'bg-red-50', 'text-red-700', 'font-semibold');
            btn.classList.remove('border-gray-200', 'text-gray-700');
        }

        function confirmCancel() {
            if (!selectedReason) {
                document.getElementById('cancelError').classList.remove('hidden');
                return;
            }

            const notes = document.getElementById('cancelNotes').value.trim();
            const fullReason = notes ? `${selectedReason} — ${notes}` : selectedReason;

            const {
                tableId,
                orderIndex
            } = cancelContext;
            if (tableId && orderIndex !== null && PREVIOUS_ORDERS[tableId]) {
                PREVIOUS_ORDERS[tableId][orderIndex].cancelled = true;
                PREVIOUS_ORDERS[tableId][orderIndex].cancelReason = fullReason;
            }

            closeCancelModal();
            loadPreviousOrders(tableId);
            showToast('Order item cancelled', 'error');
        }

        // ================= SUBMIT ORDER =================
        async function submitOrder() {
            const tableId = document.getElementById('tableSelect').value;



            if (!tableId) {
                alert('Please select a table');
                return;
            }

            if (cart.length === 0) {
                alert('Cart is empty. Please add items before placing order.');
                return;
            }

            const total = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);

            const res = await fetch(`/api/v1/staff/${slug}/table/${tableId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!res.ok) {
                throw new Error(`Failed to retrieve T${tableId} status`);
                showToast(`Failed to retrieve T${tableId} status!`, 'error');

            }

            const data = await res.json();
            console.log(data)

            if (data.tableStatus === 'occupied') {
                // Table has active order → find that order id, then call addItem
                await addItemsToExistingOrder(tableId, total);
            } else if (data.tableStatus === 'available') {
                // Fresh table → call store
                await createNewOrder(tableId, total);
            } else {
                alert('reserved');
            }

            loadPreviousOrders(tableId);
        }

        async function addItemsToExistingOrder(tableId, total) {
            try {
                const res = await fetch(`/api/v1/staff/${slug}/add-items`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        items: cart.map(i => ({
                            menu_item_id: i.id,
                            quantity: i.qty
                        }))
                    })
                });

                if (!res.ok) {
                    throw new Error('Failed to place order');
                    showToast(`Failed to place order for T${tableId}!`, 'error');

                }

                // Success
                cart = [];
                renderCart();
                renderMenu(allMenuItems);
                document.getElementById('tableSelect').value = '';

                showToast(`Order placed for T${tableId}! Total: Rs. ${total}`, 'success');

            } catch (error) {

                showToast(`Failed to place order for T${tableId}!`, 'error');
                console.error('Order error:', error);
            }
        }


        async function createNewOrder(tableId, total) {
            try {
                const res = await fetch(`/api/v1/staff/${slug}/orders`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        items: cart.map(i => ({
                            menu_item_id: i.id,
                            quantity: i.qty
                        }))
                    })
                });

                if (!res.ok) {
                    throw new Error('Failed to place order');
                    showToast(`Failed to place order for T${tableId}!`, 'error');

                }

                // Success
                cart = [];
                renderCart();
                renderMenu(allMenuItems);
                document.getElementById('tableSelect').value = '';

                showToast(`Order placed for T${tableId}! Total: Rs. ${total}`, 'success');

            } catch (error) {

                showToast(`Failed to place order for T${tableId}!`, 'error');
                console.error('Order error:', error);
            }
        }

        // ================= INITIAL LOAD =================
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
            loadMenu();
            loadTables();
        });

        // Load on page ready
        loadCategories();
        loadMenu();
        loadTables();
    </script>
@endsection
