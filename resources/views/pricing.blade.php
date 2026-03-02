<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mero Table — Renew Subscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Lora', 'serif'],
                        body: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(16px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        },
                        pulse2: {
                            '0%,100%': {
                                opacity: '1'
                            },
                            '50%': {
                                opacity: '.5'
                            },
                        }
                    },
                    animation: {
                        'fade-up': 'fadeUp .5s ease both',
                        'fade-up-2': 'fadeUp .5s .1s ease both',
                        'fade-up-3': 'fadeUp .5s .2s ease both',
                        'fade-up-4': 'fadeUp .5s .3s ease both',
                        'blink': 'pulse2 2s ease-in-out infinite',
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .font-display {
            font-family: 'Lora', serif;
        }

        .plan-card-premium {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .plan-card-platinum {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen text-slate-800">

    <!-- ─── NAV ─── -->
    <nav
        class="bg-white border-b border-slate-100 px-6 md:px-10 py-4 flex items-center justify-between sticky top-0 z-50">
        <a href="#" class="flex items-center gap-2.5">
            <div class="w-9 h-9 bg-brand-600 rounded-xl flex items-center justify-center text-lg shadow-sm">🍽️</div>
            <span class="font-display text-xl font-700 text-slate-900">Mero <span
                    class="text-brand-600">Table</span></span>
        </a>
        <div class="flex items-center gap-2.5 bg-slate-100 rounded-xl px-3.5 py-2">
            <div class="w-7 h-7 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-700">
                SR</div>
            <div>
                <p class="text-sm font-600 text-slate-800 leading-none" id="restro_name">...loading</p>
                <p class="text-xs text-slate-400 mt-0.5 leading-none"> <span id="subscription_plan">...loading</span>
                    Plan · <span class="text-red-500 font-500" id="subscription_status">Expired</span></p>
            </div>
        </div>
    </nav>

    <!-- ─── EXPIRY ALERT BANNER ─── -->
    <div
        class="bg-red-600 text-white px-6 md:px-10 py-3.5 flex flex-wrap items-center justify-between gap-3 animate-fade-up">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm flex-shrink-0">🔒
            </div>
            <div>
                <p class="font-600 text-sm">Your <strong>Basic Plan</strong> has expired</p>
                <p class="text-xs text-red-200">Sajha Restro is currently paused — renew to go live again.</p>
            </div>
        </div>
        <span class="text-xs bg-white/15 border border-white/25 rounded-lg px-3 py-1.5 font-500 whitespace-nowrap">
            Expired: <strong>Falgun 15, 2081</strong> &nbsp;·&nbsp; Feb 27, 2025
        </span>
    </div>

    <!-- ─── MAIN CONTENT ─── -->
    <main class="max-w-6xl mx-auto px-5 py-14">

        <!-- Heading block -->
        <div class="text-center mb-12 animate-fade-up-2">
            <p
                class="inline-flex items-center gap-1.5 text-xs font-600 tracking-widest uppercase text-brand-600 bg-brand-50 border border-brand-100 rounded-full px-3.5 py-1.5 mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-blink inline-block"></span>
                Subscription Required
            </p>
            <h1 class="font-display text-4xl md:text-5xl font-700 text-slate-900 leading-tight mb-4">
                Renew your<br /><em class="not-italic text-brand-600">Basic Plan</em>
            </h1>
            <p class="text-slate-500 font-300 text-lg max-w-md mx-auto leading-relaxed">
                Pick a billing cycle and reactivate Sajha Restro — your menu, tables, and settings are all preserved.
            </p>
        </div>

        <!-- ─── BILLING TOGGLE ─── -->
        <div class="flex justify-center mb-10 animate-fade-up-3">
            <div class="bg-white border border-slate-200 rounded-2xl p-1.5 flex gap-1 shadow-sm">
                <button id="btn-semi" onclick="setBilling('semi')"
                    class="px-5 py-2.5 rounded-xl text-sm font-500 text-slate-400 transition-all duration-200 hover:text-slate-700">Semiannually</button>
                <button id="btn-yearly" onclick="setBilling('yearly')"
                    class="px-5 py-2.5 rounded-xl text-sm font-600 bg-brand-600 text-white shadow-sm transition-all duration-200">Yearly
                    <span class="ml-1.5 text-xs bg-yellow-300 text-yellow-900 font-700 px-1.5 py-0.5 rounded-full">Save
                        10%</span></button>
            </div>
        </div>

        <!-- ─── PLAN CARDS ─── -->
        <div class="grid md:grid-cols-3 gap-5 animate-fade-up-4 mb-8">

            <!-- BASIC (current plan) -->
            <div
                class="relative bg-white rounded-3xl border-2 border-brand-600 shadow-xl shadow-brand-500/10 p-8 flex flex-col">
                <div
                    class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-brand-600 text-white text-xs font-700 tracking-widest uppercase px-4 py-1.5 rounded-full whitespace-nowrap shadow">
                    ✦ Your Plan
                </div>
                <div class="mb-6">
                    <h2 class="font-display text-2xl font-700 text-slate-900 mb-1">Basic</h2>
                    <p class="text-sm text-slate-400">Everything you had before — restored instantly.</p>
                </div>
                <div class="mb-1">
                    <div class="flex items-end gap-1.5">
                        <span class="text-lg font-600 text-slate-500">Rs</span>
                        <span id="basic-amt"
                            class="font-display text-5xl font-700 text-slate-900 leading-none">8,100</span>
                        <span id="basic-period" class="text-sm text-slate-400 mb-1">/year</span>
                    </div>
                    <p id="basic-orig" class="text-sm text-slate-400 line-through mt-1">Rs 9,000</p>
                </div>
                <p id="basic-daily"
                    class="text-xs text-brand-600 font-500 mb-6 bg-brand-50 border border-brand-100 rounded-lg px-3 py-1.5 self-start">
                    ≈ Rs 22 / day
                </p>
                <button
                    onclick="openCheckout('basic', 'Basic', document.getElementById('basic-amt').textContent, document.getElementById('basic-period').textContent)"
                    class="renew-btn w-full bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-600 text-base rounded-xl py-3.5 transition-all duration-150 shadow-lg shadow-brand-500/30 mb-3">
                    Renew Basic Plan →
                </button>
                <p class="text-xs text-slate-400 text-center">Activates immediately · Cancel anytime</p>
                <div class="h-px bg-slate-100 my-6"></div>
                <p class="text-xs font-700 tracking-widest uppercase text-slate-400 mb-3">What you get back</p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-2.5 text-sm text-slate-600"><span
                            class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Up
                        to <strong class="font-600 mx-1">15 tables</strong></li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-600"><span
                            class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Up
                        to <strong class="font-600 mx-1">5 staff accounts</strong></li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-600"><span
                            class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>QR
                        Code ordering</li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-600"><span
                            class="w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Analytics
                        dashboard</li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xs flex-shrink-0">✕</span>Export
                        reports</li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center text-xs flex-shrink-0">✕</span>Priority
                        support</li>
                </ul>
            </div>

            <!-- PREMIUM -->
            <div class="relative plan-card-premium rounded-3xl border border-slate-700 shadow-xl p-8 flex flex-col">
                <div
                    class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-400 text-amber-900 text-xs font-700 tracking-widest uppercase px-4 py-1.5 rounded-full whitespace-nowrap shadow">
                    ⭐ Most Popular
                </div>
                <div class="mb-6">
                    <h2 class="font-display text-2xl font-700 text-white mb-1">Premium</h2>
                    <p class="text-sm text-slate-400">Scale your restaurant with powerful tools.</p>
                </div>
                <div class="mb-1">
                    <div class="flex items-end gap-1.5">
                        <span class="text-lg font-600 text-slate-400">Rs</span>
                        <span id="premium-amt"
                            class="font-display text-5xl font-700 text-white leading-none">18,000</span>
                        <span id="premium-period" class="text-sm text-slate-400 mb-1">/year</span>
                    </div>
                    <p id="premium-orig" class="text-sm text-slate-500 line-through mt-1">Rs 20,000</p>
                </div>
                <p id="premium-daily"
                    class="text-xs text-amber-400 font-500 mb-6 bg-amber-400/10 border border-amber-400/20 rounded-lg px-3 py-1.5 self-start">
                    ≈ Rs 49 / day
                </p>
                <button
                    onclick="openCheckout('premium', 'Premium', document.getElementById('premium-amt').textContent, document.getElementById('premium-period').textContent)"
                    class="renew-btn w-full bg-amber-400 hover:bg-amber-300 active:scale-95 text-amber-900 font-700 text-base rounded-xl py-3.5 transition-all duration-150 shadow-lg shadow-amber-400/20 mb-3">
                    Upgrade to Premium →
                </button>
                <p class="text-xs text-slate-500 text-center">Activates immediately · Cancel anytime</p>
                <div class="h-px bg-slate-700 my-6"></div>
                <p class="text-xs font-700 tracking-widest uppercase text-slate-500 mb-3">Everything in Basic, plus</p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Up
                        to <strong class="font-600 mx-1 text-white">50 tables</strong></li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Up
                        to <strong class="font-600 mx-1 text-white">20 staff accounts</strong></li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Export
                        reports</li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-300"><span
                            class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-400 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Priority
                        support</li>
                    <li class="flex items-center gap-2.5 text-sm text-slate-500"><span
                            class="w-5 h-5 rounded-full bg-slate-700 text-slate-500 flex items-center justify-center text-xs flex-shrink-0">✕</span>Dedicated
                        account manager</li>
                </ul>
            </div>

            <!-- PLATINUM -->
            <div
                class="relative plan-card-platinum rounded-3xl border border-indigo-600/50 shadow-xl p-8 flex flex-col">
                <div
                    class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-violet-400 to-indigo-400 text-white text-xs font-700 tracking-widest uppercase px-4 py-1.5 rounded-full whitespace-nowrap shadow">
                    💎 Enterprise
                </div>
                <div class="mb-6">
                    <h2 class="font-display text-2xl font-700 text-white mb-1">Platinum</h2>
                    <p class="text-sm text-indigo-300">For large restaurants & chains.</p>
                </div>
                <div class="mb-1">
                    <div class="flex items-end gap-1.5">
                        <span class="text-lg font-600 text-indigo-300">Rs</span>
                        <span id="platinum-amt"
                            class="font-display text-5xl font-700 text-white leading-none">36,000</span>
                        <span id="platinum-period" class="text-sm text-indigo-300 mb-1">/year</span>
                    </div>
                    <p id="platinum-orig" class="text-sm text-indigo-400/60 line-through mt-1">Rs 40,000</p>
                </div>
                <p id="platinum-daily"
                    class="text-xs text-violet-300 font-500 mb-6 bg-violet-400/10 border border-violet-400/20 rounded-lg px-3 py-1.5 self-start">
                    ≈ Rs 99 / day
                </p>
                <button
                    onclick="openCheckout('platinum', 'Platinum', document.getElementById('platinum-amt').textContent, document.getElementById('platinum-period').textContent)"
                    class="renew-btn w-full bg-gradient-to-r from-violet-500 to-indigo-500 hover:from-violet-400 hover:to-indigo-400 active:scale-95 text-white font-700 text-base rounded-xl py-3.5 transition-all duration-150 shadow-lg shadow-violet-500/20 mb-3">
                    Upgrade to Platinum →
                </button>
                <p class="text-xs text-indigo-400/60 text-center">Activates immediately · Cancel anytime</p>
                <div class="h-px bg-indigo-700/50 my-6"></div>
                <p class="text-xs font-700 tracking-widest uppercase text-indigo-400/60 mb-3">Everything in Premium,
                    plus</p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-2.5 text-sm text-indigo-200"><span
                            class="w-5 h-5 rounded-full bg-violet-400/20 text-violet-300 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Unlimited
                        tables</li>
                    <li class="flex items-center gap-2.5 text-sm text-indigo-200"><span
                            class="w-5 h-5 rounded-full bg-violet-400/20 text-violet-300 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Unlimited
                        staff accounts</li>
                    <li class="flex items-center gap-2.5 text-sm text-indigo-200"><span
                            class="w-5 h-5 rounded-full bg-violet-400/20 text-violet-300 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Dedicated
                        account manager</li>
                    <li class="flex items-center gap-2.5 text-sm text-indigo-200"><span
                            class="w-5 h-5 rounded-full bg-violet-400/20 text-violet-300 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Custom
                        integrations</li>
                    <li class="flex items-center gap-2.5 text-sm text-indigo-200"><span
                            class="w-5 h-5 rounded-full bg-violet-400/20 text-violet-300 flex items-center justify-center text-xs font-700 flex-shrink-0">✓</span>Multi-branch
                        support</li>
                </ul>
            </div>

        </div>

        <!-- Data safety card (full width below cards) -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 animate-fade-up-4">
            <div class="flex items-start gap-3">
                <div class="text-2xl mt-0.5">⏳</div>
                <div class="flex-1">
                    <p class="font-600 text-amber-900 text-sm mb-1">Your data is safe — for now</p>
                    <p class="text-xs text-amber-700 leading-relaxed">Menu items, table layouts, and order history are
                        preserved for <strong>30 days</strong>. Renew before <strong>Chaitra 15, 2081</strong> to avoid
                        data loss.</p>
                    <div class="mt-3 flex items-center gap-2 max-w-sm">
                        <div class="flex-1 bg-amber-200 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: 57%"></div>
                        </div>
                        <span class="text-xs font-700 text-amber-800">13 days left</span>
                    </div>
                </div>
                <div class="text-center hidden md:block">
                    <p class="text-xs text-slate-400">Questions? <a href="tel:014000000"
                            class="text-brand-600 font-500 hover:underline">01-4xxxxxx</a> · <a
                            href="mailto:support@merotable.com.np"
                            class="text-brand-600 font-500 hover:underline">support@merotable.com.np</a></p>
                </div>
            </div>
        </div>
    </main>

    <!-- ─── CHECKOUT FORM OVERLAY ─── -->
    <div id="checkoutOverlay"
        class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative animate-fade-up">
            <!-- Close -->
            <button onclick="closeCheckout()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors text-sm">✕</button>

            <!-- Header -->
            <div class="mb-6">
                <p class="text-xs font-600 tracking-widest uppercase text-brand-600 mb-1">Complete Your Order</p>
                <h3 class="font-display text-2xl font-700 text-slate-900">
                    Renew <span id="checkout-plan-name">Basic</span> Plan
                </h3>
                <p class="text-sm text-slate-400 mt-1">
                    <span id="checkout-plan-amount" class="font-600 text-slate-700">Rs 8,100</span>
                    <span id="checkout-plan-period">/year</span>
                </p>
            </div>

            <form id="paymentForm">
                <!-- Hidden fields -->
                <input type="hidden" id="plan_id" name="plan_id" />
                <input type="hidden" id="plan_duration" name="plan_duration" />
                <input type="hidden" id="amount" name="amount" />
                <input type="hidden" id="type" name="type" value="renew" />

                <!-- Reference ID -->
                <div class="mb-4">
                    <label class="block text-sm font-600 text-slate-700 mb-1.5" for="referenceId">Reference /
                        Transaction ID</label>
                    <input id="referenceId" name="referenceId" type="text" placeholder="e.g. TXN123456789"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
                        required />
                </div>

                <!-- Payment Method -->
                <div class="mb-6">
                    <label class="block text-sm font-600 text-slate-700 mb-1.5" for="paymentMethod">Payment
                        Method</label>
                    <select id="paymentMethod" name="paymentMethod"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition bg-white"
                        required>
                        <option value="">Select payment method</option>
                        <option value="esewa">💚 eSewa</option>
                        <option value="khalti">💜 Khalti</option>
                        <option value="connectips">🏦 ConnectIPS</option>
                        <option value="bank_transfer">💳 Bank Transfer</option>
                    </select>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-700 text-base rounded-xl py-3.5 transition-all duration-150 shadow-lg shadow-brand-500/30">
                    Confirm Payment →
                </button>
                <p class="text-xs text-slate-400 text-center mt-3">Your subscription activates immediately after
                    confirmation.</p>
            </form>
        </div>
    </div>

    <!-- ─── FOOTER ─── -->
    <footer class="bg-slate-900 text-slate-500 text-center py-8 text-xs leading-loose">
        <p><strong class="text-slate-300">Mero Table</strong> — Restaurant Management Software · Kathmandu, Nepal 🇳🇵
        </p>
        <p class="mt-1">
            <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a> ·
            <a href="#" class="hover:text-slate-300 transition-colors ml-2">Terms</a> ·
            <a href="#" class="hover:text-slate-300 transition-colors ml-2">Contact</a>
        </p>
        <p class="mt-1">© 2025 Mero Table. All rights reserved.</p>
    </footer>

    <script>
        // Plan data
        const plans = {
            basic: {
                semi: {
                    amt: '4,500',
                    amtRaw: '4500',
                    orig: '',
                    period: '/6 months',
                    duration: 'semiannually',
                    daily: '≈ Rs 25 / day'
                },
                yearly: {
                    amt: '8,100',
                    amtRaw: '8100',
                    orig: 'Rs 9,000',
                    period: '/year',
                    duration: 'annually',
                    daily: '≈ Rs 22 / day'
                },
            },
            premium: {
                semi: {
                    amt: '10,000',
                    amtRaw: '10000',
                    orig: '',
                    period: '/6 months',
                    duration: 'semiannually',
                    daily: '≈ Rs 55 / day'
                },
                yearly: {
                    amt: '18,000',
                    amtRaw: '18000',
                    orig: 'Rs 20,000',
                    period: '/year',
                    duration: 'annually',
                    daily: '≈ Rs 49 / day'
                },
            },
            platinum: {
                semi: {
                    amt: '21,000',
                    amtRaw: '21000',
                    orig: '',
                    period: '/6 months',
                    duration: 'semiannually',
                    daily: '≈ Rs 115 / day'
                },
                yearly: {
                    amt: '36,000',
                    amtRaw: '36000',
                    orig: 'Rs 40,000',
                    period: '/year',
                    duration: 'annually',
                    daily: '≈ Rs 99 / day'
                },
            },
        };

        function capitalize(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        }

        let currentBilling = 'yearly';

        function setBilling(mode) {
            currentBilling = mode;

            // Update basic
            const b = plans.basic[mode];
            document.getElementById('basic-amt').textContent = b.amt;
            document.getElementById('basic-orig').textContent = b.orig;
            document.getElementById('basic-period').textContent = b.period;
            document.getElementById('basic-daily').textContent = b.daily;

            // Update premium
            const p = plans.premium[mode];
            document.getElementById('premium-amt').textContent = p.amt;
            document.getElementById('premium-orig').textContent = p.orig;
            document.getElementById('premium-period').textContent = p.period;
            document.getElementById('premium-daily').textContent = p.daily;

            // Update platinum
            const pl = plans.platinum[mode];
            document.getElementById('platinum-amt').textContent = pl.amt;
            document.getElementById('platinum-orig').textContent = pl.orig;
            document.getElementById('platinum-period').textContent = pl.period;
            document.getElementById('platinum-daily').textContent = pl.daily;

            // Toggle button styles
            const btnSemi = document.getElementById('btn-semi');
            const btnYearly = document.getElementById('btn-yearly');
            if (mode === 'yearly') {
                btnYearly.classList.add('bg-brand-600', 'text-white', 'font-600', 'shadow-sm');
                btnYearly.classList.remove('text-slate-400', 'font-500');
                btnSemi.classList.remove('bg-brand-600', 'text-white', 'font-600', 'shadow-sm');
                btnSemi.classList.add('text-slate-400', 'font-500');
            } else {
                btnSemi.classList.add('bg-brand-600', 'text-white', 'font-600', 'shadow-sm');
                btnSemi.classList.remove('text-slate-400', 'font-500');
                btnYearly.classList.remove('bg-brand-600', 'text-white', 'font-600', 'shadow-sm');
                btnYearly.classList.add('text-slate-400', 'font-500');
            }
        }

        function openCheckout(planKey, planLabel, displayAmt, displayPeriod) {
            const data = plans[planKey][currentBilling];

            document.getElementById('plan_id').value = planKey;
            document.getElementById('plan_duration').value = data.duration;
            document.getElementById('amount').value = data.amtRaw;
            document.getElementById('type').value = 'subscription';

            document.getElementById('checkout-plan-name').textContent = planLabel;
            document.getElementById('checkout-plan-amount').textContent = 'Rs ' + data.amt;
            document.getElementById('checkout-plan-period').textContent = data.period;

            document.getElementById('checkoutOverlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCheckout() {
            document.getElementById('checkoutOverlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on backdrop click
        document.getElementById('checkoutOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeCheckout();
        });

        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('auth_token');

            const payload = {
                plan_name: capitalize(document.getElementById('plan_id').value),
                billing_cycle: document.getElementById('plan_duration').value,
                amount: document.getElementById('amount').value,
                type: document.getElementById('type').value,
                reference_id: document.getElementById('referenceId').value,
                payment_method: document.getElementById('paymentMethod').value
            };

            try {
                const res = await fetch('/api/v1/owner/restaurant/subscription/transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    alert(data.message || 'Transaction failed ❌');
                    return;
                }

                this.reset();
                closeCheckout();
                window.location.href = '/payment-success';

            } catch (error) {
                alert('Server error ❌');
                console.error(error);
            }
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', async function() {

            const token = localStorage.getItem('auth_token');
            const restro_name = localStorage.getItem('user_name');


            async function loadRestro() {
                try {
                    const res = await fetch(`/api/v1/owner/restaurant/subscription/expired`, {
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


                    console.log(data.data)

                    document.getElementById('restro_name').textContent = restro_name;
                    document.getElementById('subscription_plan').textContent = data.data.subscription.plan.name;
                    document.getElementById('subscription_status').textContent = data.data.subscription.status;

                } catch (error) {
                    console.error('Error:', error);
                    showToast('Network error ❌', 'error');
                }
            }

            await loadRestro();

        });
    </script>
</body>

</html>
