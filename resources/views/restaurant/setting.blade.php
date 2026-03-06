@extends('layouts.app')

@section('title', 'Profile | ' . config('app.name'))

@section('content')

    <style>
        .modal-scroll::-webkit-scrollbar       { width: 4px; }
        .modal-scroll::-webkit-scrollbar-track { background: transparent; }
        .modal-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

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
        @media (min-width: 768px) { .field-input { font-size: 0.875rem; } }
        .field-input:focus        { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }
        .field-input::placeholder { color: #9ca3af; }
        textarea.field-input      { resize: none; line-height: 1.5; }

        /* ── Profile banner ── */
        .profile-lower {
            padding: 0 16px 16px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }
        @media (min-width: 640px) { .profile-lower { padding: 0 22px 22px; gap: 18px; } }

        .avatar-wrap { position: relative; margin-top: -34px; flex-shrink: 0; }
        @media (min-width: 640px) { .avatar-wrap { margin-top: -40px; } }

        .avatar {
            width: 64px; height: 64px;
            border-radius: 14px;
            border: 3px solid #fff;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 800; color: #fff;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }
        @media (min-width: 640px) { .avatar { width: 80px; height: 80px; border-radius: 18px; font-size: 2rem; } }

        .avatar-badge {
            position: absolute; bottom: -4px; right: -4px;
            background: #22c55e; width: 15px; height: 15px;
            border-radius: 50%; border: 2px solid #fff;
        }
        @media (min-width: 640px) { .avatar-badge { width: 18px; height: 18px; } }

        .profile-info          { flex: 1; min-width: 140px; padding-top: 8px; }
        .profile-info h2       { font-size: 0.95rem; font-weight: 800; color: #111827; }
        @media (min-width: 640px) { .profile-info h2 { font-size: 1.2rem; } }
        .profile-info .tagline { font-size: 0.72rem; color: #6b7280; margin-top: 2px; }
        @media (min-width: 640px) { .profile-info .tagline { font-size: 0.8rem; } }
        .profile-tags          { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; }

        .tag {
            background: #eff6ff; color: #2563eb;
            font-size: 0.68rem; font-weight: 700;
            padding: 3px 9px; border-radius: 999px;
            border: 1px solid #bfdbfe;
        }
        .tag.green { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }

        /* ── Field labels ── */
        .field-item label {
            display: block; font-size: 0.68rem; font-weight: 700;
            color: #9ca3af; text-transform: uppercase;
            letter-spacing: 0.06em; margin-bottom: 4px;
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
        .danger-row:last-child { border-bottom: none; }
        .danger-row-info p     { font-size: 0.8rem; font-weight: 700; color: #374151; }
        .danger-row-info span  { font-size: 0.72rem; color: #9ca3af; }

        .btn-danger {
            align-self: flex-start;
            background: #fff; border: 1.5px solid #fca5a5; color: #dc2626;
            padding: 7px 14px; border-radius: 9px;
            font-size: 0.75rem; font-weight: 700; cursor: pointer;
            transition: all 0.15s; white-space: nowrap; font-family: inherit; flex-shrink: 0;
        }
        @media (min-width: 480px) { .btn-danger { padding: 8px 16px; font-size: 0.78rem; align-self: auto; } }
        .btn-danger:hover { background: #fef2f2; border-color: #dc2626; }

        /* ── Edit Profile Drawer ── */
        #drawer {
            position: fixed; z-index: 50;
            transition: transform 0.3s ease;
            background: #fff;
            overflow-y: auto;
        }
        /* Mobile: bottom sheet */
        @media (max-width: 767px) {
            #drawer {
                bottom: 0; left: 0; right: 0; top: auto;
                width: 100%; max-height: 92vh;
                border-radius: 20px 20px 0 0;
                transform: translateY(100%);
                box-shadow: 0 -8px 40px rgba(0,0,0,0.15);
            }
            #drawer.open   { transform: translateY(0); }
            #drawer-handle { display: flex; }
        }
        /* Desktop: right side panel */
        @media (min-width: 768px) {
            #drawer {
                top: 0; right: 0; bottom: 0;
                width: 500px;
                transform: translateX(100%);
                box-shadow: -8px 0 40px rgba(0,0,0,0.1);
            }
            #drawer.open   { transform: translateX(0); }
            #drawer-handle { display: none; }
        }

        .drop-zone { transition: border-color 0.2s, background 0.2s; }
        .drop-zone.drag-over { border-color: #3b82f6 !important; background: #eff6ff !important; }

        .toggle-track {
            width: 40px; height: 22px; background: #e5e7eb;
            border-radius: 9999px; position: relative; cursor: pointer;
            transition: background 0.2s; flex-shrink: 0;
        }
        .toggle-track.on { background: #3b82f6; }
        .toggle-thumb {
            position: absolute; top: 3px; left: 3px;
            width: 16px; height: 16px; background: #fff; border-radius: 50%;
            transition: transform 0.22s cubic-bezier(0.34,1.56,0.64,1);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(18px); }
    </style>


    {{-- ── PAGE HEADER ── --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6 md:mb-8">
        <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Restaurant Profile</h1>
        <button id="openDrawer"
            class="self-start sm:self-auto bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg
                   text-xs md:text-sm font-bold shadow-lg hover:bg-blue-700">
            + Edit Profile
        </button>
    </header>


    {{-- ── PROFILE BANNER ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 md:mb-5 overflow-hidden">
        <div class="h-20 md:h-28 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 rounded-t-2xl"></div>
        <div class="profile-lower">
            <div class="avatar-wrap">
                <div class="avatar">🍜</div>
                <div class="avatar-badge"></div>
            </div>
            <div class="profile-info">
                <h2 id="profileName">Himalayan Kitchen</h2>
                <div class="tagline">Authentic Nepali &amp; Asian Cuisine · Since 2018</div>
                <div class="profile-tags">
                    <span class="tag green">● Open Now</span>
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
            </div>
            <div class="px-4 md:px-5 py-4 md:py-5 space-y-3 md:space-y-4">
                <div class="field-item">
                    <label>Restaurant Name</label>
                    <input class="field-input" type="text" id="fieldName" value="Himalayan Kitchen"
                        oninput="document.getElementById('profileName').textContent = this.value || 'Restaurant Name'" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Phone Number</label>
                        <input class="field-input" type="text" id="fieldPhone" value="+977 9801234567" />
                    </div>
                    <div class="field-item">
                        <label>Email</label>
                        <input class="field-input" type="email" id="fieldEmail" value="info@himalayan.np" />
                    </div>
                </div>
                <div class="field-item">
                    <label>Description</label>
                    <textarea class="field-input" id="fieldDesc" rows="3">Authentic flavors of Nepal brought to your table. From steaming momos to traditional thalis, we serve the best of Himalayan cuisine in the heart of Kathmandu.</textarea>
                </div>
            </div>
        </div>

        {{-- Financial Settings --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100">
                <h3 class="text-sm md:text-base font-extrabold text-gray-900">Financial Settings</h3>
            </div>
            <div class="px-4 md:px-5 py-4 md:py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Tax (%)</label>
                        <input type="number" id="tax_percentage" value="13" class="field-input" />
                    </div>
                    <div class="field-item">
                        <label>Service Charge (%)</label>
                        <input type="number" id="service_charge_percentage" value="10" class="field-input" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="tax_enabled"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded" checked />
                        <span class="text-xs md:text-sm text-gray-700 font-medium">Enable Tax</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="service_charge_enabled"
                            class="h-4 w-4 text-blue-600 border-gray-300 rounded" checked />
                        <span class="text-xs md:text-sm text-gray-700 font-medium">Enable Service Charge</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="field-item">
                        <label>Delivery Charge</label>
                        <input type="number" id="delivery_charge" value="50" class="field-input" />
                    </div>
                    <div class="field-item">
                        <label>Currency</label>
                        <input type="text" id="currency" value="NPR" class="field-input" />
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


    {{-- ── OVERLAY ── --}}
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    {{-- ── EDIT PROFILE DRAWER ── --}}
    <div id="drawer">

        {{-- Drag handle (mobile only) --}}
        <div id="drawer-handle" class="justify-center pt-3 pb-1">
            <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 rounded-xl p-2">
                    <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-sm md:text-base leading-tight">Edit Profile</h2>
                    <p class="text-xs text-gray-400">Update your restaurant details</p>
                </div>
            </div>
            <button id="closeDrawer"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="menuForm" class="modal-scroll px-4 md:px-5 py-4 md:py-5 space-y-4 md:space-y-5">

            {{-- Image upload --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Item Photo</label>
                <div id="dropZone"
                    class="drop-zone border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50
                           flex flex-col items-center justify-center gap-2 cursor-pointer py-6 md:py-8
                           hover:border-blue-400 hover:bg-blue-50/40"
                    onclick="document.getElementById('imageInput').click()"
                    ondragover="handleDragOver(event)"
                    ondragleave="handleDragLeave(event)"
                    ondrop="handleDrop(event)">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="h-5 w-5 md:h-6 md:w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-center px-4">
                        <p class="text-xs md:text-sm font-semibold text-gray-700">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400 mt-0.5">PNG, JPG up to 5MB</p>
                    </div>
                </div>
                <div id="imagePreview"
                    class="hidden relative rounded-2xl overflow-hidden border border-gray-200 shadow-sm"
                    style="height:150px;">
                    <img id="previewImg" src="" alt="Preview" class="w-full h-full object-cover"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end justify-between px-3 py-3">
                        <span id="previewFileName" class="text-white text-xs font-medium truncate max-w-[160px]"></span>
                        <button onclick="clearImage(event)"
                            class="bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Change
                        </button>
                    </div>
                </div>
                <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="handleImageSelect(event)"/>
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Category</label>
                <div class="relative">
                    <select name="category_id" id="categorySelect" class="field-input appearance-none pr-10">
                        <option value="">Loading categories…</option>
                    </select>
                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Name --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Item Name</label>
                <input id="menuName" type="text" name="name" placeholder="e.g. Chicken Momo" class="field-input"/>
            </div>

            {{-- Price + Availability --}}
            <div class="grid grid-cols-2 gap-3 md:gap-4">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs md:text-sm font-semibold select-none">Rs.</span>
                        <input id="menuPrice" type="number" name="price" min="0" placeholder="0"
                            class="field-input" style="padding-left: 38px;"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Availability</label>
                    <div class="flex items-center gap-2" style="height:42px;">
                        <div id="availToggle" class="toggle-track on" onclick="toggleAvailabilityForm()">
                            <div class="toggle-thumb"></div>
                            <input type="hidden" name="is_available" id="is_available" value="1">
                        </div>
                        <span id="availLabel" class="text-xs md:text-sm font-semibold text-blue-600">Available</span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">
                    Description <span class="text-gray-400 font-normal ml-1">(optional)</span>
                </label>
                <textarea id="menuDesc" rows="2" placeholder="Brief description of the item…"
                    class="field-input resize-none"></textarea>
            </div>

            {{-- Footer buttons --}}
            <div class="flex gap-3 pb-2">
                <button type="button" onclick="closeDrawer()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>

        </form>
    </div>


    <script>
        const token      = localStorage.getItem('auth_token');
        const restroSlug = localStorage.getItem('restro_url');

        // ── Drawer ────────────────────────────────────────────────────
        const drawer         = document.getElementById('drawer');
        const overlay        = document.getElementById('overlay');
        const form           = document.getElementById('menuForm');
        const categorySelect = document.getElementById('categorySelect');
        let editingMenuId    = null;

        function openDrawer(menu = null) {
            drawer.classList.add('open');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            if (menu) {
                editingMenuId    = menu.id;
                form.name.value  = menu.name;
                form.price.value = menu.price;
                setAvailability(menu.isAvailable);
                CategoryService.populateSelect(categorySelect, menu.category.id);
            } else {
                editingMenuId = null;
                form.reset();
                CategoryService.populateSelect(categorySelect);
            }
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            editingMenuId = null;
            form.reset();
        }

        document.getElementById('openDrawer').addEventListener('click', () => openDrawer());
        document.getElementById('closeDrawer').addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) document.body.classList.remove('overflow-hidden');
        });

        // ── Category service ──────────────────────────────────────────
        const CategoryService = {
            cache: null,
            async fetchCategories(forceRefresh = false) {
                if (this.cache && !forceRefresh) return this.cache;
                try {
                    const res  = await fetch('/api/v1/owner/restaurant/category', { headers: { 'Authorization': `Bearer ${token}` } });
                    const data = await res.json();
                    if (!res.ok) throw data;
                    this.cache = data.data;
                    return this.cache;
                } catch (err) { console.error('Failed to fetch categories', err); return []; }
            },
            async populateSelect(selectElement, selectedId = null) {
                const cats = await this.fetchCategories();
                selectElement.innerHTML = '<option value="">Select category</option>';
                if (!cats.length) { selectElement.innerHTML = '<option value="">No categories found</option>'; return; }
                cats.forEach(cat => {
                    const opt       = document.createElement('option');
                    opt.value       = cat.id;
                    opt.textContent = cat.name;
                    if (selectedId && cat.id === selectedId) opt.selected = true;
                    selectElement.appendChild(opt);
                });
            }
        };

        // ── Image handlers ────────────────────────────────────────────
        function handleImageSelect(e)  { const f = e.target.files[0]; if (f) loadImagePreview(f); }
        function handleDragOver(e)     { e.preventDefault(); document.getElementById('dropZone').classList.add('drag-over'); }
        function handleDragLeave()     { document.getElementById('dropZone').classList.remove('drag-over'); }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');
            const f = e.dataTransfer.files[0];
            if (f && f.type.startsWith('image/')) loadImagePreview(f);
        }
        function loadImagePreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('previewImg').src              = e.target.result;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('dropZone').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
        function clearImage(e) {
            e.stopPropagation();
            document.getElementById('imageInput').value = '';
            document.getElementById('previewImg').src   = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('dropZone').classList.remove('hidden');
        }

        // ── Availability toggle ───────────────────────────────────────
        function toggleAvailabilityForm() {
            const toggle = document.getElementById('availToggle');
            const label  = document.getElementById('availLabel');
            const hidden = document.getElementById('is_available');
            const isOn   = !toggle.classList.contains('on');
            toggle.classList.toggle('on', isOn);
            hidden.value      = isOn ? 1 : 0;
            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600',  !isOn);
        }
        function setAvailability(value) {
            const toggle = document.getElementById('availToggle');
            const label  = document.getElementById('availLabel');
            const hidden = document.getElementById('is_available');
            const isOn   = value == 1 || value === true;
            toggle.classList.toggle('on', isOn);
            hidden.value      = isOn ? 1 : 0;
            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600',  !isOn);
        }

        // ── Form submit ───────────────────────────────────────────────
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = {
                category_id:  Number(form.category_id.value),
                name:         form.name.value,
                price:        Number(form.price.value),
                is_available: Number(form.is_available.value),
            };
            const url    = editingMenuId ? `/api/v1/owner/restaurant/update-menu/${editingMenuId}` : '/api/v1/owner/restaurant/add-menu';
            const method = editingMenuId ? 'PATCH' : 'POST';
            try {
                const res  = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Something went wrong ❌', 'error'); return; }
                const msg = editingMenuId
                    ? `🎉 '${data.data.name}' updated!`
                    : `🎉 '${data.data.name}' added to ${data.data.category.name}!`;
                showToast(msg, 'success');
                closeDrawer();
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        });

        // ── Danger zone ───────────────────────────────────────────────
        async function pauseRestaurant() {
            if (!confirm('Hide your restaurant from the customer app until you re-open it?')) return;
            try {
                const res  = await fetch('/api/v1/owner/restaurant/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type':'application/json', 'Authorization':`Bearer ${token}`, 'Accept':'application/json' },
                    body: JSON.stringify({ is_active: false }),
                });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Failed ❌', 'error'); return; }
                showToast('Restaurant paused ⏸', 'warning');
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        }

        async function resetMenu() {
            const confirmed = prompt('Type RESET to confirm deleting all menu items:');
            if (confirmed !== 'RESET') { showToast('Reset cancelled', 'warning'); return; }
            try {
                const res  = await fetch('/api/v1/owner/restaurant/menu/reset', {
                    method: 'DELETE',
                    headers: { 'Authorization':`Bearer ${token}`, 'Accept':'application/json' },
                });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Failed ❌', 'error'); return; }
                showToast('All menu items deleted', 'error');
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        }

        async function deleteAccount() {
            const confirmed = prompt('Type DELETE to permanently delete your restaurant account:');
            if (confirmed !== 'DELETE') { showToast('Deletion cancelled', 'warning'); return; }
            try {
                const res  = await fetch('/api/v1/owner/restaurant', {
                    method: 'DELETE',
                    headers: { 'Authorization':`Bearer ${token}`, 'Accept':'application/json' },
                });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Failed ❌', 'error'); return; }
                showToast('Account deleted. Redirecting…', 'error');
                setTimeout(() => window.location.href = '/', 2000);
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        }
    </script>

@endsection