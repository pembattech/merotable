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
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-blue-700">+ Add
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
                <button class="hover:underline">Edit</button>
                <button class="${item.isAvailable ? 'text-red-400' : 'text-green-500'} hover:underline">${item.isAvailable ? 'Disable' : 'Enable'}</button>
            </td>
        `;
                tbody.appendChild(tr);
            });
        }

        fetchMenu();
    </script>


@endsection
