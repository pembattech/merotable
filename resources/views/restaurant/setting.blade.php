@extends('layouts.app')

@section('title', 'Profile | ' . config('app.name'))

@section('content')

    <style>
        .modal-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .modal-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        /* ── Shared field-input ── */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.6rem 0.875rem;
            font-size: 0.8125rem;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
            font-family: inherit;
        }

        @media (min-width: 768px) {
            .field-input {
                font-size: 0.875rem;
            }
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        textarea.field-input {
            resize: none;
            line-height: 1.5;
        }

        /* ── Profile banner ── */
        .profile-lower {
            padding: 0 16px 16px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (min-width: 640px) {
            .profile-lower {
                padding: 0 22px 22px;
                gap: 18px;
            }
        }

        .avatar-wrap {
            position: relative;
            margin-top: -34px;
            flex-shrink: 0;
        }

        @media (min-width: 640px) {
            .avatar-wrap {
                margin-top: -40px;
            }
        }

        .avatar {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            border: 3px solid #fff;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        @media (min-width: 640px) {
            .avatar {
                width: 80px;
                height: 80px;
                border-radius: 18px;
                font-size: 2rem;
            }
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            background: #22c55e;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        @media (min-width: 640px) {
            .avatar-badge {
                width: 18px;
                height: 18px;
            }
        }

        .profile-info {
            flex: 1;
            min-width: 140px;
            padding-top: 8px;
        }

        .profile-info h2 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
        }

        @media (min-width: 640px) {
            .profile-info h2 {
                font-size: 1.2rem;
            }
        }

        .profile-info .tagline {
            font-size: 0.72rem;
            color: #6b7280;
            margin-top: 2px;
        }

        @media (min-width: 640px) {
            .profile-info .tagline {
                font-size: 0.8rem;
            }
        }

        .profile-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 6px;
        }

        .tag {
            background: #eff6ff;
            color: #2563eb;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
        }

        .tag.green {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .tag.orange {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fed7aa;
        }

        /* ── Field labels ── */
        .field-item label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        /* ── Save button on card (base styles, visibility handled by .dirty) ── */
        .btn-save-card {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 7px 0;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-save-card:hover {
            background: #1d4ed8;
        }

        /* ── Danger zone ── */
        .danger-row {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #fef2f2;
        }

        @media (min-width: 480px) {
            .danger-row {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 14px 0;
            }
        }

        .danger-row:last-child {
            border-bottom: none;
        }

        .danger-row-info p {
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
        }

        .danger-row-info span {
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .btn-danger {
            align-self: flex-start;
            background: #fff;
            border: 1.5px solid #fca5a5;
            color: #dc2626;
            padding: 7px 14px;
            border-radius: 9px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: inherit;
            flex-shrink: 0;
        }

        @media (min-width: 480px) {
            .btn-danger {
                padding: 8px 16px;
                font-size: 0.78rem;
                align-self: auto;
            }
        }

        .btn-danger:hover {
            background: #fef2f2;
            border-color: #dc2626;
        }

        /* ── Save button — hidden until dirty ── */
        .btn-save-card {
            overflow: hidden;
            max-width: 0;
            opacity: 0;
            padding-left: 0;
            padding-right: 0;
            pointer-events: none;
            transition: max-width 0.3s ease, opacity 0.25s ease, padding 0.3s ease;
            white-space: nowrap;
        }

        .btn-save-card.dirty {
            max-width: 120px;
            opacity: 1;
            padding-left: 16px;
            padding-right: 16px;
            pointer-events: auto;
        }

        .btn-save-card:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed;
        }

        /* ── Skeleton loader ── */
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 8px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>


    {{-- ── PAGE HEADER ── --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8">
        <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Restaurant Profile</h1>
    </header>


    {{-- ── PROFILE BANNER ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 md:mb-5 overflow-hidden">
        <div class="h-20 md:h-28 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 rounded-t-2xl"></div>
        <div class="profile-lower">
            <div class="avatar-wrap">
                <div class="avatar" id="bannerAvatar">🍜</div>
                <div class="avatar-badge"></div>
            </div>
            <div class="profile-info">
                <h2 id="profileName"><span class="skeleton inline-block w-40 h-5">&nbsp;</span></h2>
                <div class="tagline" id="profileTagline"><span class="skeleton inline-block w-56 h-3 mt-1">&nbsp;</span>
                </div>
                <div class="profile-tags mt-2">
                    <span class="tag green" id="profileStatus">● Loading…</span>
                </div>
            </div>
        </div>
    </div>


    {{-- ── INFO + FINANCIAL GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 md:mb-5">

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100">
                <h3 class="text-sm md:text-base font-extrabold text-gray-900">Basic Information</h3>
                <button id="btnSaveBasic" class="btn-save-card" onclick="saveBasicInfo()">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Save
                </button>
            </div>
            <div class="px-4 md:px-5 py-4 md:py-5 space-y-3 md:space-y-4">
                <div class="field-item">
                    <label>Restaurant Name</label>
                    <input class="field-input" type="text" id="fieldName" placeholder="Loading…"
                        oninput="document.getElementById('profileName').textContent = this.value || 'Restaurant Name'" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Phone Number</label>
                        <input class="field-input" type="text" id="fieldPhone" placeholder="Loading…" />
                    </div>
                    <div class="field-item">
                        <label>Email</label>
                        <input class="field-input" type="email" id="fieldEmail" placeholder="Loading…" />
                    </div>
                </div>
                <div class="field-item">
                    <label>Address</label>
                    <input class="field-input" type="text" id="fieldAddress" placeholder="Loading…" />
                </div>
                <div class="field-item">
                    <label>Description</label>
                    <textarea class="field-input" id="fieldDesc" rows="3" placeholder="Loading…"></textarea>
                </div>

                {{-- Logo upload --}}
                <div class="field-item">
                    <label>Logo</label>
                    <div id="logoDropZone"
                        class="border-2 border-dashed border-gray-200 rounded-xl bg-gray-50
                               flex items-center gap-3 cursor-pointer px-4 py-3
                               hover:border-blue-400 hover:bg-blue-50/40 transition"
                        onclick="document.getElementById('logoInput').click()"
                        ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-50')"
                        ondragleave="this.classList.remove('border-blue-400','bg-blue-50')" ondrop="handleLogoDrop(event)">
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Click or drag logo here</p>
                            <p class="text-xs text-gray-400">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                    <div id="logoPreviewWrap" class="hidden mt-2 flex items-center gap-3">
                        <img id="logoPreviewImg" src="" alt="Logo preview"
                            class="w-14 h-14 rounded-xl object-cover border border-gray-200 shadow-sm" />
                        <div class="flex-1 min-w-0">
                            <p id="logoPreviewName" class="text-xs font-semibold text-gray-700 truncate"></p>
                            <button type="button" onclick="clearLogo()"
                                class="text-xs text-red-500 hover:text-red-700 font-medium mt-0.5">Remove</button>
                        </div>
                    </div>
                    <input type="file" id="logoInput" accept="image/*" class="hidden"
                        onchange="handleLogoSelect(event)" />
                </div>
            </div>
        </div>

        {{-- Financial Settings --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100">
                <h3 class="text-sm md:text-base font-extrabold text-gray-900">Financial Settings</h3>
                <button id="btnSaveFinancial" class="btn-save-card" onclick="saveFinancialSettings()">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Save
                </button>
            </div>
            <div class="px-4 md:px-5 py-4 md:py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Tax (%)</label>
                        <input type="number" id="tax_percentage" class="field-input" placeholder="0" min="0"
                            max="100" step="0.01" />
                    </div>
                    <div class="field-item">
                        <label>Service Charge (%)</label>
                        <input type="number" id="service_charge_percentage" class="field-input" placeholder="0"
                            min="0" max="100" step="0.01" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="tax_enabled" class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                        <span class="text-xs md:text-sm text-gray-700 font-medium">Enable Tax</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="service_charge_enabled"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                        <span class="text-xs md:text-sm text-gray-700 font-medium">Enable Service Charge</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Delivery Charge</label>
                        <input type="number" id="delivery_charge" class="field-input" placeholder="0" min="0"
                            step="0.01" />
                    </div>
                    <div class="field-item">
                        <label>Currency</label>
                        <input type="text" id="currency" class="field-input" placeholder="NPR" maxlength="10" />
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ── DANGER ZONE ── --}}
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden mb-6">
        <div class="px-4 md:px-5 py-3 md:py-4 border-b border-red-50">
            <h3 class="text-sm md:text-base font-extrabold text-red-600">⚠️ Danger Zone</h3>
        </div>
        <div class="px-4 md:px-5 py-1">
            <div class="danger-row">
                <div class="danger-row-info">
                    <p>Temporarily Close Restaurant</p>
                    <span>Hide your restaurant from the customer app until re-opened.</span>
                </div>
                <button class="btn-danger" onclick="pauseRestaurant()">Pause Restaurant</button>
            </div>
            <div class="danger-row">
                <div class="danger-row-info">
                    <p>Reset Menu</p>
                    <span>Remove all menu items. This action cannot be undone.</span>
                </div>
                <button class="btn-danger" onclick="resetMenu()">Reset Menu</button>
            </div>
            <div class="danger-row">
                <div class="danger-row-info">
                    <p>Delete Restaurant Account</p>
                    <span>Permanently delete your restaurant and all associated data.</span>
                </div>
                <button class="btn-danger" onclick="deleteAccount()">Delete Account</button>
            </div>
        </div>
    </div>


    <script>
        const token = localStorage.getItem('auth_token');
        const restroSlug = localStorage.getItem('restro_url');

        // ─────────────────────────────────────────────────────────────
        // Dirty-tracking snapshots
        // ─────────────────────────────────────────────────────────────
        let basicSnapshot = null; // set after loadProfile()
        let financialSnapshot = null; // set after loadFinancialSettings()
        let logoChanged = false;

        function getBasicValues() {
            return {
                name: document.getElementById('fieldName').value.trim(),
                phone: document.getElementById('fieldPhone').value.trim(),
                email: document.getElementById('fieldEmail').value.trim(),
                address: document.getElementById('fieldAddress').value.trim(),
                description: document.getElementById('fieldDesc').value.trim(),
            };
        }

        function getFinancialValues() {
            return {
                tax_percentage: document.getElementById('tax_percentage').value,
                service_charge_percentage: document.getElementById('service_charge_percentage').value,
                tax_enabled: document.getElementById('tax_enabled').checked,
                service_charge_enabled: document.getElementById('service_charge_enabled').checked,
                delivery_charge: document.getElementById('delivery_charge').value,
                currency: document.getElementById('currency').value.trim(),
            };
        }

        function isBasicDirty() {
            if (!basicSnapshot) return false;
            if (logoChanged) return true;
            const cur = getBasicValues();
            return Object.keys(basicSnapshot).some(k => basicSnapshot[k] !== cur[k]);
        }

        function isFinancialDirty() {
            if (!financialSnapshot) return false;
            const cur = getFinancialValues();
            return Object.keys(financialSnapshot).some(k => String(financialSnapshot[k]) !== String(cur[k]));
        }

        // function syncBasicDirty() {
        //     document.getElementById('btnSaveBasic').classList.toggle('dirty', isBasicDirty());
        // }

        // function syncFinancialDirty() {
        //     document.getElementById('btnSaveFinancial').classList.toggle('dirty', isFinancialDirty());
        // }
        function syncBasicDirty() {
            const btn = document.getElementById('btnSaveBasic');
            const dirty = isBasicDirty();

            btn.classList.toggle('dirty', dirty);
            btn.classList.toggle('hidden', !dirty); // 👈 auto show/hide
        }

        function syncFinancialDirty() {
            const btn = document.getElementById('btnSaveFinancial');
            const dirty = isFinancialDirty();

            btn.classList.toggle('dirty', dirty);
            btn.classList.toggle('hidden', !dirty); // 👈 auto show/hide
        }

        // Attach listeners once DOM is ready (called after load functions populate fields)
        function attachBasicListeners() {
            ['fieldName', 'fieldPhone', 'fieldEmail', 'fieldAddress', 'fieldDesc'].forEach(id => {
                document.getElementById(id).addEventListener('input', syncBasicDirty);
            });
        }

        function attachFinancialListeners() {
            ['tax_percentage', 'service_charge_percentage', 'delivery_charge', 'currency'].forEach(id => {
                document.getElementById(id).addEventListener('input', syncFinancialDirty);
            });
            ['tax_enabled', 'service_charge_enabled'].forEach(id => {
                document.getElementById(id).addEventListener('change', syncFinancialDirty);
            });
        }

        // ─────────────────────────────────────────────────────────────
        // LOAD profile on page load
        // ─────────────────────────────────────────────────────────────
        async function loadProfile() {
            try {
                const res = await fetch('/api/v1/owner/restaurant/profile', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load');

                const r = data.data;

                console.log(data)

                // Banner
                document.getElementById('profileName').textContent = r.name || '—';
                document.getElementById('profileTagline').textContent = r.slug || '';
                const statusEl = document.getElementById('profileStatus');
                if (r.isActive) {
                    statusEl.textContent = '● Open Now';
                    statusEl.className = 'tag green';
                } else {
                    statusEl.textContent = '● Temporarily Closed';
                    statusEl.className = 'tag orange';
                }

                // Logo in banner avatar
                if (r.logo) {
                    document.getElementById('bannerAvatar').innerHTML = `<img src="${r.logo}" alt="${r.name}"/>`;
                }

                // Basic info fields
                document.getElementById('fieldName').value = r.name || '';
                document.getElementById('fieldPhone').value = r.contactNumber || '';
                document.getElementById('fieldEmail').value = r.email || '';
                document.getElementById('fieldAddress').value = r.address || '';
                document.getElementById('fieldDesc').value = r.description || '';

                // Existing logo preview
                if (r.logo_url) {
                    document.getElementById('logoPreviewImg').src = r.logo_url;
                    document.getElementById('logoPreviewName').textContent = 'Current logo';
                    document.getElementById('logoDropZone').classList.add('hidden');
                    document.getElementById('logoPreviewWrap').classList.remove('hidden');
                }

                // Snapshot AFTER populating
                basicSnapshot = getBasicValues();
                logoChanged = false;
                attachBasicListeners();

            } catch (err) {
                console.error('loadProfile:', err);
                showToast('Failed to load restaurant info ❌', 'error');
                document.getElementById('profileName').textContent = 'Restaurant';
            }
        }

        async function loadFinancialSettings() {
            try {
                const res = await fetch('/api/v1/owner/restaurant/settings', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to load');

                const s = data.data;

                document.getElementById('tax_percentage').value = s.taxPercentage ?? 0;
                document.getElementById('service_charge_percentage').value = s.serviceChargePercentage ?? 0;
                document.getElementById('tax_enabled').checked = !!s.taxEnabled;
                document.getElementById('service_charge_enabled').checked = !!s.serviceChargeEnabled;
                document.getElementById('delivery_charge').value = s.deliveryCharge ?? 0;
                document.getElementById('currency').value = s.currency || 'NPR';

                // Snapshot AFTER populating
                financialSnapshot = getFinancialValues();
                attachFinancialListeners();


            } catch (err) {
                console.error('loadFinancialSettings:', err);
                showToast('Failed to load financial settings ❌', 'error');
            }
        }

        // Run on page load
        loadProfile();
        loadFinancialSettings();


        // ─────────────────────────────────────────────────────────────
        // SAVE – Basic Information  (restaurant table)
        // ─────────────────────────────────────────────────────────────
        async function saveBasicInfo() {
            const btn = document.getElementById('btnSaveBasic');
            btn.disabled = true;
            btn.innerHTML = `<svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving…`;

            try {
                const logoFile = document.getElementById('logoInput').files[0];
                let body;
                let headers = {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                };

                if (logoFile) {
                    // Multipart when a new logo is selected
                    body = new FormData();
                    body.append('name', document.getElementById('fieldName').value.trim());
                    body.append('phone', document.getElementById('fieldPhone').value.trim());
                    body.append('email', document.getElementById('fieldEmail').value.trim());
                    body.append('address', document.getElementById('fieldAddress').value.trim());
                    body.append('description', document.getElementById('fieldDesc').value.trim());
                    body.append('logo', logoFile);
                    body.append('_method', 'PATCH'); // Laravel method spoofing for multipart
                } else {
                    // JSON when no new file
                    headers['Content-Type'] = 'application/json';
                    body = JSON.stringify({
                        name: document.getElementById('fieldName').value.trim(),
                        email: document.getElementById('fieldEmail').value.trim(),
                        contact_number: document.getElementById('fieldPhone').value.trim(),
                        address: document.getElementById('fieldAddress').value.trim(),
                        description: document.getElementById('fieldDesc').value.trim(),
                    });
                }

                const method = logoFile ? 'POST' : 'PATCH'; // POST + _method=PATCH for FormData
                const res = await fetch('/api/v1/owner/restaurant/profile', {
                    method,
                    headers,
                    body
                });
                const data = await res.json();

                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save');

                // Refresh banner
                const r = data.data;
                document.getElementById('profileName').textContent = r.name || '—';
                if (r.logo_url) {
                    document.getElementById('bannerAvatar').innerHTML = `<img src="${r.logo_url}" alt="${r.name}"/>`;
                }

                // ✅ RESET STATE
                basicSnapshot = getBasicValues();
                logoChanged = false;

                syncBasicDirty();

                showToast('Basic info saved', 'success');

            } catch (err) {
                console.error('saveBasicInfo:', err);
                showToast(err.message || 'Failed to save ❌', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save`;
            }
        }


        // ─────────────────────────────────────────────────────────────
        // SAVE – Financial Settings  (restaurant_setting table)
        // ─────────────────────────────────────────────────────────────
        async function saveFinancialSettings() {
            const btn = document.getElementById('btnSaveFinancial');
            btn.disabled = true;
            btn.innerHTML = `<svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving…`;

            try {
                const payload = {
                    tax_percentage: parseFloat(document.getElementById('tax_percentage').value) || 0,
                    service_charge_percentage: parseFloat(document.getElementById('service_charge_percentage')
                        .value) || 0,
                    tax_enabled: document.getElementById('tax_enabled').checked,
                    service_charge_enabled: document.getElementById('service_charge_enabled').checked,
                    delivery_charge: parseFloat(document.getElementById('delivery_charge').value) || 0,
                    currency: document.getElementById('currency').value.trim() || 'NPR',
                };

                const res = await fetch('/api/v1/owner/restaurant/settings', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to save');

                // document.getElementById('btnSaveBasic').classList.toggle('dirty');
                // document.getElementById('btnSaveFinancial').classList.toggle('dirty');

                // ✅ RESET STATE
                financialSnapshot = getFinancialValues();

                syncFinancialDirty();

                showToast('Financial settings saved', 'success');

            } catch (err) {
                console.error('saveFinancialSettings:', err);
                showToast(err.message || 'Failed to save ❌', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    `<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Save`;
            }
        }


        // ─────────────────────────────────────────────────────────────
        // Logo image handlers
        // ─────────────────────────────────────────────────────────────
        function handleLogoSelect(e) {
            const f = e.target.files[0];
            if (f) showLogoPreview(f);
        }

        function handleLogoDrop(e) {
            e.preventDefault();
            const f = e.dataTransfer.files[0];
            if (f && f.type.startsWith('image/')) {
                // Assign to input so it's included in FormData
                const dt = new DataTransfer();
                dt.items.add(f);
                document.getElementById('logoInput').files = dt.files;
                showLogoPreview(f);
            }
        }

        function showLogoPreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('logoPreviewImg').src = e.target.result;
                document.getElementById('logoPreviewName').textContent = file.name;
                document.getElementById('logoDropZone').classList.add('hidden');
                document.getElementById('logoPreviewWrap').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function clearLogo() {
            document.getElementById('logoInput').value = '';
            document.getElementById('logoPreviewImg').src = '';
            document.getElementById('logoPreviewWrap').classList.add('hidden');
            document.getElementById('logoDropZone').classList.remove('hidden');
        }


        // ─────────────────────────────────────────────────────────────
        // Danger zone
        // ─────────────────────────────────────────────────────────────
        async function pauseRestaurant() {
            if (!confirm('Hide your restaurant from the customer app until you re-open it?')) return;
            try {
                const res = await fetch('/api/v1/owner/restaurant/deactivate', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'inactive'
                    }),
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Failed ❌', 'error');
                    return;
                }
                showToast('Restaurant paused ⏸', 'warning');
                document.getElementById('profileStatus').textContent = '● Temporarily Closed';
                document.getElementById('profileStatus').className = 'tag orange';
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }

        async function resetMenu() {
            alert('Coming Soon.')
            // const confirmed = prompt('Type RESET to confirm deleting all menu items:');
            // if (confirmed !== 'RESET') {
            //     showToast('Reset cancelled', 'warning');
            //     return;
            // }
            // try {
            //     const res = await fetch('/api/v1/owner/restaurant/menu/reset', {
            //         method: 'DELETE',
            //         headers: {
            //             'Authorization': `Bearer ${token}`,
            //             'Accept': 'application/json'
            //         },
            //     });
            //     const data = await res.json();
            //     if (!data.success) {
            //         showToast(data.message || 'Failed ❌', 'error');
            //         return;
            //     }
            //     showToast('All menu items deleted', 'error');
            // } catch (err) {
            //     console.error(err);
            //     showToast('Something went wrong ❌', 'error');
            // }
        }

        async function deleteAccount() {
            alert('Coming Soon.')
            // const confirmed = prompt('Type DELETE to permanently delete your restaurant account:');
            // if (confirmed !== 'DELETE') {
            //     showToast('Deletion cancelled', 'warning');
            //     return;
            // }
            // try {
            //     const res = await fetch('/api/v1/owner/restaurant', {
            //         method: 'DELETE',
            //         headers: {
            //             'Authorization': `Bearer ${token}`,
            //             'Accept': 'application/json'
            //         },
            //     });
            //     const data = await res.json();
            //     if (!data.success) {
            //         showToast(data.message || 'Failed ❌', 'error');
            //         return;
            //     }
            //     showToast('Account deleted. Redirecting…', 'error');
            //     setTimeout(() => window.location.href = '/', 2000);
            // } catch (err) {
            //     console.error(err);
            //     showToast('Something went wrong ❌', 'error');
            // }
        }
    </script>

@endsection
