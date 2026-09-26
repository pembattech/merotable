<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Bill — Table {{ $order->table->table_number }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6 font-mono text-slate-800">

    <div class="w-full max-w-[300px]">

        <div class="bg-white rounded-lg p-6 relative">

            <div class="text-center mb-3">
                <p class="font-sans font-semibold text-sm">{{ $restaurant->name }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Table {{ $order->table->table_number }} &middot; Order #mt-{{ $order->id }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ $order->bill_printed_at?->format('d M Y, g:i A') ?? now()->format('d M Y, g:i A') }}
                </p>
            </div>

            <div class="border-t border-dashed border-slate-300 my-2.5"></div>

            <div class="space-y-1.5 text-xs">
                @foreach ($order->orderItems as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->menuItem->name }} x{{ $item->quantity }}</span>
                        <span>{{ number_format($item->quantity * $item->price, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-dashed border-slate-300 my-2.5"></div>

            @php
                $subtotal = $order->orderItems->sum(fn ($i) => $i->quantity * $i->price);
                $tax = $subtotal * 0.10;
                $service = $subtotal * 1.00;
            @endphp

            <div class="space-y-1 text-xs text-slate-500">
                <div class="flex justify-between"><span>Subtotal</span><span>Rs. {{ number_format($subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span>Tax (10%)</span><span>Rs. {{ number_format($tax, 2) }}</span></div>
                <div class="flex justify-between"><span>Service charge</span><span>Rs. {{ number_format($service, 2) }}</span></div>
            </div>

            <div class="border-t border-dashed border-slate-300 my-2.5"></div>

            <div class="flex justify-between font-sans font-semibold text-sm mb-3">
                <span>Total</span>
                <span>Rs. {{ number_format($order->total_amount, 2) }}</span>
            </div>

            <p class="text-center text-[10px] text-slate-400 font-sans">Not a payment receipt</p>

            <button
                onclick="window.print()"
                class="no-print w-full mt-3.5 h-9 rounded-lg border border-slate-300 font-sans text-sm font-medium hover:bg-slate-50"
            >
                Print
            </button>

        </div>

    </div>

</body>
</html>