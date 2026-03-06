@extends('layouts.app')

@section('title', 'Menu | ' . config('app.name'))

@section('content')
    <style>
        .drop-zone {
            transition: border-color 0.2s, background 0.2s;
        }
        .drop-zone.drag-over {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
        }
        .toggle-track {
            width: 40px;
            height: 22px;
            background: #e5e7eb;
            border-radius: 9999px;
            position: relative;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .toggle-track.on { background: #3b82f6; }
        .toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-track.on .toggle-thumb { transform: translateX(18px); }

        /* ── Drawer: full-screen sheet on mobile, side panel on md+ ── */
        #drawer {
            position: fixed;
            z-index: 50;
            transition: transform 0.3s ease, bottom 0.3s ease;
        }

        /* Mobile: slide up from bottom */
        @media (max-width: 767px) {
            #drawer {
                bottom: 0;
                left: 0;
                right: 0;
                top: auto;
                width: 100%;
                transform: translateY(100%);
            }
            #drawer.open { transform: translateY(0); }
        }

        /* Desktop: slide in from right */
        @media (min-width: 768px) {
            #drawer {
                top: 0;
                right: 0;
                bottom: 0;
                width: 500px;
                transform: translateX(100%);
            }
            #drawer.open { transform: translateX(0); }
        }

        /* Table scroll wrapper on mobile */
        .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* Card view for menu items on very small screens */
        @media (max-width: 639px) {
            .menu-table-head { display: none; }
            .menu-tr {
                display: flex;
                flex-direction: column;
                padding: 12px 16px;
                border-bottom: 1px solid #f3f4f6;
                gap: 4px;
            }
            .menu-tr:hover { background: #f9fafb; }
            .menu-td { padding: 0 !important; }
            .menu-td::before {
                content: attr(data-label);
                display: inline-block;
                font-size: 0.65rem;
                font-weight: 700;
                text-transform: uppercase;
                color: #9ca3af;
                letter-spacing: 0.05em;
                margin-right: 6px;
                min-width: 80px;
            }
            .menu-td-actions { display: flex; gap: 12px; margin-top: 4px; }
            .menu-td-actions::before { display: none; }
        }
    </style>

    {{-- ── Header ── --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start mb-6 md:mb-8">
        <div>
            <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Menu Management</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                Manage <span id="activeItemCount" class="font-bold"></span> active items across
                <span id="categoryCount" class="font-bold"></span> categories
            </p>
        </div>
        <div class="flex gap-2">
            <button class="bg-white border border-gray-200 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold hover:bg-gray-50 whitespace-nowrap">
                Bulk Export
            </button>
            <button id="openDrawer"
                class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-bold shadow-lg hover:bg-blue-700 whitespace-nowrap">
                + Add Item
            </button>
        </div>
    </header>

    {{-- ── Category Filter ── --}}
    <div id="categoryButtons" class="flex flex-wrap gap-2 mb-5 md:mb-6">
        <button data-category="All"
            class="category-btn bg-blue-100 text-blue-700 px-3 md:px-4 py-1.5 md:py-2 rounded-full text-xs font-bold"
            onclick="filterByCategory('All')">
            All Items
        </button>
    </div>

    {{-- ── Menu Table ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="table-scroll max-h-[60vh] md:max-h-[73%] overflow-y-auto">
            <table class="w-full text-left min-w-[540px] sm:min-w-0">
                <thead class="menu-table-head bg-gray-50 border-b border-gray-100 sticky top-0">
                    <tr>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Category</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Price (NPR)</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody id="menuTableBody" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>

    {{-- ── Overlay ── --}}
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    {{-- ── Drawer ── --}}
    <div id="drawer">
        <div class="bg-white w-full md:w-[500px] md:h-screen rounded-t-2xl md:rounded-none shadow-2xl flex flex-col max-h-[92vh] md:max-h-screen overflow-hidden">

            {{-- Drawer header --}}
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                {{-- Drag handle (mobile only) --}}
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-gray-200 rounded-full md:hidden"></div>
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 id="drawerTitle" class="font-bold text-gray-800 text-sm md:text-base leading-tight">Add Menu Item</h2>
                        <p class="text-xs text-gray-400">Fill in the details below</p>
                    </div>
                </div>
                <button id="closeDrawer" type="button"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-1.5 md:p-2 transition">
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form id="menuForm" class="flex-1 overflow-y-auto px-4 md:px-5 py-4 md:py-5 space-y-4 md:space-y-5">

                {{-- Image upload --}}
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Item Photo</label>
                    <div id="dropZone"
                        class="drop-zone border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 flex flex-col items-center justify-center gap-2 cursor-pointer py-6 md:py-8 hover:border-blue-400 hover:bg-blue-50/40"
                        onclick="document.getElementById('imageInput').click()"
                        ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-5 w-5 md:h-6 md:w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-xs md:text-sm font-semibold text-gray-700">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG up to 5MB</p>
                        </div>
                    </div>
                    <div id="imagePreview" class="hidden relative rounded-2xl overflow-hidden border border-gray-200 shadow-sm" style="height:160px;">
                        <img id="previewImg" src="" alt="Preview" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end justify-between px-4 py-3">
                            <span id="previewFileName" class="text-white text-xs font-medium truncate max-w-xs"></span>
                            <button onclick="clearImage(event)"
                                class="bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Change
                            </button>
                        </div>
                    </div>
                    <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="handleImageSelect(event)" />
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <div class="relative">
                        <select name="category_id" id="categorySelect" class="field-input appearance-none pr-10 text-sm">
                            <option value="">Loading categories...</option>
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Item Name</label>
                    <input id="menuName" type="text" name="name" placeholder="e.g. Chicken Momo" class="field-input text-sm" />
                </div>

                {{-- Price + Availability --}}
                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs md:text-sm font-semibold select-none">Rs.</span>
                            <input id="menuPrice" type="number" name="price" min="0" placeholder="0"
                                class="field-input text-sm" style="padding-left: 38px;" />
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
                        Description
                        <span class="text-gray-400 font-normal ml-1">(optional)</span>
                    </label>
                    <textarea id="menuDesc" rows="2" placeholder="Brief description of the item..."
                        class="field-input resize-none text-sm"></textarea>
                </div>

                {{-- Footer buttons --}}
                <div class="pt-1 pb-2">
                    <div class="flex gap-3">
                        <button onclick="closeDrawer()" type="button"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-2.5 md:py-3 rounded-xl transition duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-sm font-bold py-2.5 md:py-3 rounded-xl transition duration-150 flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                            <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Item
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        let allMenuItems = [];
        let activeCategory = 'All';

        async function fetchMenu() {
            try {
                const restroSlug = localStorage.getItem('restro_url');
                const token = localStorage.getItem('auth_token');
                if (!restroSlug) throw new Error('Restaurant URL not found');
                if (!token) throw new Error('Auth token not found');

                const response = await fetch(`/api/v1/auth/restaurant/${restroSlug}/menu`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                allMenuItems = data.menu;

                document.getElementById('activeItemCount').textContent = data.active_item_count;
                document.getElementById('categoryCount').textContent = data.category_count;

                renderCategoryButtons(allMenuItems);
                renderMenuTable(allMenuItems);
            } catch (error) {
                console.error('Failed to fetch menu:', error);
            }
        }

        function renderCategoryButtons(menu) {
            const container = document.getElementById('categoryButtons');
            container.innerHTML = '';

            const allButton = document.createElement('button');
            allButton.textContent = 'All';
            allButton.dataset.category = 'All';
            allButton.className = 'category-btn bg-white border border-gray-200 text-gray-600 px-3 md:px-4 py-1.5 md:py-2 rounded-full text-xs font-bold hover:bg-gray-100';
            allButton.addEventListener('click', () => filterByCategory('All'));
            container.appendChild(allButton);

            const categories = [...new Set(menu.map(item => item?.category?.name).filter(Boolean))];
            categories.forEach(cat => {
                const button = document.createElement('button');
                button.textContent = cat;
                button.dataset.category = cat;
                button.className = 'category-btn bg-white border border-gray-200 text-gray-600 px-3 md:px-4 py-1.5 md:py-2 rounded-full text-xs font-bold hover:bg-gray-100';
                button.addEventListener('click', () => filterByCategory(cat));
                container.appendChild(button);
            });

            setActiveButton(activeCategory);
        }

        function filterByCategory(category) {
            activeCategory = category;
            const filtered = category === 'All' ? allMenuItems : allMenuItems.filter(item => item.category.name === category);
            renderMenuTable(filtered);
            setActiveButton(category);
        }

        function setActiveButton(category) {
            document.querySelectorAll('.category-btn').forEach(btn => {
                const active = btn.dataset.category === category;
                btn.classList.toggle('bg-blue-100', active);
                btn.classList.toggle('text-blue-700', active);
                btn.classList.toggle('bg-white', !active);
                btn.classList.toggle('text-gray-600', !active);
            });
        }

        function renderMenuTable(items) {
            const tbody = document.getElementById('menuTableBody');
            tbody.innerHTML = '';

            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'menu-tr hover:bg-gray-50 transition';
                tr.innerHTML = `
                    <td class="menu-td px-4 md:px-6 py-3 md:py-4" data-label="Item">
                        <div class="font-bold text-gray-800 text-sm">${item.name}</div>
                    </td>
                    <td class="menu-td px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-600" data-label="Category">
                        ${item.category.name}
                    </td>
                    <td class="menu-td px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm font-bold" data-label="Price">
                        Rs. ${item.price}
                    </td>
                    <td class="menu-td px-4 md:px-6 py-3 md:py-4" data-label="Stock">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold ${item.isAvailable ? 'bg-green-100 text-green-700 italic' : 'bg-red-100 text-red-700 italic'}">
                            ● ${item.isAvailable ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </td>
                    <td class="menu-td menu-td-actions px-4 md:px-6 py-3 md:py-4 text-xs md:text-sm text-blue-600 font-bold" data-label="Actions">
                        <button data-menu='${JSON.stringify(item)}' class="editBtn hover:underline">Edit</button>
                        <button data-menu-id='${item.id}' data-menu-status='${item.isAvailable ? 'true' : 'false'}'
                            class="availabilityBtn ${item.isAvailable ? 'text-red-400' : 'text-green-500'} hover:underline">
                            ${item.isAvailable ? 'Disable' : 'Enable'}
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
                tr.querySelector('.editBtn').addEventListener('click', function() {
                    openDrawer(JSON.parse(this.dataset.menu));
                });
            });
        }

        fetchMenu();

        // ---------- Drawer ----------
        const drawer  = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const form    = document.getElementById('menuForm');
        const categorySelect  = document.getElementById('categorySelect');
        const openDrawerBtn   = document.getElementById('openDrawer');
        const closeDrawerBtn  = document.getElementById('closeDrawer');
        let editingMenuId = null;
        const token = localStorage.getItem('auth_token');

        const CategoryService = {
            cache: null,
            async fetchCategories(forceRefresh = false) {
                if (this.cache && !forceRefresh) return this.cache;
                try {
                    const res = await fetch('/api/v1/owner/restaurant/category', {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const data = await res.json();
                    if (!res.ok) throw data;
                    this.cache = data.data;
                    return this.cache;
                } catch (err) { console.error('Failed to fetch categories', err); return []; }
            },
            async populateSelect(selectElement, selectedId = null) {
                const categories = await this.fetchCategories();
                selectElement.innerHTML = '<option value="">Select category</option>';
                if (!categories.length) { selectElement.innerHTML = '<option value="">No categories found</option>'; return; }
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    if (selectedId && cat.id === selectedId) option.selected = true;
                    selectElement.appendChild(option);
                });
            }
        };

        function openDrawer(menu = null) {
            drawer.classList.add('open');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            const title = document.getElementById('drawerTitle');
            if (menu) {
                editingMenuId = menu.id;
                title.textContent = 'Edit Menu Item';
                form.name.value  = menu.name;
                form.price.value = menu.price;
                setAvailability(menu.isAvailable);
                CategoryService.populateSelect(categorySelect, menu.category.id);
            } else {
                editingMenuId = null;
                title.textContent = 'Add Menu Item';
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

        openDrawerBtn.addEventListener('click', () => openDrawer());
        closeDrawerBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        // Image handlers
        function handleImageSelect(e) { const f = e.target.files[0]; if (f) loadImagePreview(f); }
        function handleDragOver(e)  { e.preventDefault(); document.getElementById('dropZone').classList.add('drag-over'); }
        function handleDragLeave()  { document.getElementById('dropZone').classList.remove('drag-over'); }
        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');
            const f = e.dataTransfer.files[0];
            if (f && f.type.startsWith('image/')) loadImagePreview(f);
        }
        function loadImagePreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('dropZone').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
        function clearImage(e) {
            e.stopPropagation();
            document.getElementById('imageInput').value = '';
            document.getElementById('previewImg').src = '';
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('dropZone').classList.remove('hidden');
        }

        function toggleAvailabilityForm() {
            const toggle = document.getElementById('availToggle');
            const label  = document.getElementById('availLabel');
            const hidden = document.getElementById('is_available');
            const isOn   = !toggle.classList.contains('on');
            toggle.classList.toggle('on', isOn);
            hidden.value = isOn ? 1 : 0;
            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600', !isOn);
        }

        function setAvailability(value) {
            const toggle = document.getElementById('availToggle');
            const label  = document.getElementById('availLabel');
            const hidden = document.getElementById('is_available');
            const isOn   = value == 1 || value === true;
            toggle.classList.toggle('on', isOn);
            hidden.value = isOn ? 1 : 0;
            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600', !isOn);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = {
                category_id: Number(form.category_id.value),
                name: form.name.value,
                price: Number(form.price.value),
                is_available: Number(form.is_available.value)
            };
            const url    = editingMenuId ? `/api/v1/owner/restaurant/update-menu/${editingMenuId}` : '/api/v1/owner/restaurant/add-menu';
            const method = editingMenuId ? 'PATCH' : 'POST';
            try {
                const res  = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` }, body: JSON.stringify(payload) });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Something went wrong ❌', 'error'); return; }
                showToast(editingMenuId ? `🎉 '${data.data.name}' updated!` : `🎉 '${data.data.name}' added to ${data.data.category.name}!`, 'success');
                closeDrawer();
                fetchMenu();
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        });

        document.getElementById('menuTableBody').addEventListener('click', function(e) {
            if (e.target.classList.contains('availabilityBtn')) {
                toggleAvailability(e.target.dataset.menuId, e.target.dataset.menuStatus === 'true');
            }
        });

        async function toggleAvailability(menuID, currentStatus) {
            try {
                const res  = await fetch(`/api/v1/owner/restaurant/menu/${menuID}/availability`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                    body: JSON.stringify({ is_available: !currentStatus })
                });
                const data = await res.json();
                if (!data.success) { showToast(data.message || 'Something went wrong ❌', 'error'); return; }
                showToast(`'${data.data.name}' availability updated!`, 'success');
                fetchMenu();
            } catch (err) { console.error(err); showToast('Something went wrong ❌', 'error'); }
        }
    </script>

@endsection