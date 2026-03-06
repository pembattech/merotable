@extends('layouts.app')

@section('title', 'Staff | ' . config('app.name'))

@section('content')

    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.35s ease both; }
        .d1 { animation-delay: .04s } .d2 { animation-delay: .08s }
        .d3 { animation-delay: .12s } .d4 { animation-delay: .16s }
        .d5 { animation-delay: .20s } .d6 { animation-delay: .24s }
        .d7 { animation-delay: .28s } .d8 { animation-delay: .32s }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-panel { animation: slideUp 0.26s cubic-bezier(0.34, 1.56, 0.64, 1) both; }

        /* ── Staff card ── */
        .staff-card {
            background: #fff;
            border: 1px solid #e9eef5;
            border-radius: 16px;
            padding: 16px;
            cursor: pointer;
            transition: box-shadow 0.18s, transform 0.18s, border-color 0.18s;
        }
        @media (min-width: 768px) { .staff-card { padding: 20px; } }
        .staff-card:hover {
            box-shadow: 0 8px 24px rgba(37,99,235,0.10);
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        /* ── Avatar colours ── */
        .av-0 { background:#dbeafe; color:#1d4ed8; }
        .av-1 { background:#ede9fe; color:#6d28d9; }
        .av-2 { background:#dcfce7; color:#15803d; }
        .av-3 { background:#fef3c7; color:#b45309; }
        .av-4 { background:#fce7f3; color:#be185d; }
        .av-5 { background:#e0f2fe; color:#0369a1; }

        /* ── Toggle ── */
        .tog-track {
            width: 40px; height: 22px;
            background: #e5e7eb;
            border-radius: 999px;
            position: relative;
            cursor: pointer;
            transition: background .2s;
            flex-shrink: 0;
        }
        .tog-track.on { background: #3b82f6; }
        .tog-thumb {
            position: absolute; top: 3px; left: 3px;
            width: 16px; height: 16px;
            background: #fff; border-radius: 50%;
            transition: transform .2s cubic-bezier(0.34,1.56,0.64,1);
            box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .tog-track.on .tog-thumb { transform: translateX(18px); }

        .modal-scroll::-webkit-scrollbar { width: 4px; }
    </style>

    {{-- ── HEADER ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6 md:mb-7 fade-up d1">
        <div>
            <h1 class="text-lg md:text-2xl font-extrabold text-gray-900">Staff Members</h1>
            <p class="text-xs md:text-sm text-gray-400 mt-0.5">Momo House · <span id="staffCount"></span> members</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Search (hidden on very small, shown sm+) --}}
            <div class="relative hidden sm:block">
                <input id="searchInput" type="text" placeholder="Search staff…" oninput="filterStaff()"
                    class="pl-9 pr-4 py-2 md:py-2.5 border border-gray-200 bg-white rounded-xl text-xs md:text-sm
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none w-40 md:w-52 transition">
                <svg class="absolute left-3 top-2.5 h-3.5 w-3.5 md:h-4 md:w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            {{-- Role filter --}}
            <div class="relative">
                <select id="roleFilter" onchange="filterStaff()"
                    class="appearance-none pl-3 pr-7 py-2 md:py-2.5 border border-gray-200 bg-white rounded-xl
                           text-xs md:text-sm font-medium text-gray-600 focus:ring-2 focus:ring-blue-500
                           focus:border-transparent outline-none cursor-pointer transition">
                    <option value="">All Roles</option>
                    <option value="manager">Manager</option>
                    <option value="waiter">Waiter</option>
                    <option value="cashier">Cashier</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="staff">Staff</option>
                </select>
                <svg class="pointer-events-none absolute right-2 top-2.5 h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- Add button --}}
            <button onclick="openModal()"
                class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white
                       text-xs md:text-sm font-bold px-3 md:px-4 py-2 md:py-2.5 rounded-xl transition shadow-lg shadow-blue-200 whitespace-nowrap">
                <svg class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Staff
            </button>
        </div>
    </div>

    {{-- ── SUMMARY STRIPS ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-5 md:mb-6 fade-up d2">
        <div class="bg-white border border-gray-100 rounded-2xl px-3 md:px-4 py-3 flex items-center gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-9 md:h-9 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Total</p>
                <p class="text-base md:text-lg font-extrabold text-gray-800" id="totalCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-3 md:px-4 py-3 flex items-center gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-9 md:h-9 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Active</p>
                <p class="text-base md:text-lg font-extrabold text-gray-800" id="activeCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-3 md:px-4 py-3 flex items-center gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-9 md:h-9 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <div class="w-3 h-3 rounded-full bg-orange-400"></div>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Off Duty</p>
                <p class="text-base md:text-lg font-extrabold text-gray-800" id="offCount"></p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl px-3 md:px-4 py-3 flex items-center gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-9 md:h-9 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="h-4 w-4 md:h-5 md:w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Roles</p>
                <p class="text-base md:text-lg font-extrabold text-gray-800">5</p>
            </div>
        </div>
    </div>

    {{-- ── STAFF GRID ── --}}
    <div id="staffGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4"></div>

    {{-- Empty state --}}
    <div id="emptyState" class="hidden text-center py-16 md:py-20">
        <div class="w-14 h-14 md:w-16 md:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="h-7 w-7 md:h-8 md:w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="font-semibold text-gray-400 text-sm md:text-base">No staff found</p>
        <p class="text-xs md:text-sm text-gray-300 mt-1">Try adjusting your search or filter</p>
    </div>


    {{-- ══ ADD / EDIT STAFF MODAL ══ --}}
    <div id="staffModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="modal-panel relative bg-white w-full sm:max-w-md sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[92vh]">

            {{-- Drag handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 id="modalTitle" class="font-bold text-gray-800 text-sm md:text-base leading-tight">Add Staff Member</h2>
                        <p class="text-xs text-gray-400">Fill in the details below</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto modal-scroll flex-1 px-4 md:px-5 py-4 md:py-5 space-y-3 md:space-y-4">
                <form>
                    {{-- Avatar preview --}}
                    <div class="flex justify-center mb-2">
                        <div id="modalAvatar" class="w-14 h-14 md:w-16 md:h-16 rounded-2xl flex items-center justify-center text-xl md:text-2xl font-extrabold av-0">?</div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="fName" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                        <input id="fName" type="text" placeholder="e.g. Pemba Tamang" class="field-input text-sm"
                            oninput="updateAvatar(); clearErr('fName')"/>
                        <p class="field-error text-red-500 text-xs mt-1" id="err-fName"></p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="fEmail" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input id="fEmail" type="email" placeholder="staff@example.com" class="field-input text-sm"
                            oninput="clearErr('fEmail')"/>
                        <p class="field-error text-red-500 text-xs mt-1" id="err-fEmail"></p>
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="fRole" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Role</label>
                        <div class="relative">
                            <select id="fRole" class="field-input appearance-none pr-10 text-sm" onchange="clearErr('fRole')">
                                <option value="" disabled selected>Select a role</option>
                                <option value="waiter">Waiter</option>
                                <option value="cashier">Cashier</option>
                                <option value="kitchen">Kitchen</option>
                                <option value="captain">Captain</option>
                                <option value="manager">Manager</option>
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <p class="field-error text-red-500 text-xs mt-1" id="err-fRole"></p>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="fPhone" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Phone</label>
                        <input id="fPhone" type="tel" placeholder="98XXXXXXXX" class="field-input text-sm"
                            oninput="clearErr('fPhone')"/>
                        <p class="field-error text-red-500 text-xs mt-1" id="err-fPhone"></p>
                    </div>

                    {{-- Password section (add mode only) --}}
                    <div id="passwordSection">
                        <div class="flex items-center gap-3 my-3">
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Login Credentials</span>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        <div>
                            <label for="fPassword" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <input id="fPassword" type="password" placeholder="Min. 8 characters" class="field-input text-sm"/>
                            <p class="field-error text-red-500 text-xs mt-1" id="err-fPassword"></p>
                        </div>
                        <div class="mt-3">
                            <label for="fPasswordConfirm" class="block text-xs md:text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                            <input id="fPasswordConfirm" type="password" placeholder="Re-enter password" class="field-input text-sm"/>
                            <p class="field-error text-red-500 text-xs mt-1" id="err-fPasswordConfirm"></p>
                        </div>
                    </div>

                    {{-- Status toggle --}}
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 md:px-4 py-3">
                        <div>
                            <p class="text-xs md:text-sm font-semibold text-gray-700">Active Status</p>
                            <p class="text-xs text-gray-400">Staff can log in and take orders</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div id="fStatusTog" class="tog-track on" onclick="toggleStatus()">
                                <div class="tog-thumb"></div>
                            </div>
                            <span id="fStatusLabel" class="text-xs md:text-sm font-semibold text-blue-600">Active</span>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="px-4 md:px-5 py-3 md:py-4 border-t border-gray-100 flex-shrink-0 flex gap-3">
                <button onclick="closeModal()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                    Cancel
                </button>
                <button onclick="saveStaff()"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="saveLabel">Save Staff Member</span>
                </button>
            </div>
        </div>
    </div>


    {{-- ══ DETAIL DRAWER ══ --}}
    <div id="detailDrawer" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeDrawer()"></div>
        <div class="modal-panel relative bg-white w-full sm:max-w-sm sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[88vh]">

            {{-- Drag handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                <h2 class="font-bold text-gray-800 text-sm md:text-base">Staff Details</h2>
                <button onclick="closeDrawer()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto modal-scroll px-4 md:px-5 py-4 md:py-5" id="drawerBody"></div>

            <div class="px-4 md:px-5 py-3 md:py-4 border-t border-gray-100 flex gap-2 flex-shrink-0">
                <button onclick="editFromDrawer()"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </button>
                <button onclick="toggleActiveFromDrawer()" id="drawerToggleBtn"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs md:text-sm font-semibold py-2.5 rounded-xl transition">
                    Deactivate
                </button>
                <button onclick="deleteStaff()"
                    class="w-10 h-10 md:w-11 md:h-11 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl transition flex items-center justify-center flex-shrink-0">
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <script>
        let fStatusOn  = true;
        let editingId  = null;
        let viewingId  = null;

        const AV_COLORS  = ['av-0','av-1','av-2','av-3','av-4','av-5'];
        const ROLE_COLORS = {
            waiter:  { bg:'#eff6ff', color:'#1d4ed8' },
            cashier: { bg:'#f0fdf4', color:'#15803d' },
            kitchen: { bg:'#fff7ed', color:'#c2410c' },
            staff:   { bg:'#fdf4ff', color:'#7e22ce' },
            manager: { bg:'#fef2f2', color:'#b91c1c' },
            captain: { bg:'#f0f9ff', color:'#0369a1' },
        };

        const token = localStorage.getItem('auth_token');
        let STAFF = [], FILTERED_STAFF = [];

        async function fetchStaff() {
            const res  = await fetch('/api/v1/owner/restaurant/staff', {
                headers: { 'Accept':'application/json', 'Authorization':`Bearer ${token}` }
            });
            const data = await res.json();
            STAFF          = data.data;
            FILTERED_STAFF = [...STAFF];
            renderGrid(FILTERED_STAFF);
        }

        // ── RENDER ────────────────────────────────────────────────────
        function renderGrid(staffList) {
            const grid  = document.getElementById('staffGrid');
            const empty = document.getElementById('emptyState');
            const delays = ['d1','d2','d3','d4','d5','d6','d7','d8'];

            if (!staffList.length) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            grid.innerHTML = staffList.map((s, i) => {
                const initials = s.name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
                const avClass  = AV_COLORS[s.id % AV_COLORS.length];
                const rc       = ROLE_COLORS[s.role] || { bg:'#f1f5f9', color:'#475569' };
                const delay    = delays[i % delays.length];
                const joined   = s.createdAt
                    ? new Date(s.createdAt).toLocaleDateString('en-US', { month:'short', year:'numeric' })
                    : '—';
                const onDuty   = !!s.todayAttendance;

                return `
                <div class="staff-card fade-up ${delay}" onclick="openDrawer(${s.id})">
                    <div class="flex items-start justify-between mb-3 md:mb-4">
                        <div class="flex items-center gap-2 md:gap-3 min-w-0">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center text-sm md:text-base font-extrabold flex-shrink-0 ${avClass}">
                                ${initials}
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-800 text-xs md:text-sm leading-tight truncate">${s.name}</p>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full capitalize mt-1 inline-block"
                                    style="background:${rc.bg};color:${rc.color};">${s.role}</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1 flex-shrink-0 ml-2"
                            style="background:${onDuty?'#dcfce7':'#f3f4f6'};color:${onDuty?'#15803d':'#9ca3af'};">
                            <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:${onDuty?'#22c55e':'#d1d5db'};"></span>
                            ${onDuty ? 'On Duty' : 'Off'}
                        </span>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-1 text-xs text-gray-400">
                            <svg class="h-3 w-3 md:h-3.5 md:w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="truncate max-w-[80px]">${s.phone || '—'}</span>
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-400">
                            <svg class="h-3 w-3 md:h-3.5 md:w-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            ${joined}
                        </div>
                    </div>
                </div>`;
            }).join('');

            updateCounts(staffList);
        }

        function updateCounts(staffList) {
            const total   = staffList.length;
            const onDuty  = staffList.filter(s => s.todayAttendance).length;
            document.getElementById('staffCount').textContent  = total;
            document.getElementById('totalCount').textContent  = total;
            document.getElementById('activeCount').textContent = onDuty;
            document.getElementById('offCount').textContent    = total - onDuty;
        }

        // ── FILTER ────────────────────────────────────────────────────
        function filterStaff() {
            const q    = (document.getElementById('searchInput')?.value || '').toLowerCase();
            const role = document.getElementById('roleFilter').value;
            FILTERED_STAFF = STAFF.filter(s => {
                const matchSearch = !q || s.name.toLowerCase().includes(q) || s.phone?.includes(q) || s.role.toLowerCase().includes(q);
                const matchRole   = !role || s.role === role;
                return matchSearch && matchRole;
            });
            renderGrid(FILTERED_STAFF);
        }

        // ── MODAL ─────────────────────────────────────────────────────
        function openModal(id) {
            clearAllErrors();
            editingId  = id || null;
            fStatusOn  = true;

            document.getElementById('modalTitle').textContent = id ? 'Edit Staff Member' : 'Add Staff Member';
            document.getElementById('saveLabel').textContent  = id ? 'Save Changes'      : 'Save Staff Member';

            const pwSection = document.getElementById('passwordSection');
            pwSection.style.display = id ? 'none' : 'block';

            if (id) {
                const s = STAFF.find(x => x.id === id);
                document.getElementById('fName').value  = s.name;
                document.getElementById('fRole').value  = s.role;
                document.getElementById('fPhone').value = s.phone || '';
                document.getElementById('fEmail').value = s.email || '';
                fStatusOn = s.active;
            } else {
                ['fName','fRole','fPhone','fEmail','fPassword','fPasswordConfirm'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                fStatusOn = true;
            }

            const tog = document.getElementById('fStatusTog');
            const lbl = document.getElementById('fStatusLabel');
            tog.classList.toggle('on', fStatusOn);
            lbl.textContent  = fStatusOn ? 'Active' : 'Off Duty';
            lbl.className    = `text-xs md:text-sm font-semibold ${fStatusOn ? 'text-blue-600' : 'text-gray-400'}`;

            updateAvatar();
            document.body.classList.add('overflow-hidden');
            document.getElementById('staffModal').classList.replace('hidden','flex');
        }

        function closeModal() {
            document.getElementById('staffModal').classList.replace('flex','hidden');
            document.body.classList.remove('overflow-hidden');
            editingId = null;
        }

        function toggleStatus() {
            fStatusOn = !fStatusOn;
            const tog = document.getElementById('fStatusTog');
            const lbl = document.getElementById('fStatusLabel');
            tog.classList.toggle('on', fStatusOn);
            lbl.textContent = fStatusOn ? 'Active' : 'Off Duty';
            lbl.className   = `text-xs md:text-sm font-semibold ${fStatusOn ? 'text-blue-600' : 'text-gray-400'}`;
        }

        function updateAvatar() {
            const name = document.getElementById('fName').value.trim();
            const init = name ? name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase() : '?';
            document.getElementById('modalAvatar').textContent = init;
        }

        // ── VALIDATION ────────────────────────────────────────────────
        function showErr(fieldId, msg) {
            const el = document.getElementById('err-' + fieldId);
            if (el) { el.textContent = msg; el.classList.add('show'); }
        }
        function clearErr(fieldId) {
            const el = document.getElementById('err-' + fieldId);
            if (el) { el.classList.remove('show'); el.textContent = ''; }
        }
        function clearAllErrors() {
            document.querySelectorAll('.field-error').forEach(el => { el.classList.remove('show'); el.textContent = ''; });
        }

        // ── SAVE ──────────────────────────────────────────────────────
        async function saveStaff() {
            clearAllErrors();
            let valid = true;

            const name     = document.getElementById('fName').value.trim();
            const email    = document.getElementById('fEmail').value.trim();
            const role     = document.getElementById('fRole').value;
            const phone    = document.getElementById('fPhone').value.trim();
            const password = editingId ? null : document.getElementById('fPassword').value;
            const pwConf   = editingId ? null : document.getElementById('fPasswordConfirm').value;

            if (!name || name.length < 2) { showErr('fName', name ? 'Name must be at least 2 characters' : 'Full name is required'); valid = false; }
            if (!email)                   { showErr('fEmail', 'Email address is required'); valid = false; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showErr('fEmail', 'Enter a valid email address'); valid = false; }
            if (!role)  { showErr('fRole',  'Please select a role');      valid = false; }
            if (!phone) { showErr('fPhone', 'Phone number is required');  valid = false; }
            else if (!/^[0-9]{7,15}$/.test(phone.replace(/\s/g,''))) { showErr('fPhone', 'Enter a valid phone number'); valid = false; }

            if (!editingId) {
                if (!password) { showErr('fPassword', 'Password is required'); valid = false; }
                if (!pwConf)   { showErr('fPasswordConfirm', 'Please confirm your password'); valid = false; }
                else if (password && pwConf !== password) { showErr('fPasswordConfirm', 'Passwords do not match'); valid = false; }
            }

            if (!valid) return;

            try {
                const isEdit  = !!editingId;
                const url     = isEdit ? `/api/v1/owner/restaurant/staff/${editingId}` : '/api/v1/owner/restaurant/staff';
                const method  = isEdit ? 'PATCH' : 'POST';
                const payload = isEdit
                    ? { name, email, phone, role }
                    : { name, email, phone, password, password_confirmation: pwConf, role };

                const res  = await fetch(url, {
                    method,
                    headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'Authorization':`Bearer ${token}` },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (!res.ok) {
                    if (data.errors) {
                        if (data.errors.email)    showErr('fEmail',           data.errors.email[0]);
                        if (data.errors.phone)    showErr('fPhone',           data.errors.phone[0]);
                        if (data.errors.password) showErr('fPassword',        data.errors.password[0]);
                        if (data.errors.name)     showErr('fName',            data.errors.name[0]);
                    } else { showToast(data.message || 'Failed to save staff', 'error'); }
                    return;
                }

                if (isEdit) {
                    const idx = STAFF.findIndex(s => s.id === editingId);
                    if (idx !== -1) STAFF[idx] = data.data;
                } else {
                    STAFF.push(data.data);
                }

                showToast(`${name} ${isEdit ? 'updated' : 'added'}`, 'success');
                closeModal();
                closeDrawer();
                renderGrid(STAFF);
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }

        // ── DETAIL DRAWER ─────────────────────────────────────────────
        function openDrawer(id) {
            viewingId = id;
            const s = STAFF.find(x => x.id === id);
            const initials = s.name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
            const avClass  = AV_COLORS[s.id % AV_COLORS.length];
            const rc       = ROLE_COLORS[s.role] || { bg:'#f1f5f9', color:'#475569' };
            const joined   = s.createdAt
                ? new Date(s.createdAt).toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' })
                : '—';
            const onDuty   = !!s.todayAttendance;
            console.log('onDuty')
            console.log(onDuty)
            console.log(s.todayAttendance)

            document.getElementById('drawerBody').innerHTML = `
                <div class="text-center mb-5">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex items-center justify-center text-2xl md:text-3xl font-extrabold mx-auto mb-3 ${avClass}">${initials}</div>
                    <h3 class="text-lg md:text-xl font-extrabold text-gray-800">${s.name}</h3>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full capitalize mt-2 inline-block"
                        style="background:${rc.bg};color:${rc.color};">${s.role}</span>
                </div>
                <div class="space-y-2 md:space-y-3">
                    ${[
                        ['Account Status', `<span class="text-xs md:text-sm font-bold flex items-center gap-1.5" style="color:${s.isActive?'#15803d':'#9ca3af'};"><span class="w-2 h-2 rounded-full" style="background:${s.isActive?'#22c55e':'#d1d5db'};"></span>${s.isActive?'Active':'Inactive'}</span>`],
                        ['Duty Status',    `<span class="text-xs md:text-sm font-bold flex items-center gap-1.5" style="color:${onDuty?'#15803d':'#9ca3af'};"><span class="w-2 h-2 rounded-full" style="background:${onDuty?'#22c55e':'#d1d5db'};"></span>${onDuty?'On Duty':'Off Duty'}</span>`],
                        ['Phone',          `<span class="text-xs md:text-sm font-semibold text-gray-800">${s.phone||'—'}</span>`],
                        ['Email',          `<span class="text-xs md:text-sm font-semibold text-gray-800 truncate max-w-[160px]">${s.email||'—'}</span>`],
                        ['Joined',         `<span class="text-xs md:text-sm font-semibold text-gray-800">${joined}</span>`],
                        ['Staff ID',       `<span class="text-xs md:text-sm font-semibold text-gray-800">#MT-${String(s.id).padStart(3,'0')}</span>`],
                    ].map(([label, val]) => `
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-3 md:px-4 py-2.5 md:py-3">
                            <span class="text-xs md:text-sm text-gray-500">${label}</span>
                            ${val}
                        </div>`).join('')}
                </div>`;

            const toggleBtn = document.getElementById('drawerToggleBtn');
            toggleBtn.textContent  = s.isActive ? 'Deactivate' : 'Activate';
            toggleBtn.style.color  = s.isActive ? '#dc2626'    : '#15803d';

            document.body.classList.add('overflow-hidden');
            document.getElementById('detailDrawer').classList.replace('hidden','flex');
        }

        function closeDrawer() {
            clearAllErrors();
            document.getElementById('detailDrawer').classList.replace('flex','hidden');
            document.body.classList.remove('overflow-hidden');
            viewingId = null;
        }

        function editFromDrawer()          { const id = viewingId; closeDrawer(); openModal(id); }
        function toggleActiveFromDrawer()  { showToast('In Progress', 'error'); }
        function deleteStaff()             { showToast('In Progress', 'error'); }

        fetchStaff();
    </script>

@endsection