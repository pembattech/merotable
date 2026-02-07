@extends('layouts.app')

@section('title', 'Menu | ' . config('app.name'))

@section('content')
    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Menu Management</h1>
            <p class="text-sm text-gray-500">
                Manage <span id="activeItemCount" class="font-bold"></span> active items across
                <span id="categoryCount" class="font-bold"></span> categories
            </p>
        </div>
        <div class="flex space-x-3">
            <button class="bg-white border border-gray-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">Bulk
                Export</button>
            <button id="openDrawer"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-blue-700">+
                Add
                New Food Item</button>
        </div>
    </header>

    <!-- Category Filter Buttons -->
    <div id="categoryButtons" class="flex space-x-4 mb-6">
        <button data-category="All" class="category-btn bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-xs font-bold"
            onclick="filterByCategory('All')">
            All Items
        </button>
    </div>

    <!-- Menu Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Item Name</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Price (NPR)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Stock Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="menuTableBody" class="divide-y divide-gray-100">
                <!-- Table rows injected here -->
            </tbody>
        </table>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- Drawer -->
    <div id="drawer"
        class="fixed top-0 right-0 h-full w-[400px] bg-white shadow-xl
               translate-x-full transition-transform duration-300 z-50">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <h2 class="text-lg font-semibold">Add Menu Item</h2>
            <button id="closeDrawer" class="text-gray-500 hover:text-black">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form id="menuForm" class="p-4 space-y-4">
            <div>
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Category
                    </label>

                    <select name="category_id" id="categorySelect" class="w-full border rounded px-3 py-2" required>
                        <option value="">Loading categories...</option>
                    </select>
                </div>

            </div>

            <div>
                <label class="block text-sm font-medium">Menu Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Price</label>
                <input type="number" name="price" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_available" checked>
                <span class="text-sm">Available</span>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                Save Menu
            </button>
        </form>
    </div>

    <script>
        let allMenuItems = [];
        let activeCategory = 'All'; // Track the selected category

        async function fetchMenu() {
            try {
                const restroSlug = localStorage.getItem('restro_url');
                const token = localStorage.getItem('auth_token');

                if (!restroSlug) throw new Error('Restaurant URL not found');
                if (!token) throw new Error('Auth token not found');

                const response = await fetch(`/api/v1/auth/restaurant/${restroSlug}/menu`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                allMenuItems = data.menu;

                // Update counts
                document.getElementById('activeItemCount').textContent = data.active_item_count;
                document.getElementById('categoryCount').textContent = data.category_count;

                // Render category buttons
                renderCategoryButtons(allMenuItems);

                // Render table with all items
                renderMenuTable(allMenuItems);

            } catch (error) {
                console.error('Failed to fetch menu:', error);
            }
        }

        function renderCategoryButtons(menu) {
            const container = document.getElementById('categoryButtons');
            const categories = [...new Set(menu.map(item => item.category.name))];

            categories.forEach(cat => {
                const button = document.createElement('button');
                button.textContent = cat;
                button.dataset.category = cat;
                button.className =
                    'category-btn bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-xs font-bold hover:bg-gray-100';
                button.addEventListener('click', () => filterByCategory(cat));
                container.appendChild(button);
            });

            // Set the first "All Items" button as active
            setActiveButton(activeCategory);
        }

        function filterByCategory(category) {
            activeCategory = category;
            const filtered = category === 'All' ? allMenuItems : allMenuItems.filter(item => item.category.name ===
                category);
            renderMenuTable(filtered);
            setActiveButton(category);
        }

        function setActiveButton(category) {
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => {
                if (btn.dataset.category === category) {
                    btn.classList.add('bg-blue-100', 'text-blue-700');
                    btn.classList.remove('bg-white', 'text-gray-600');
                } else {
                    btn.classList.remove('bg-blue-100', 'text-blue-700');
                    btn.classList.add('bg-white', 'text-gray-600');
                }
            });
        }

        function renderMenuTable(items) {
            const tbody = document.getElementById('menuTableBody');
            tbody.innerHTML = '';

            items.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition';
                tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800">${item.name}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">${item.category.name}</td>
            <td class="px-6 py-4 text-sm font-bold">Rs. ${item.price}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold ${item.isAvailable ? 'bg-green-100 text-green-700 italic' : 'bg-red-100 text-red-700 italic'}">
                    ● ${item.isAvailable ? 'In Stock' : 'Out of Stock'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-blue-600 font-bold space-x-3">
                <button data-menu='${JSON.stringify(item)}' class="editBtn hover:underline">Edit</button>
                <button data-menu-id='${item.id}' data-menu-status='${item.isAvailable ? 'true' : 'false'}' class="availabilityBtn ${item.isAvailable ? 'text-red-400' : 'text-green-500'} hover:underline">${item.isAvailable ? 'Disable' : 'Enable'}</button>
            </td>
        `;
                tbody.appendChild(tr);


                // Attach listener here
                tr.querySelector('.editBtn').addEventListener('click', function() {
                    const menu = JSON.parse(this.dataset.menu);
                    openDrawer(menu);
                });


            });
        }

        fetchMenu();

        // ---------- Drawer Elements ----------
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const form = document.getElementById('menuForm');
        const categorySelect = document.getElementById('categorySelect');
        const openDrawerBtn = document.getElementById('openDrawer');
        const closeDrawerBtn = document.getElementById('closeDrawer');

        let editingMenuId = null; // null = Add, otherwise Edit

        const token = localStorage.getItem('auth_token');

        // ---------- Category Service ----------
        const CategoryService = {
            cache: null,
            async fetchCategories(forceRefresh = false) {
                if (this.cache && !forceRefresh) return this.cache;

                try {
                    const res = await fetch('/api/v1/owner/restaurant/category', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) throw data;
                    this.cache = data.data;
                    return this.cache;
                } catch (err) {
                    console.error('Failed to fetch categories', err);
                    return [];
                }
            },
            async populateSelect(selectElement, selectedId = null) {
                const categories = await this.fetchCategories();
                selectElement.innerHTML = '<option value="">Select category</option>';
                if (categories.length === 0) {
                    selectElement.innerHTML = '<option value="">No categories found</option>';
                    return;
                }
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    if (selectedId && cat.id === selectedId) option.selected = true;
                    selectElement.appendChild(option);
                });
            }
        };

        // ---------- Drawer Functions ----------
        function openDrawer(menu = null) {
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');

            if (menu) {
                // Edit mode
                editingMenuId = menu.id;
                form.name.value = menu.name;
                form.price.value = menu.price;
                form.is_available.checked = menu.is_available;
                CategoryService.populateSelect(categorySelect, menu.category.id);
            } else {
                // Add mode
                editingMenuId = null;
                form.reset();
                CategoryService.populateSelect(categorySelect);
            }
        }

        function closeDrawer() {
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            editingMenuId = null;
            form.reset();
        }



        openDrawerBtn.addEventListener('click', () => openDrawer());
        closeDrawerBtn.addEventListener('click',
            closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        // ---------- Form Submit (Add/Edit) ----------
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const payload = {
                category_id: Number(form.category_id.value),
                name: form.name.value,
                price: Number(form.price.value),
                is_available: form.is_available.checked
            };

            let url = '/api/v1/owner/restaurant/add-menu';
            let method = 'POST';

            if (editingMenuId) {
                url = `/api/v1/owner/restaurant/update-menu/${editingMenuId}`;
                method = 'PATCH';
            }

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok) throw data;

                // TODO: Display success message, refresh menu list
                alert(editingMenuId ? 'Menu updated ✅' : 'Menu added ✅');
                closeDrawer();

                fetchMenu();

            } catch (err) {
                console.error(err);
                // TODO: Display message instead of alert.
                alert('Something went wrong ❌');
            }
        });


        // Toggle availability
        document.getElementById('menuTableBody').addEventListener('click', function(e) {
            if (e.target.classList.contains('availabilityBtn')) {
                toggleAvailability(e.target.dataset.menuId, e.target.dataset.menuStatus === 'true');
            }
        });


        async function toggleAvailability(menuID, currentStatus) {
            const token = localStorage.getItem('auth_token');

            try {
                const res = await fetch(`/api/v1/owner/restaurant/menu/${menuID}/availability`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({
                        is_available: !currentStatus
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    throw data;
                }

                console.log(data);

                alert('Availability updated ✅');

                fetchMenu(); // refresh table
            } catch (err) {
                console.error(err);
                alert('Something went wrong ❌');
            }
        }
    </script>











































































    {{-- 
    <!-- SCRIPT -->
    <script>
        const categoryService = new CategoryService();
        const categorySelect = document.getElementById('categorySelect');
        // console.log(categoryService);

        async function loadCategories() {
            const token = localStorage.getItem('auth_token');

            try {
                const response = await fetch(
                    '/api/v1/owner/restaurant/categories', {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                const select = document.getElementById('categorySelect');

                select.innerHTML = '<option value="">Select category</option>';

                result.data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });

            } catch (error) {
                console.error('Failed to load categories', error);

                document.getElementById('categorySelect').innerHTML =
                    '<option value="">Failed to load</option>';
            }
        }

        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const openBtn = document.getElementById('openAddDrawer');
        const closeBtn = document.getElementById('closeAddDrawer');
        const form = document.getElementById('menuAddForm');

        // Open drawer
        openBtn.addEventListener('click', () => {
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');

            await loadCategories();
        });

        // Close drawer
        function closeDrawer() {
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        }

        closeBtn.addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        // Submit form using async fetch
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const token = localStorage.getItem('auth_token');

            const payload = {
                category_id: Number(form.category_id.value),
                name: form.name.value,
                price: Number(form.price.value),
                is_available: form.is_available.checked
            };

            try {
                const response = await fetch(
                    '/api/v1/owner/restaurant/add-menu', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify(payload)
                    }
                );

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                console.log('Menu added:', data);

                alert('Menu added successfully ✅');
                form.reset();
                closeDrawer();

                // OPTIONAL:
                // refreshMenuList();

            } catch (error) {
                console.error(error);
                alert('Something went wrong ❌');
            }
        });
    </script>
 --}}

@endsection
