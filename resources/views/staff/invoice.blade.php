<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-up {
        animation: fadeUp 0.35s ease both;
    }

    /* Scrollable item list – constrained height in screen view */
    .item-scroll {
        max-height: 130px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .item-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .item-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .item-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    /* Fade hint under the scroll area */
    .scroll-hint {
        text-align: center;
        font-size: 11px;
        color: #94a3b8;
        padding: 4px 0 6px;
        border-top: 1px solid #f1f5f9;
        transition: opacity 0.2s;
    }

    /* @media print {
          body {
              background: white;
          }

          .no-print {
              display: none !important;
          } */

    /* On print, let all items expand naturally – no scroll cap */
    /* .item-scroll {
              max-height: none !important;
              overflow: visible !important;
          }

          .scroll-hint {
              display: none !important;
          }

          .print-only {
              display: block !important;
          }
      } */
    /*
      .print-only {
          display: none;
      } */

    @media print {

        body * {
            visibility: hidden;
        }

        #thermalReceipt,
        #thermalReceipt * {
            visibility: visible;
        }

        #thermalReceipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

    }
</style>

<div id="openInvoiceModal" class="animate-slide-up fixed inset-0 z-50 hidden items-end sm:items-center justify-center">

    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeInvoiceModal()"></div>


    <div id="invoiceModal" class="w-full max-w-sm mx-auto mb-2 sm:mb-0 text-xs">

        <!-- Top bar -->
        <div class="flex items-center justify-between mb-4 no-print fade-up">
            <div class="flex items-center gap-2">
                <button onclick="printThermal()"
                    class="flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Invoice
                </button>
                <button onclick="downloadPDF()"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </button>
            </div>
            <button onclick="closeInvoiceModal()"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Invoice card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden fade-up border border-gray-100">

            <!-- Header -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 px-4 py-3 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight">INVOICE</h2>
                        <p class="invoice-number text-blue-100 text-[10px]"></p>
                    </div>
                    <div
                        class="flex flex-col gap-1 justify-center items-center bg-white/20 backdrop-blur-sm rounded-lg px-3 py-1">
                        <p class="text-blue-100 text-xs font-semibold uppercase tracking-wider">Status</p>
                        <p class="payment-status text-white font-bold text-[10px]"></p>
                    </div>
                </div>
            </div>

            <!-- From / Bill To -->
            <div class="px-4 py-2 grid grid-cols-2 gap-4 border-b border-gray-100 bg-gray-50/50">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">From</p>
                    <h3 class="restro-name font-bold text-gray-800 text-sm"></h3>
                    <p class="text-[11px] text-gray-500 leading-tight">
                        <span class="restro-address"></span>
                    </p>
                    <p class="text-[11px] text-gray-500 leading-tight">
                        <span class="restro-phone"></span>
                    </p>

                    <p class="text-[11px] text-gray-500 leading-tight">
                        VAT: <span class="restro-vat"></span>
                    </p>

                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Bill To</p>
                    <h3 class="font-bold text-gray-800 text-sm">Table <span class="table-number"></span></h3>
                    <p class="text-[11px] text-gray-500 leading-tight">
                        Order #<span class="order-id"></span>
                    </p>
                    <p class="text-[11px] text-gray-500 leading-tight">
                        <span class="invoice-date"></span>
                    </p>
                </div>
            </div>

            <!-- Items table -->
            <div class="px-4 py-2">
                {{-- <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-1 text-[9px] font-bold text-gray-400 uppercase">Item</th>
                            <th class="text-center py-1 text-[9px] font-bold text-gray-400 uppercase">Qty</th>
                            <th class="text-center py-1 text-[9px] font-bold text-gray-400 uppercase">Price</th>
                            <th class="text-right py-1 text-[9px] font-bold text-gray-400 uppercase">Total</th>
                        </tr>
                    </thead>
                </table> --}}

                <!-- Header table -->
                <table class="w-full table-fixed">
                    <colgroup>
                        <col style="width: 50%">
                        <col style="width: 15%">
                        <col style="width: 15%">
                        <col style="width: 20%">
                    </colgroup>

                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-1 text-[9px] font-bold text-gray-400 uppercase">Item</th>
                            <th class="text-center py-1 text-[9px] font-bold text-gray-400 uppercase">Qty</th>
                            <th class="text-center py-1 text-[9px] font-bold text-gray-400 uppercase">Price</th>
                            <th class="text-right py-1 text-[9px] font-bold text-gray-400 uppercase">Total</th>
                        </tr>
                    </thead>
                </table>

                <!-- Scrollable body – max-height on screen, expands on print -->
                {{-- <div class="item-scroll" id="itemScroll">
                    <table class="w-full">
                        <tbody id="itemBody" class="text-xs"></tbody>
                    </table>
                </div> --}}

                <!-- Body table -->
                <div class="item-scroll" id="itemScroll">
                    <table class="w-full table-fixed">
                        <colgroup>
                            <col style="width: 50%">
                            <col style="width: 15%">
                            <col style="width: 15%">
                            <col style="width: 20%">
                        </colgroup>

                        <tbody id="itemBody" class="text-xs"></tbody>
                    </table>
                </div>


                <!-- Scroll hint (hidden after user scrolls, hidden on print) -->
                <div class="scroll-hint" id="scrollHint"></div>
            </div>

            <!-- Totals -->
            <div class="px-4 py-2">
                <div class="bg-gray-50 rounded-xl border border-gray-100 p-2 space-y-1 text-[11px]">
                    <div class="flex justify-between text-[11px]">
                        <span class="text-gray-500" id="subtotalLabel">Subtotal</span>
                        <span class="font-semibold text-gray-700" id="subtotalAmt">Rs. 0</span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-gray-500">Tax (13% VAT)</span>
                        <span class="font-semibold text-gray-700" id="taxAmt">Rs. 0</span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span class="text-gray-500">Service charge (10%)</span>
                        <span class="font-semibold text-gray-700" id="scAmt">Rs. 0</span>
                    </div>
                    <div class="border-t-2 border-gray-200 pt-2 flex justify-between">
                        <span class="font-bold text-gray-800 text-sm">Grand Total</span>
                        <span class="font-extrabold text-blue-600 text-lg" id="grandTotal">Rs. 0</span>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <div
                        class="flex-1 bg-green-50 text-green-700 text-[10px] font-bold py-1.5 rounded-md text-center border border-green-100">
                        <p class="payment-method uppercase"></p>
                    </div>
                    <div
                        class="flex-1 bg-blue-50 text-blue-700 text-[10px] font-bold py-1.5 rounded-md text-center border border-blue-100">
                        <p class="payment-status uppercase"></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-2 text-center border-t border-gray-100">
                <p class="text-[11px] font-bold text-gray-600">Thank you for dining!</p>
                <p class="text-[9px] text-gray-400 mt-1">MeroTable – Computer Generated</p>
            </div>

        </div>
    </div>
</div>

<div id="thermalReceipt" class="hidden print:block text-xs font-mono text-black">

    <div class="w-[280px] mx-auto">

        <!-- Header -->
        <div class="text-center">
            <h2 class="font-bold text-sm restroName"></h2>
            <p class="restro-address"></p>
            <p>Tel: <span class="restro-phone"></span></p>
            <p>VAT: <span class="restro-vat"></span></p>
        </div>

        <div class="border-t border-dashed my-2"></div>

        <!-- Info -->
        <div class="text-[11px]">
            <p>Invoice: <span class="invoice-number"></span></p>
            <p>Table: <span class="table-number"></span></p>
            <p>Order: #<span class="order-id"></span></p>
            <p>Date: <span class="invoice-date"></span></p>
        </div>

        <div class="border-t border-dashed my-2"></div>

        <!-- Items -->
        <div>
            <div class="flex justify-between font-bold">
                <span>Item</span>
                <span>Total</span>
            </div>

            <div id="thermalItems"></div>
        </div>

        <div class="border-t border-dashed my-2"></div>

        <!-- Totals -->
        <div class="text-[11px] space-y-1">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span id="tSubtotal">Rs. 0</span>
            </div>
            <div class="flex justify-between">
                <span>VAT (13%)</span>
                <span id="tTax">Rs. 0</span>
            </div>
            <div class="flex justify-between">
                <span>SC (10%)</span>
                <span id="tSC">Rs. 0</span>
            </div>

            <div class="border-t border-dashed mt-2 pt-1 flex justify-between font-bold">
                <span>Total</span>
                <span id="tTotal">Rs. 0</span>
            </div>
        </div>

        <div class="border-t border-dashed my-2"></div>

        <!-- Footer -->
        <div class="text-center text-[10px]">
            <p>Payment:
            <p class="payment-method uppercase"></p>
            </p>
            <p>*** Thank You ***</p>
        </div>

    </div>
</div>
<script>
    let invoiceData = null;

    function openInvoiceModal(data) {
        invoiceData = data;

        scrollEl.scrollTop = 0;

        const now = new Date();

        const formattedDate = now.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        document.querySelectorAll('.restro-name').forEach(el => el.textContent = invoiceData.restaurant.restaurantName);
        document.querySelectorAll('.restro-address').forEach(el => el.textContent = invoiceData.restaurant
            .restaurantAddress);
        document.querySelectorAll('.restro-phone').forEach(el => el.textContent = invoiceData.restaurant
            .restaurantContact);
        document.querySelectorAll('.restro-vat').forEach(el => el.textContent = "#########");
        document.querySelectorAll('.invoice-number').forEach(el => el.textContent = invoiceData.invoiceNumber);
        document.querySelectorAll('.table-number').forEach(el => el.textContent = invoiceData.tableNumber);
        document.querySelectorAll('.order-id').forEach(el => el.textContent = invoiceData.orderId);
        document.querySelectorAll('.payment-method').forEach(el => el.textContent = invoiceData.paymentMethod);
        document.querySelectorAll('.payment-status').forEach(el => el.textContent = invoiceData.paymentStatus);
        document.querySelectorAll('.invoice-date').forEach(el => el.textContent = formattedDate);

        document.getElementById('openInvoiceModal').classList.add('flex');
        document.getElementById('openInvoiceModal').classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

        populateInvoice(invoiceData);

    }

    function closeInvoiceModal() {
        document.getElementById('openInvoiceModal').classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }
    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeInvoiceModal();
    });


    function populateInvoice(invoiceData) {

        //TODO: get it from the restaurant setting.
        const TAX_RATE = 0.13;
        const SC_RATE = 0.10;

        const tbody = document.getElementById('itemBody');
        tbody.innerHTML = ''; // clear previous items

        let subtotal = 0;

        invoiceData.orderItems.forEach(item => {
            const lineTotal = item.quantity * item.price;
            subtotal += lineTotal;

            const tr = document.createElement('tr');
            tr.className = 'border-b border-gray-50';

            tr.innerHTML = `
            <td class="py-1.5 text-[11px] font-medium text-gray-700">
                ${item.menuItem?.name || 'Item'}
            </td>
            <td class="text-center py-1.5 text-[11px] text-gray-500">
                ${item.quantity}
            </td>
            <td class="text-center py-1.5 text-[11px] text-gray-500">
                Rs. ${Number(item.price).toLocaleString()}
            </td>
            <td class="text-right py-1.5 text-[11px] font-bold text-gray-800">
                Rs. ${lineTotal.toLocaleString()}
            </td>
        `;

            tbody.appendChild(tr);
        });

        const tax = Math.round(subtotal * TAX_RATE);
        const sc = Math.round(subtotal * SC_RATE);
        const grand = subtotal + tax + sc;

        document.getElementById('subtotalLabel').textContent =
            `Subtotal (${invoiceData.orderItems.length} items)`;

        document.getElementById('subtotalAmt').textContent =
            `Rs. ${subtotal.toLocaleString()}`;

        document.getElementById('taxAmt').textContent =
            `Rs. ${tax.toLocaleString()}`;

        document.getElementById('scAmt').textContent =
            `Rs. ${sc.toLocaleString()}`;

        document.getElementById('grandTotal').textContent =
            `Rs. ${grand.toLocaleString()}`;
    }

    // ── Scroll hint ───────────────────────────────────────────────
    const scrollEl = document.getElementById('itemScroll');
    const hintEl = document.getElementById('scrollHint');

    function updateHint(order) {
        const atBottom =
            scrollEl.scrollHeight - scrollEl.scrollTop <= scrollEl.clientHeight + 4;

        if (scrollEl.scrollHeight <= scrollEl.clientHeight) {
            hintEl.style.display = 'none';
        } else if (atBottom) {
            hintEl.textContent = `${order.order_items.length} items total`;
        } else {
            hintEl.textContent = `Scroll to see all ${order.order_items.length} items ↓`;
        }
    }


    scrollEl.addEventListener('scroll', () => {
        if (invoiceData) {
            updateHint(invoiceData);
        }
    });


    // ── PDF download ──────────────────────────────────────────────
    function downloadPDF() {
        alert('PDF download – integrate with jsPDF or server-side PDF generation.');
    }

    function renderThermalReceipt(items) {
        const container = document.getElementById('thermalItems');

        let subtotal = 0;
        container.innerHTML = '';

        items.forEach(item => {
            const total = item.quantity * item.price;
            subtotal += total;

            container.innerHTML += `
            <div class="flex justify-between text-[11px]">
                <span>${item.name} x${item.quantity}</span>
                <span>${total}</span>
            </div>
        `;
        });

        const tax = Math.round(subtotal * 0.13);
        const sc = Math.round(subtotal * 0.10);
        const grand = subtotal + tax + sc;

        document.getElementById('tSubtotal').textContent = `Rs. ${subtotal}`;
        document.getElementById('tTax').textContent = `Rs. ${tax}`;
        document.getElementById('tSC').textContent = `Rs. ${sc}`;
        document.getElementById('tTotal').textContent = `Rs. ${grand}`;
    }

    function printThermal() {
        if (!invoiceData) return;

        const items = invoiceData.orderItems.map(item => ({
            name: item.menuItem?.name || 'Item',
            quantity: item.quantity,
            price: item.price
        }));

        renderThermalReceipt(items);
        window.print();
    }
</script>
