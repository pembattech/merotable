{{-- ══ CHECKOUT MODAL ══ --}}
<div id="checkoutModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeCheckoutModal()"></div>
    <div
        class="relative bg-white w-full sm:max-w-md sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[92vh]">

        {{-- Drag handle (mobile) --}}
        <div class="flex justify-center pt-3 pb-1 sm:hidden">
            <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 rounded-xl p-2">
                    <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-sm md:text-base">Payment</h2>
                    <p class="text-xs text-gray-400" id="checkoutTableLabel">—</p>
                </div>
            </div>
            <button onclick="closeCheckoutModal()"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 px-4 md:px-5 py-4 md:py-5 space-y-4 md:space-y-5">

            {{-- Total display --}}
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-4 md:p-5 text-white">
                <p class="text-blue-200 text-xs font-semibold uppercase tracking-wider mb-1">Total Amount</p>
                <p class="text-3xl md:text-4xl font-extrabold" id="modalTotal">Rs. 0</p>
            </div>

            {{-- Payment method --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2 md:mb-3">Payment Method</label>
                <div class="grid grid-cols-2 gap-2 md:gap-3">
                    <button class="method-btn selected" onclick="selectMethod(this, 'cash')">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cash
                    </button>
                    <button class="method-btn" onclick="selectMethod(this, 'card')">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Card
                    </button>
                    <button class="method-btn" onclick="selectMethod(this, 'mobile')">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Mobile Pay
                    </button>
                    <button class="method-btn" onclick="selectMethod(this, 'other')">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mx-auto mb-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Other
                    </button>
                </div>
            </div>

            {{-- Cash section --}}
            <div id="cashSection">
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Amount Received</label>
                <div class="relative">
                    <span
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs md:text-sm font-semibold">Rs.</span>
                    <input id="amountReceived" type="number" min="0" placeholder="0" oninput="calculateChange()"
                        class="field-input" style="padding-left: 38px;" />
                </div>
                <div id="changeDisplay" class="mt-3 hidden">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3 md:p-4">
                        <p class="text-xs text-green-600 font-semibold mb-1">Change to Return</p>
                        <p class="text-xl md:text-2xl font-extrabold text-green-700" id="changeAmount">Rs. 0</p>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                    Notes <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <textarea id="paymentNotes" rows="2" placeholder="Add payment notes…"
                    class="field-input resize-none"></textarea>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-4 md:px-5 py-3 md:py-4 border-t border-gray-100 flex-shrink-0 flex gap-3">
            <button onclick="closeCheckoutModal()"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                Cancel
            </button>
            <button onclick="completePayment()" id="completePaymentBtn" class="flex-[2] bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition
                                           shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Complete Payment
            </button>
        </div>

    </div>
</div>


<script>
    // ── Checkout modal ────────────────────────────────────────
    function openCheckoutModal() {
        if (!selectedTable) return;
        document.getElementById('modalTotal').textContent = document.getElementById('total').textContent;
        document.getElementById('checkoutTableLabel').textContent = `Table ${selectedTable.tableNumber}`;
        document.getElementById('amountReceived').value = '';
        document.getElementById('changeDisplay').classList.add('hidden');
        document.getElementById('paymentNotes').value = '';
        document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
        document.querySelector('.method-btn').classList.add('selected');
        selectedMethod = 'cash';
        document.getElementById('cashSection').style.display = 'block';
        document.getElementById('checkoutModal').classList.replace('hidden', 'flex');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.replace('flex', 'hidden');
    }

    function selectMethod(btn, method) {
        document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        selectedMethod = method;
        document.getElementById('cashSection').style.display = method === 'cash' ? 'block' : 'none';
    }

    function calculateChange() {
        const total = parseInt(document.getElementById('total').textContent.replace(/[^\d]/g, '')) || 0;
        const received = parseInt(document.getElementById('amountReceived').value) || 0;
        if (received > 0 && received >= total) {
            document.getElementById('changeAmount').textContent = `Rs. ${(received - total).toLocaleString()}`;
            document.getElementById('changeDisplay').classList.remove('hidden');
        } else {
            document.getElementById('changeDisplay').classList.add('hidden');
        }
    }

    async function completePayment() {
        try {
            const btn = document.querySelector("#completePaymentBtn");

            const order = selectedTable?.orders?.[0];

            if (!order) {
                showToast("No active order found for this table", "error");
                return;
            }

            if (selectedMethod === "cash") {
                const received =
                    Number(document.getElementById("amountReceived")?.value) || 0;

                const totalPayable = grandTotal;

                if (received < totalPayable) {
                    showToast("Amount received is less than total", "error");
                    return;
                }
            }

            const payload = {
                order_id: order.id,
                table_number: selectedTable.tableNumber,
                payment_method: selectedMethod,
            };

            const response = await fetch(`/api/v1/staff/${url}/invoice`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Invoice failed');
            }

            openInvoiceModal(data.data)

            await completeOrder(selectedTable.id, selectedTable.orders[0].id, 'available');

            closeCheckoutModal();
            clearSelection();

            await renderTables();

        } catch (error) {
            console.error(error);
            showToast('Payment failed: ' + error.message, 'error');
        }
    }

    async function completeOrder(tableId, orderId) {
        try {
            // 1. Update table status
            const tableRes = await fetch(`/api/v1/staff/${url}/table/${tableId}/status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: 'available'
                })
            });

            const tableData = await tableRes.json();

            if (!tableData.success) {
                showToast(tableData.message || 'Failed to update table status.', 'error');
                return;
            }

            // 2. Update order status
            const orderRes = await fetch(`/api/v1/staff/${url}/table/${tableId}/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    status: 'completed'
                })
            });

            const orderData = await orderRes.json();

            if (orderData.success) {
                showToast(
                    'Payment successful. Order closed and table is now available.',
                    'success'
                );
            } else {
                showToast(orderData.message || 'Failed to close order.', 'error');
            }

        } catch (error) {
            console.error('Error completing order:', error);
            showToast('Something went wrong. Please try again.', 'error');
        }
    }
</script>