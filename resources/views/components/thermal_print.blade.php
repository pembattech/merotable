<style>
    /* ---------- Print styles ---------- */
    @media print {

        /* @page {
            size: A4;
            margin: 0;
        } */

        @page {
            size: 80mm auto;
            margin: 0;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;

            /* A4 width for thermal printers */
            /* width: 210mm !important; */

            /* Thermal printer width for 80mm paper */
            width: 80mm !important;
            background: white !important;
        }

        /*
     * Hide everything using visibility, NOT display.
     * display:none on an ancestor prevents ALL descendants from
     * rendering no matter what display value they have — so if
     * #thermalReceipt is nested inside any wrapper (a layout div,
     * a Livewire/Vue root, #app, etc.) rather than being a direct
     * child of <body>, "body>* { display:none }" hides that wrapper
     * and takes #thermalReceipt down with it -> blank/white page.
     * visibility:hidden does not have this problem: a visible
     * descendant still renders even inside a hidden ancestor.
     */
        body * {
            visibility: hidden !important;
        }

        /* Show only the receipt and everything inside it */
        #thermalReceipt,
        #thermalReceipt * {
            visibility: visible !important;
        }

        #thermalReceipt {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;

            display: block !important;

            /* width: 210mm !important;
            max-width: 210mm !important; */
            width: 80mm !important;
            max-width: 80mm !important;

            margin: 0 !important;
            padding: 5mm 8mm !important;

            box-sizing: border-box !important;

            background: white !important;

            overflow: visible !important;

            color: #000 !important;

            border: none !important;
            border-radius: 0 !important;

            font-family: 'DejaVu Sans Mono', monospace !important;


        }

        /* Receipt inner content */
        #thermalReceipt>div {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
        }

        /* Remove screen-only restrictions */
        #thermalReceipt .item-scroll {
            max-height: none !important;
            overflow: visible !important;
        }

        /* Prevent unnecessary page breaks */
        #thermalReceipt,
        #thermalReceipt>div,
        #thermalReceipt table,
        #thermalReceipt tr {
            page-break-before: auto !important;
            page-break-after: auto !important;
            page-break-inside: avoid !important;
            break-before: auto !important;
            break-after: auto !important;
            break-inside: avoid !important;
        }

        /* Don't print UI */
        .no-print {
            display: none !important;
        }

        #itemScroll {
            max-height: none !important;
            overflow: visible !important;
        }
    }
</style>



<div id="thermalReceipt" style="display:none;">
    <div style="font-size: 11px; line-height: 1.35; color: #000; width: 100%;">

        <div style="text-align:center; margin-bottom:5px;">
            <div class="font-extrabold" style="font-size:14px; text-transform:uppercase;" id="tRestName"></div>
            <div style="font-size:10px;" id="tRestAddress"></div>
            <div style="font-size:10px;" id="tRestContact"></div>
            {{-- TODO: get the VAT number --}}
            <div style="font-size:10px;" id="tRestVAT">VAT: #######</div>
        </div>

        <div style="margin-bottom:2px;" id="tInvoiceNo"></div>
        <div style="margin-bottom:2px;" id="tTable"></div>

        <div style="margin-bottom:2px;" id="tOrder"></div>
        <div style="margin-bottom:2px;" id="tDate"></div>
        <div style="margin-bottom:2px;" id="tPaymentMethod"></div>
        <div style="margin-bottom:2px;" id="tPaymentStatus"></div>


        <hr style="border:none; border-top:1px dashed #000; margin:6px 0;">

        <div id="itemScroll" class="item-scroll">
            <div class="flex justify-between text-[11px]">
                <span class="font-bold">Items</span>
                <span class="font-bold">Amount</span>
            </div>
            <div id="thermalItems"></div>
        </div>

        <hr style="border:none; border-top:1px dashed #000; margin:4px 0;">

        <div style="display:flex; justify-content:space-between; margin-bottom: 2px;">
            <span>Subtotal</span><span id="tSubtotal"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom: 2px;" id="tDiscountRow">
            <span>Discount</span><span id="tDiscount"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom: 2px;">
            <span id="tTaxLabel">Tax</span><span id="tTax"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom: 2px;">
            <span id="tSCLabel">Service Charge</span><span id="tSC"></span>
        </div>

        <hr style="border:none; border-top:1px dashed #000; margin:4px 0;">

        <div style="display:flex; justify-content:space-between; font-weight:900; font-size:13px;">
            <span>Grand Total</span><span id="tTotal"></span>
        </div>


        <div style="text-align:center; margin-top:12px; font-size:10px;">*** Thank You ***</div>

    </div>
</div>




<script>

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatCurrency(n) {
        const num = Number(n) || 0;
        return `Rs. ${num.toFixed(2)}`;
    }

    function downloadPDF() {
        alert('PDF download – integrate with jsPDF or server-side PDF generation.');
    }

    function renderThermalReceipt() {
        if (!invoiceData || !invoiceData.order || !Array.isArray(invoiceData.order.items)) {
            console.error('renderThermalReceipt: invalid invoiceData');
            return;
        }

        const order = invoiceData.order;
        const restaurant = invoiceData.restaurant || {};
        const container = document.getElementById('thermalItems');

        // Restaurant header
        document.getElementById('tRestName').textContent = restaurant.restaurantName || '';

        const addressEl = document.getElementById('tRestAddress');
        if (restaurant.restaurantAddress) {
            addressEl.textContent = restaurant.restaurantAddress;
            addressEl.style.display = '';
        } else {
            addressEl.textContent = '';
            addressEl.style.display = 'none';
        }

        document.getElementById('tRestContact').textContent = restaurant.restaurantContact
            ? `Tel: ${restaurant.restaurantContact}`
            : '';

        // Invoice meta
        document.getElementById('tInvoiceNo').textContent = `Invoice: ${invoiceData.invoiceNumber || ''}`;
        document.getElementById('tTable').textContent = `Table: ${order.tableNumber}`;
        document.getElementById('tOrder').textContent = `Order: #mt-${order.id}`;

        document.getElementById('tDate').textContent = `Date: ${formatDateTime(order.paidAt) || formatDateTime(order.createdAt) || ''}`;

        // Items — build string once, escape names, guard against bad numbers
        let html = '';
        order.items.forEach(item => {
            const qty = Number(item.quantity) || 0;
            const price = Number(item.price) || 0;
            const total = item.total != null ? Number(item.total) : qty * price;

            html += `
            <div class="flex justify-between text-[11px]">
                <span>${escapeHtml(item.menuItem || 'Item')} x${qty}</span>
                <span>${total.toFixed(2)}</span>
            </div>
        `;
        });
        container.innerHTML = html;

        // Totals — pulled from order, not invoiceData directly
        document.getElementById('tSubtotal').textContent = formatCurrency(order.subtotal);

        const discount = Number(order.discountAmount) || 0;
        const discountRow = document.getElementById('tDiscountRow');
        if (discount > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('tDiscount').textContent = `- ${formatCurrency(discount)}`;
        } else {
            discountRow.style.display = 'none';
        }

        document.getElementById('tTaxLabel').textContent = `Tax (${order.taxPercentage ?? 0}%)`;
        document.getElementById('tTax').textContent = formatCurrency(order.taxAmount);

        document.getElementById('tSCLabel').textContent = `Service Charge (${order.serviceChargePercentage ?? 0}%)`;
        document.getElementById('tSC').textContent = formatCurrency(order.serviceCharge);

        document.getElementById('tTotal').textContent = formatCurrency(order.totalAmount);

        document.getElementById('tPaymentMethod').textContent = `Payment Mode: ${capitalize(order.paymentMethod) || ''}`;
        document.getElementById('tPaymentStatus').textContent = `Payment Status: ${capitalize(invoiceData.invoiceNumber ? order.paymentStatus : 'Unpaid') || ''}`;
    }

    function printThermal() {
        if (!invoiceData) {
            console.error('printThermal: invoiceData is missing');
            return;
        }

        const el = document.getElementById('thermalReceipt');
        if (!el) {
            console.error('printThermal: #thermalReceipt element not found in DOM');
            return;
        }

        renderThermalReceipt();

        // Save current title
        const originalTitle = document.title;

        // Set custom print filename
        document.title = `${invoiceData.restaurant.restaurantName}-${invoiceData.invoiceNumber}`;

        window.print();

        // Restore title after printing
        setTimeout(() => {
            document.title = originalTitle;
        }, 1000);
    }
</script>