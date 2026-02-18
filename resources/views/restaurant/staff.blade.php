@extends('layouts.app')

@section('title', 'Staff | ' . config('app.name'))

@section('content')

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

        .d1 {
            animation-delay: .04s
        }

        .d2 {
            animation-delay: .08s
        }

        .d3 {
            animation-delay: .12s
        }

        .d4 {
            animation-delay: .16s
        }

        .d5 {
            animation-delay: .20s
        }

        .d6 {
            animation-delay: .24s
        }

        .d7 {
            animation-delay: .28s
        }

        .d8 {
            animation-delay: .32s
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-panel {
            animation: slideUp 0.26s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        /* card hover */
        .staff-card {
            background: #fff;
            border: 1px solid #e9eef5;
            border-radius: 16px;
            padding: 20px;
            cursor: pointer;
            transition: box-shadow 0.18s, transform 0.18s, border-color 0.18s;
        }

        .staff-card:hover {
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.10);
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        /* avatar colors */
        .av-0 {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .av-1 {
            background: #ede9fe;
            color: #6d28d9;
        }

        .av-2 {
            background: #dcfce7;
            color: #15803d;
        }

        .av-3 {
            background: #fef3c7;
            color: #b45309;
        }

        .av-4 {
            background: #fce7f3;
            color: #be185d;
        }

        .av-5 {
            background: #e0f2fe;
            color: #0369a1;
        }

        /* field input */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        /* toggle */
        .tog-track {
            width: 40px;
            height: 22px;
            background: #e5e7eb;
            border-radius: 999px;
            position: relative;
            cursor: pointer;
            transition: background .2s;
        }

        .tog-track.on {
            background: #3b82f6;
        }

        .tog-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            transition: transform .2s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        }

        .tog-track.on .tog-thumb {
            transform: translateX(18px);
        }

        /* scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        .modal-scroll::-webkit-scrollbar {
            width: 4px;
        }
    </style>

    {{-- TODO: backend: add new staff table and clarify `active` field to separate online vs on-duty status
        -s.account_status   // active / inactive
        s.duty_status      // on_duty / off_duty
        s.online_status    // online / offline --}}


    <!-- ── HEADER ── -->
    <div class="flex items-center justify-between mb-7 fade-up d1">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Staff Members</h1>
            <p class="text-sm text-gray-400 mt-1">Momo House · <span id="staffCount"></span> members</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- search -->
            <div class="relative hidden sm:block">
                <input id="searchInput" type="text" placeholder="Search staff..." oninput="filterStaff()"
                    class="pl-9 pr-4 py-2.5 border border-gray-200 bg-white rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none w-52 transition">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <!-- filter -->
            <div class="relative">
                <select id="roleFilter" onchange="filterStaff()"
                    class="appearance-none pl-3 pr-8 py-2.5 border border-gray-200 bg-white rounded-xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none cursor-pointer transition">
                    <option value="">All Roles</option>
                    <option value="manager">Manager</option>
                    <option value="waiter">Waiter</option>
                    <option value="cashier">Cashier</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="staff">Staff</option>
                </select>
                <svg class="pointer-events-none absolute right-2.5 top-3 h-4 w-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <!-- add -->
            <button onclick="openModal()"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-blue-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Staff
            </button>
        </div>
    </div>

    <!-- ── SUMMARY STRIPS ── -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 fade-up d2">
        <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Total</p>
                <p class="text-lg font-extrabold text-gray-800" id="totalCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Active</p>
                <p class="text-lg font-extrabold text-gray-800" id="activeCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center">
                <div class="w-3 h-3 rounded-full bg-orange-400"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Off Duty</p>
                <p class="text-lg font-extrabold text-gray-800" id="offCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Roles</p>
                <p class="text-lg font-extrabold text-gray-800">5</p>
            </div>
        </div>
    </div>

    <!-- ── STAFF GRID ── -->
    <div id="staffGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <!-- empty state -->
    <div id="emptyState" class="hidden text-center py-20">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <p class="font-semibold text-gray-400">No staff found</p>
        <p class="text-sm text-gray-300 mt-1">Try adjusting your search or filter</p>
    </div>



    <!-- ══════════════════════════════════════════════
                                                                 ADD / EDIT STAFF MODAL
                                                            ══════════════════════════════════════════════ -->
    <div id="staffModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div
            class="modal-panel relative bg-white w-full sm:max-w-md sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[92vh]">

            <!-- header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 id="modalTitle" class="font-bold text-gray-800 text-base leading-tight">Add Staff Member</h2>
                        <p class="text-xs text-gray-400">Fill in the details below</p>
                    </div>
                </div>
                <button onclick="closeModal()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-2 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- body -->
            <div class="overflow-y-auto modal-scroll flex-1 px-5 py-5 space-y-4">

                <form>

                    <!-- avatar preview -->
                    <div class="flex justify-center mb-2">
                        <div id="modalAvatar"
                            class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl font-extrabold av-0">?
                        </div>
                    </div>

                    <!-- name -->
                    <div>
                        <label for="fName" class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                        <input id="fName" type="text" placeholder="e.g. Pemba Tamang" class="field-input"
                            oninput="updateAvatar(); clearErr('fName')" />
                        <p class="field-error text-red-500 text-sm" id="err-fName">
                            {{-- <span></span> --}}
                        </p>
                    </div>

                    <!-- email -->
                    <div>
                        <label for="fEmail" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input id="fEmail" type="email" placeholder="staff@example.com" class="field-input"
                            oninput="clearErr('fEmail')" />
                        <p class="field-error text-red-500 text-sm" id="err-fEmail">
                            {{-- <span></span> --}}
                        </p>
                    </div>

                    <!-- role -->
                    <div>
                        <label for="fRole" class="block text-sm font-semibold text-gray-700 mb-1.5">Role</label>
                        <div class="relative">
                            <select id="fRole" class="field-input appearance-none pr-10"
                                onchange="clearErr('fRole')">
                                <option value="" disabled selected>Select a role</option>
                                <option value="waiter">Waiter</option>
                                <option value="cashier">Cashier</option>
                                <option value="kitchen">Kitchen</option>
                                <option value="captain">Captain</option>
                                <option value="manager">Manager</option>
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <p class="field-error text-red-500 text-sm" id="err-fRole">
                            {{-- <span></span> --}}
                        </p>
                    </div>

                    <!-- phone -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="fPhone" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone</label>
                            <input id="fPhone" type="tel" placeholder="98XXXXXXXX" class="field-input"
                                oninput="clearErr('fPhone')" />
                            <p class="field-error text-red-500 text-sm" id="err-fPhone">
                                {{-- <span></span> --}}
                            </p>
                        </div>
                    </div>

                    <!-- password section — only shown when adding new staff -->
                    <div id="passwordSection">
                        <div class="flex items-center gap-3 my-1">
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Login
                                Credentials</span>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>

                        <!-- password -->
                        <div class="mt-4">
                            <label for ="fPassword"
                                class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <div class="pw-wrap">
                                <input id="fPassword" type="password" placeholder="Min. 8 characters"
                                    class="field-input" />
                            </div>

                            <p class="field-error text-red-500 text-sm" id="err-fPassword">
                                {{-- <span></span> --}}
                            </p>
                        </div>

                        <!-- confirm password -->
                        <div class="mt-4">
                            <label for="fPasswordConfirm" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm
                                Password</label>
                            <div class="pw-wrap">
                                <input id="fPasswordConfirm" type="password" placeholder="Re-enter password"
                                    class="field-input" />
                            </div>
                            <p class="field-error text-red-500 text-sm" id="err-fPasswordConfirm">
                                {{-- <span></span> --}}
                            </p>
                        </div>
                    </div>

                    <!-- status toggle -->
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Active Status</p>
                            <p class="text-xs text-gray-400">Staff can log in and take orders</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div id="fStatusTog" class="tog-track on" onclick="toggleStatus()">
                                <div class="tog-thumb"></div>
                            </div>
                            <span id="fStatusLabel" class="text-sm font-semibold text-blue-600">Active</span>
                        </div>
                    </div>

                </form>

            </div>

            <!-- footer -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/80 flex-shrink-0 flex gap-3">
                <button onclick="closeModal()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition">Cancel</button>
                <button onclick="saveStaff()"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span id="saveLabel">Save Staff Member</span>
                </button>
            </div>
        </div>
    </div>

    <!-- VIEW / DETAIL DRAWER -->
    <div id="detailDrawer" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeDrawer()"></div>
        <div
            class="modal-panel relative bg-white w-full sm:max-w-sm sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[88vh]">

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <h2 class="font-bold text-gray-800">Staff Details</h2>
                <button onclick="closeDrawer()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-2 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto modal-scroll px-5 py-5" id="drawerBody"></div>

            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/80 flex gap-2 flex-shrink-0">
                <button onclick="editFromDrawer()"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </button>
                <button onclick="toggleActiveFromDrawer()" id="drawerToggleBtn"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">Deactivate</button>
                <button onclick="deleteStaff()"
                    class="w-11 h-11 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl transition flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>


    {{-- <div id="staffModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Add Staff</h3>
                <button onclick="closeStaffModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form onsubmit="createStaff(event)" class="space-y-4">

                <div>
                    <label class="text-sm font-medium text-gray-600">Full Name</label>
                    <input id="staffName" required
                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <input type="email" id="staffEmail" required
                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Phone</label>
                    <input id="staffPhone" required
                        class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Password</label>
                        <input type="password" id="staffPassword" required
                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Confirm Password</label>
                        <input type="password" id="staffPasswordConfirm" required
                            class="w-full border rounded-lg px-3 py-2 mt-1 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-600">Role</label>
                    <select id="staffRole" class="w-full border rounded-lg px-3 py-2 mt-1">
                        <option value="waiter">Waiter</option>
                        <option value="cashier">Cashier</option>
                        <option value="manager">Manager</option>
                        <option value="kitchen">Kitchen Staff</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

                <button class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Create Staff
                </button>

            </form>
        </div>
    </div> --}}


    <script>
        let fStatusOn = true;
        const AV_COLORS = ['av-0', 'av-1', 'av-2', 'av-3', 'av-4', 'av-5'];
        const ROLE_COLORS = {
            waiter: {
                bg: '#eff6ff',
                color: '#1d4ed8'
            },
            cashier: {
                bg: '#f0fdf4',
                color: '#15803d'
            },
            kitchen: {
                bg: '#fff7ed',
                color: '#c2410c'
            },
            staff: {
                bg: '#fdf4ff',
                color: '#7e22ce'
            },
            manager: {
                bg: '#fef2f2',
                color: '#b91c1c'
            },
        };


        const token = localStorage.getItem('auth_token');

        let STAFF = [];
        let FILTERED_STAFF = [];


        async function fetchStaff() {
            const res = await fetch('/api/v1/owner/restaurant/staff', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await res.json();

            STAFF = data.data; // source of truth
            FILTERED_STAFF = [...STAFF]; // working copy

            renderGrid(FILTERED_STAFF);
        }

        // ── RENDER GRID ───────────────────────────────────────────────
        function renderGrid(staffList) {
            const grid = document.getElementById('staffGrid');
            const empty = document.getElementById('emptyState');
            const delays = ['d1', 'd2', 'd3', 'd4', 'd5', 'd6', 'd7', 'd8'];

            if (!staffList.length) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            grid.innerHTML = staffList.map((s, i) => {
                const initials = s.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
                const avClass = AV_COLORS[s.id % AV_COLORS.length];
                const rc = ROLE_COLORS[s.role] || {
                    bg: '#f1f5f9',
                    color: '#475569'
                };
                const delay = delays[i % delays.length];
                const joined = s.created_at ? new Date(s.created_at).toLocaleDateString('en-US', {
                    month: 'short',
                    year: 'numeric'
                }) : '—';

                return `
    <div class="staff-card fade-up ${delay}" onclick="openDrawer(${s.id})">
      <div class="flex items-start justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center text-base font-extrabold flex-shrink-0 ${avClass}">
            ${initials}
          </div>
          <div>
            <p class="font-bold text-gray-800 text-sm leading-tight">${s.name}</p>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full capitalize mt-1 inline-block"
              style="background:${rc.bg};color:${rc.color};">${s.role}</span>
          </div>
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5 flex-shrink-0"
          style="background:${s.active?'#dcfce7':'#f3f4f6'};color:${s.active?'#15803d':'#9ca3af'};">
          <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:${s.active?'#22c55e':'#d1d5db'};"></span>
          ${s.active ? 'Active' : 'Off Duty'}
        </span>
      </div>
      <div class="flex items-center justify-between pt-3 border-t border-gray-50">
        <div class="flex items-center gap-1.5 text-xs text-gray-400">
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
          ${s.phone || '—'}
        </div>
        <div class="flex items-center gap-1.5 text-xs text-gray-400">
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Joined ${joined}
        </div>
      </div>
    </div>`;
            }).join('');

            updateCounts(staffList);
        }

        function updateCounts(staffList) {
            const total = staffList.length;
            const active = staffList.filter(s => s.active).length;
            const off = total - active;
            document.getElementById('staffCount').textContent = total;
            document.getElementById('totalCount').textContent = total;
            document.getElementById('activeCount').textContent = active;
            document.getElementById('offCount').textContent = off;
        }


        // ── FILTER / SEARCH ───────────────────────────────────────────
        function filterStaff() {
            const q = (document.getElementById('searchInput').value || '').toLowerCase();
            const role = document.getElementById('roleFilter').value;
            const duty = document.getElementById('statusFilter')?.value; // optional

            FILTERED_STAFF = STAFF.filter(s => {
                const matchSearch = !q ||
                    s.name.toLowerCase().includes(q) ||
                    s.phone?.includes(q) ||
                    s.role.toLowerCase().includes(q);

                const matchRole = !role || s.role === role;

                const matchDuty = !duty || String(s.active) === duty;

                return matchSearch && matchRole && matchDuty;
            });

            renderGrid(FILTERED_STAFF);
        }


        // ── MODAL ─────────────────────────────────────────────────────
        function openModal(id) {
            clearAllErrors()

            editingId = id || null;
            fStatusOn = true;

            document.getElementById('modalTitle').textContent = id ? 'Edit Staff Member' : 'Add Staff Member';
            document.getElementById('saveLabel').textContent = id ? 'Save Changes' : 'Save Staff Member';

            if (id) {
                const s = STAFF.find(x => x.id === id);
                document.getElementById('fName').value = s.name;
                document.getElementById('fRole').value = s.role;
                document.getElementById('fPhone').value = s.phone || '';
                document.getElementById('fEmail').value = s.email || '';
                fStatusOn = s.active;
            } else {
                document.getElementById('fName').value = '';
                document.getElementById('fRole').value = '';
                document.getElementById('fPhone').value = '';
                document.getElementById('fEmail').value = '';
                fStatusOn = true;
            }

            const tog = document.getElementById('fStatusTog');
            const lbl = document.getElementById('fStatusLabel');
            if (fStatusOn) {
                tog.classList.add('on');
                lbl.textContent = 'Active';
                lbl.className = 'text-sm font-semibold text-blue-600';
            } else {
                tog.classList.remove('on');
                lbl.textContent = 'Off Duty';
                lbl.className = 'text-sm font-semibold text-gray-400';
            }

            updateAvatar();
            document.getElementById('staffModal').classList.replace('hidden', 'flex');
        }

        function closeModal() {
            document.getElementById('staffModal').classList.replace('flex', 'hidden');
            editingId = null;
        }

        function toggleStatus() {
            fStatusOn = !fStatusOn;
            const tog = document.getElementById('fStatusTog');
            const lbl = document.getElementById('fStatusLabel');
            if (fStatusOn) {
                tog.classList.add('on');
                lbl.textContent = 'Active';
                lbl.className = 'text-sm font-semibold text-blue-600';
            } else {
                tog.classList.remove('on');
                lbl.textContent = 'Off Duty';
                lbl.className = 'text-sm font-semibold text-gray-400';
            }
        }

        function updateAvatar() {
            const name = document.getElementById('fName').value.trim();
            const init = name ? name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase() : '?';
            const av = document.getElementById('modalAvatar');
            av.textContent = init;
        }


        // ── VALIDATION HELPERS ────────────────────────────────────────
        function showErr(fieldId, msg) {
            const errEl = document.getElementById('err-' + fieldId);
            if (errEl) {
                errEl.textContent = msg;
                errEl.classList.add('show');
            }
        }

        function clearErr(fieldId) {
            const errEl = document.getElementById('err-' + fieldId);
            if (errEl) {
                errEl.classList.remove('show');
                errEl.textContent = '';
            }
        }

        function clearAllErrors() {
            document.querySelectorAll('.field-error')
                .forEach(el => {
                    el.classList.remove('show');
                    el.textContent = '';
                });
        }


        async function saveStaff() {

            clearAllErrors();
            let valid = true;

            const name = document.getElementById('fName').value.trim();
            const email = document.getElementById('fEmail').value.trim();
            const role = document.getElementById('fRole').value;
            const phone = document.getElementById('fPhone').value.trim();
            const password = editingId ? null : document.getElementById('fPassword').value;
            const password_confirmation = editingId ? null : document.getElementById('fPasswordConfirm').value;

            // Name
            if (!name) {
                showErr('fName', 'Full name is required');
                valid = false;
            } else if (name.length < 2) {
                showErr('fName', 'Name must be at least 2 characters');
                valid = false;
            }

            // Email
            if (!email) {
                showErr('fEmail', 'Email address is required');
                valid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showErr('fEmail', 'Enter a valid email address');
                valid = false;
            } else if (!editingId && STAFF.some(s => s.email === email)) {
                showErr('fEmail', 'This email is already registered');
                valid = false;
            }

            // Role
            if (!role) {
                showErr('fRole', 'Please select a role');
                valid = false;
            }

            // Phone
            if (!phone) {
                showErr('fPhone', 'Phone number is required');
                valid = false;
            } else if (!/^[0-9]{7,15}$/.test(phone.replace(/\s/g, ''))) {
                showErr('fPhone', 'Enter a valid phone number');
                valid = false;
            }

            // Password (new staff only)
            if (!editingId) {
                if (!password) {
                    showErr('fPassword', 'Password is required');
                    valid = false;
                    // } else if (password.length < 8) {
                    //     showErr('fPassword', 'Password must be at least 8 characters');
                    //     valid = false;
                    // } else if (!/[A-Z]/.test(password)) {
                    //     showErr('fPassword', 'Include at least one uppercase letter');
                    //     valid = false;
                    // } else if (!/[0-9]/.test(password)) {
                    //     showErr('fPassword', 'Include at least one number');
                    //     valid = false;
                }

                if (!password_confirmation) {
                    showErr('fPasswordConfirm', 'Please confirm your password');
                    valid = false;
                } else if (password && password_confirmation !== password) {
                    showErr('fPasswordConfirm', 'Passwords do not match');
                    valid = false;
                }
            }

            if (!valid) return;

            if (editingId) {
                // pull from already-loaded list instead of a second API call
                const s = STAFF.find(x => x.id === editingId);

                document.getElementById('fName').value = s.name || '';
                document.getElementById('fEmail').value = s.email || '';
                document.getElementById('fRole').value = s.role || '';
                document.getElementById('fPhone').value = s.phone || '';

                const payload = {
                    name: name,
                    email: email,
                    phone: phone,
                    role: role,
                };

                const res = await fetch(`/api/v1/owner/restaurant/staff/${editingId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                    },
                    body: JSON.stringify(payload),
                });

                if (!res.ok) {
                    const err = await res.json();
                    showToast(err.message || 'Failed to update staff', 'error');
                    return;
                }

                const updated = await res.json();

                // replace item inside STAFF array
                const index = STAFF.findIndex(s => s.id === editingId);
                if (index !== -1) {
                    STAFF[index] = updated.data;
                }

                showToast(`${name} updated`, 'success');
            } else {
                const payload = {
                    name: name,
                    email: email,
                    phone: phone,
                    password: password,
                    password_confirmation: password_confirmation,
                    role: role,
                };


                const res = await fetch('/api/v1/owner/restaurant/staff', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    const err = await res.json();

                    // Laravel validation errors come back as err.errors object
                    if (err.errors) {
                        if (err.errors.email) showErr('fEmail', err.errors.email[0]);
                        if (err.errors.phone) showErr('fPhone', err.errors.phone[0]);
                        if (err.errors.password) showErr('fPassword', err.errors.password[0]);
                        if (err.errors.name) showErr('fName', err.errors.name[0]);
                    } else {
                        showToast(err.message || 'Failed to create staff', 'error');
                    }
                    return;
                }


                const created = await res.json();

                // add new staff to array
                STAFF.push(created.data);

                showToast(`${name} added`, 'success');
            }

            closeModal();
            closeDrawer();
            renderGrid(STAFF);
        }

        // ── DETAIL DRAWER ─────────────────────────────────────────────
        function openDrawer(id) {
            viewingId = id;
            const s = STAFF.find(x => x.id === id);
            const initials = s.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
            const avClass = AV_COLORS[s.id % AV_COLORS.length];
            const rc = ROLE_COLORS[s.role] || {
                bg: '#f1f5f9',
                color: '#475569'
            };
            const joined = s.created_at ? new Date(s.created_at).toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            }) : '—';

            document.getElementById('drawerBody').innerHTML = `
    <div class="text-center mb-6">
      <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl font-extrabold mx-auto mb-3 ${avClass}">${initials}</div>
      <h3 class="text-xl font-extrabold text-gray-800">${s.name}</h3>
      <span class="text-xs font-semibold px-3 py-1 rounded-full capitalize mt-2 inline-block"
        style="background:${rc.bg};color:${rc.color};">${s.role}</span>
    </div>
    <div class="space-y-3">
      <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Account Status</span>
        <span class="text-sm font-bold flex items-center gap-1.5" style="color:${s.is_active?'#15803d':'#9ca3af'};">
          <span class="w-2 h-2 rounded-full" style="background:${s.is_active?'#22c55e':'#d1d5db'};"></span>
          ${s.is_active ? 'Active' : 'Inactive'}
        </span>
        </div>
        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Duty Status</span>
        <span class="text-sm font-bold flex items-center gap-1.5" style="color:${s.is_duty_active?'#15803d':'#9ca3af'};">
          <span class="w-2 h-2 rounded-full" style="background:${s.is_duty_active?'#22c55e':'#d1d5db'};"></span>
          ${s.is_duty_active ? 'On Duty' : 'Off Duty'}
        </span>
        </div>
      <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Phone</span>
        <span class="text-sm font-semibold text-gray-800">${s.phone || '—'}</span>
      </div>
      <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Email</span>
        <span class="text-sm font-semibold text-gray-800">${s.email || '—'}</span>
      </div>
      <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Joined</span>
        <span class="text-sm font-semibold text-gray-800">${joined}</span>
      </div>
      <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
        <span class="text-sm text-gray-500">Staff ID</span>
        <span class="text-sm font-semibold text-gray-800">#MT-${String(s.id).padStart(3,'0')}</span>
      </div>
    </div>`;

            const toggleBtn = document.getElementById('drawerToggleBtn');
            toggleBtn.textContent = s.is_active ? 'Deactivate' : 'Activate';
            toggleBtn.style.color = s.is_active ? '#dc2626' : '#15803d';

            document.getElementById('detailDrawer').classList.replace('hidden', 'flex');
        }

        function closeDrawer() {
            clearAllErrors();
            document.getElementById('detailDrawer').classList.replace('flex', 'hidden');
            viewingId = null;
        }

        function editFromDrawer() {
            const id = viewingId;
            closeDrawer();
            openModal(id);
        }

        function toggleActiveFromDrawer() {
            // const s = STAFF.find(x => x.id === viewingId);
            // s.active = !s.active;
            // showToast(`${s.name} ${s.active?'activated':'deactivated'}`, s.active ? 'success' : 'warning');
            // closeDrawer();
            // renderGrid(STAFF);

            showToast('In Progress', 'error');
        }

        function deleteStaff() {
            // const s = STAFF.find(x => x.id === viewingId);
            // if (!confirm(`Remove ${s.name} from staff?`)) return;
            // STAFF = STAFF.filter(x => x.id !== viewingId);
            // showToast(`${s.name} removed`, 'error');
            // closeDrawer();
            // renderGrid(STAFF);

            showToast('In Progress', 'error');
        }

        fetchStaff()
    </script>


@endsection
