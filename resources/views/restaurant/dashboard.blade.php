@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')

    <style>
        /* ── Subscription pill ── */
        .subscription-pill {
            display: flex;
            align-items: stretch;
            border-radius: 10px;
            border: 1.5px solid #e0e3ef;
            background: #fff;
            overflow: hidden;
            cursor: default;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .subscription-pill:hover {
            border-color: #c5c9dc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .plan-label {
            padding: 7px 12px;
            background: #2563eb;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }

        .plan-tier {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1;
        }

        .plan-sub {
            font-size: 10px;
            font-weight: 500;
            opacity: 0.75;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .expiry-section {
            padding: 7px 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }

        .expiry-top {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9399b0;
            line-height: 1;
        }

        .expiry-days {
            font-size: 13px;
            font-weight: 700;
            color: #e67700;
            letter-spacing: -0.2px;
            line-height: 1;
        }

        /* ── Restaurant status ── */
        .restaurant-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1.5px solid #bbf7d0;
            background: #f0fdf4;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
            user-select: none;
        }

        .restaurant-status:hover {
            background: #dcfce7;
            border-color: #86efac;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.12);
        }

        .status-indicator {
            position: relative;
            width: 8px;
            height: 8px;
            flex-shrink: 0;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
            position: absolute;
            inset: 0;
        }

        .status-ring {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 2px solid #16a34a;
            position: absolute;
            inset: 0;
            animation: ring-pulse 2s ease-out infinite;
            opacity: 0;
        }

        @keyframes ring-pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            100% {
                transform: scale(2.6);
                opacity: 0;
            }
        }

        .status-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .status-sub {
            font-size: 10px;
            font-weight: 500;
            color: #4ade80;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-title {
            font-size: 12px;
            font-weight: 700;
            color: #15803d;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Notification button ── */
        .notif-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1.5px solid #e0e3ef;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, box-shadow 0.2s, transform 0.15s;
        }

        .notif-btn:hover {
            background: #f5f6fb;
            border-color: #c5c9dc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        }

        .notif-btn:active {
            transform: scale(0.95);
        }

        .notif-btn svg {
            width: 17px;
            height: 17px;
            color: #5b6080;
            transition: color 0.2s;
        }

        .notif-btn:hover svg {
            color: #2563eb;
        }

        .notif-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid #fff;
        }
    </style>

    <header class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-extrabold text-gray-800">Restaurant Overview</h1>
        <div class="flex items-center space-x-4">

            <!-- Subscription -->
            <div class="subscription-pill" title="Your current plan">
                <div class="plan-label">
                    <span class="plan-tier" id="planTier">N/A</span>
                    <span class="plan-sub">Plan</span>
                </div>
                <div class="expiry-section">
                    <span class="expiry-top">Expires in</span>
                    <span class="expiry-days" id="expiryDays">N/A days</span>
                </div>
            </div>

            <!-- Restaurant Status -->
            <div class="restaurant-status">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <div class="status-ring"></div>
                </div>
                <div class="status-text">
                    <span class="status-sub">Restaurant</span>
                    <span class="status-title">Open</span>
                </div>
            </div>

            <!-- Notifications -->
            <button class="notif-btn" aria-label="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="notif-badge"></span>
            </button>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Today's Revenue</p>
            <h3 class="text-3xl font-bold mt-2">Rs. 12,450</h3>
            <p class="text-xs text-green-600 mt-2 font-bold">↑ 12% from yesterday</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Active Orders</p>
            <h3 class="text-3xl font-bold mt-2">08</h3>
            <p class="text-xs text-blue-600 mt-2 font-bold">Live tracking active</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Occupied Tables</p>
            <h3 class="text-3xl font-bold mt-2">5/15</h3>
            <p class="text-xs text-gray-400 mt-2 font-bold">Standard Capacity</p>
        </div>
    </div>

    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Live Table Map</h2>
            <button class="text-sm text-blue-600 font-bold hover:underline">Refresh Status</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg border-2 border-blue-700">
                <div class="flex justify-between items-start">
                    <span class="text-2xl font-black">T-01</span>
                    <span
                        class="text-[10px] bg-blue-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Occupied</span>
                </div>
                <div class="mt-4">
                    <p class="text-xs opacity-80">Order Amount</p>
                    <p class="font-bold">Rs. 850</p>
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl border border-gray-200 hover:border-blue-400 transition cursor-pointer group">
                <span class="text-2xl font-black text-gray-300 group-hover:text-blue-500">T-02</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>

            <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg border-2 border-blue-700">
                <div class="flex justify-between items-start">
                    <span class="text-2xl font-black">T-03</span>
                    <span
                        class="text-[10px] bg-blue-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Occupied</span>
                </div>
                <div class="mt-4">
                    <p class="text-xs opacity-80">Order Amount</p>
                    <p class="font-bold">Rs. 1,200</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <span class="text-2xl font-black text-gray-300">T-04</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <span class="text-2xl font-black text-gray-300">T-05</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', async function() {

            const token = localStorage.getItem('auth_token');

            async function loadRestro() {
                try {
                    const res = await fetch(`/api/v1/owner/restaurant`, {
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

                    console.log('Restaurant Data:', data);

                    const subscription = data.data.active_subscription;

                    if (!subscription) return;

                    // If no subscription or expired, redirect to pricing page
                    if (!subscription || subscription.daysLeft <= 0) {
                        window.location.href = "/pricing";
                        return;
                    }

                    const planTier = subscription.plan?.name ?? 'No Plan';
                    const expiryDays = subscription.daysLeft ?? 0;

                    console.log(planTier, expiryDays);

                    document.getElementById('planTier').textContent = planTier;
                    document.getElementById('expiryDays').textContent = expiryDays;

                } catch (error) {
                    console.error('Error:', error);
                    showToast('Network error ❌', 'error');
                }
            }

            await loadRestro();

        });
    </script>

@endsection
