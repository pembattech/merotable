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

        .cat-active {
            background-color: #1e293b;
            color: white;
        }

        .cat-not-active:hover {
            background: #f1f5f9;
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

        .toggle-track.on {
            background: #3b82f6;
        }

        .toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .toggle-track.on .toggle-thumb {
            transform: translateX(18px);
        }

        #menuTableBody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Drawer: bottom sheet on mobile, side panel on md+ */
        #drawer {
            position: fixed;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        @media (max-width: 767px) {
            #drawer {
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                transform: translateY(100%);
            }

            #drawer.open {
                transform: translateY(0);
            }
        }

        @media (min-width: 768px) {
            #drawer {
                top: 0;
                right: 0;
                bottom: 0;
                width: 500px;
                transform: translateX(100%);
            }

            #drawer.open {
                transform: translateX(0);
            }
        }

        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Import button animations */
        #importButton .import-icon {
            transition: transform 0.25s ease;
        }

        #importButton:hover .import-icon {
            animation: import-bounce 0.8s ease-in-out infinite;
        }

        #importButton:active {
            transform: scale(0.96);
        }

        #importButton.is-loading {
            pointer-events: none;
            opacity: 0.85;
        }

        #importButton.is-loading .import-icon {
            animation: none;
            display: none;
        }

        #importButton .import-spinner {
            display: none;
        }

        #importButton.is-loading .import-spinner {
            display: inline-block;
            animation: import-spin 0.8s linear infinite;
        }

        #importButton.is-loading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 40%;
            background: #6366f1;
            border-radius: 9999px;
            animation: import-progress 1.1s ease-in-out infinite;
        }

        #importButton.is-done {
            animation: import-pop 0.5s ease;
            border-color: #22c55e;
            color: #15803d;
        }

        @keyframes import-bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        @keyframes import-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes import-progress {
            0% {
                left: -40%;
            }

            100% {
                left: 100%;
            }
        }

        @keyframes import-pop {
            0% {
                transform: scale(1);
            }

            40% {
                transform: scale(1.08);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Availability filter select */
        .filter-select-wrap {
            position: relative;
            display: inline-block;
            flex-shrink: 0;
        }

        .filter-select-wrap::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            width: 7px;
            height: 7px;
            border-right: 2px solid #6b7280;
            border-bottom: 2px solid #6b7280;
            transform: translateY(-70%) rotate(45deg);
            pointer-events: none;
            transition: transform 0.2s, border-color 0.2s;
        }

        .filter-select-wrap:focus-within::after {
            border-color: #4f46e5;
            transform: translateY(-30%) rotate(225deg);
        }

        #availabilityFilter {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            padding-right: 2.25rem;
            font-weight: 600;
            color: #374151;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        #availabilityFilter:hover {
            background: #f9fafb;
            border-color: #a5b4fc;
        }

        #availabilityFilter:focus {
            outline: none;
        }

        #availabilityFilter.is-filtered {
            background: #eef2ff;
            border-color: #818cf8;
            color: #4338ca;
        }
    </style>

    {{-- Header --}}
    <header class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start mb-6 md:mb-8 fade-up d1">
        <div>
            <h1 class="text-lg md:text-2xl font-extrabold text-gray-800">Menu Management</h1>
            <p class="text-xs md:text-sm text-gray-500 mt-0.5">
                Manage <span id="activeItemCount" class="font-bold"> 00 </span> active items across
                <span id="categoryCount" class="font-bold"> 0 </span> categories
            </p>
        </div>
        <div class="flex gap-2">
            <button type="button"
                class="bg-white border border-gray-200 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold hover:bg-gray-50 whitespace-nowrap">
                Bulk Export
            </button>
            <button id="importButton" type="button"
                class="relative overflow-hidden inline-flex items-center gap-1.5 bg-white border border-indigo-200 text-indigo-700 px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold hover:bg-indigo-50 hover:border-indigo-300 transition whitespace-nowrap">
                <svg class="import-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0-12l-4 4m4-4l4 4" />
                </svg>
                <svg class="import-spinner w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25" />
                    <path d="M22 12a10 10 0 00-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                </svg>
                <span id="importButtonLabel">Import</span>
            </button>
            <button id="openDrawer" type="button"
                class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-bold shadow-lg hover:bg-blue-700 whitespace-nowrap">
                + Add Item
            </button>
        </div>
    </header>


    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5 md:mb-6 fade-up d8">

        {{-- Category filter --}}
        <div id="categories" class="bg-white w-fit border border-gray-200 rounded-2xl p-3 mb-3">
            <div id="categoryButtons"
            class="bg-gray-50 w-fit border border-gray-200 rounded-xl p-1 flex flex-col lg:flex-row lg:items-center gap-3">
        </div>
    </div>

     <div class="filter-select-wrap">
            <select id="availabilityFilter" aria-label="Filter by availability" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <option value="all">All Items</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>
        </div>
    
</div>

    {{-- Menu table --}}
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden fade-up d8">
        <div class="">
            <table class="w-full text-left table-fixed min-w-[640px]">
                <thead class="table w-full table-fixed bg-gray-50 border-b border-gray-100 sticky top-0">
                    <tr>
                        <th class="w-[30%] px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Item Name
                        </th>
                        <th class="w-[20%] px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Category
                        </th>
                        <th class="w-[30%] px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Price (NPR)
                        </th>
                        <th class="w-[10%] px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="w-[10%] px-4 md:px-6 py-3 md:py-4 text-xs font-bold text-gray-500 uppercase text-right">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="menuTableBody" class="table-scroll block max-h-[53vh] overflow-y-auto divide-y divide-gray-100">
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hidden CSV import form (triggered by the Import button) --}}
    <form id="menuImportForm" class="hidden">
        <input id="menuCsv" name="file" type="file" accept=".csv,text/csv" required>
    </form>

    {{-- Overlay --}}
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    {{-- Drawer --}}
    <div id="drawer">
        <div class="relative bg-white w-full md:w-[500px] md:h-screen rounded-t-2xl md:rounded-none shadow-2xl
                                                    flex flex-col max-h-[92vh] md:max-h-screen overflow-hidden">

            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-gray-200 rounded-full md:hidden">
            </div>

            {{-- Drawer header --}}
            <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 id="drawerTitle" class="font-bold text-gray-800 text-sm md:text-base leading-tight">Add Menu
                            Item</h2>
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
            <form id="menuForm" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto px-4 md:px-5 py-4 md:py-5 space-y-5">

                {{-- Photo --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Item Photo <span class="text-gray-400 font-normal">(optional)</span>
                    </label>

                    <div id="dropZone" class="drop-zone border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50
                                                                flex flex-col items-center justify-center gap-2 cursor-pointer py-6
                                                                hover:border-indigo-400 hover:bg-indigo-50/40 transition"
                        onclick="document.getElementById('imageInput').click()" ondragover="handleDragOver(event)"
                        ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                        <div class="w-11 h-11 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400">PNG, JPG or WEBP · Max 5MB</p>
                    </div>

                    <div id="imagePreview" class="hidden relative mt-3 rounded-xl overflow-hidden border border-gray-200"
                        style="height:160px;">
                        <img id="previewImg" src="" alt="Item preview" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end justify-between p-3">
                            <span id="previewFileName" class="text-white text-xs truncate max-w-[70%]"></span>
                            <button type="button" onclick="clearImage(event)"
                                class="bg-white/20 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                Change
                            </button>
                        </div>
                    </div>

                    <input type="file" id="imageInput" name="image" accept="image/png,image/jpeg,image/webp" class="hidden"
                        onchange="handleImageSelect(event)">
                </div>

                {{-- Category --}}
                <div>
                    <label for="categorySelect" class="block text-sm font-semibold text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="categorySelect" class="field-input text-sm" required>
                        <option value="">Select a category</option>
                    </select>
                </div>

                {{-- Name --}}
                <div>
                    <label for="menuName" class="block text-sm font-semibold text-gray-700 mb-2">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input id="menuName" type="text" name="name" placeholder="e.g. Chicken Momo" maxlength="150" required
                        class="field-input text-sm">
                </div>

                {{-- Pricing type --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pricing Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="pricing_mode" value="single" checked class="peer sr-only"
                                onchange="updatePricingMode()">
                            <div
                                class="border border-gray-200 rounded-xl p-3 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                                <p class="text-sm font-semibold text-gray-800">Single Price</p>
                                <p class="text-xs text-gray-500 mt-1">One price per item</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="pricing_mode" value="sizes" class="peer sr-only"
                                onchange="updatePricingMode()">
                            <div
                                class="border border-gray-200 rounded-xl p-3 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                                <p class="text-sm font-semibold text-gray-800">Multiple Sizes</p>
                                <p class="text-xs text-gray-500 mt-1">Different prices by size</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Single price --}}
                <div id="singlePriceSection">
                    <label for="menuPrice" class="block text-sm font-semibold text-gray-700 mb-2">
                        Item Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-semibold">Rs.</span>
                        <input id="menuPrice" type="number" name="price" min="0" step="0.01" placeholder="0.00" required
                            class="field-input text-sm" style="padding-left:48px;">
                    </div>
                </div>

                {{-- Multiple sizes --}}
                <div id="sizePricingSection" class="hidden space-y-4">
                    <div>
                        <label for="sizeType" class="block text-sm font-semibold text-gray-700 mb-2">Size
                            Options</label>
                        <select id="sizeType" name="size_type" class="field-input text-sm" onchange="renderSizePrices()">
                            <option value="hf">Half / Full</option>
                            <option value="sml">Small / Medium / Large</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1.5">Enter the price for each serving size.</p>
                    </div>

                    <div id="sizePriceFields" class="space-y-3"></div>

                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-3">
                        <p class="text-xs text-blue-800">
                            <strong>Tip:</strong> Each size will have its own price.
                            Staff can select the required size when taking an order.
                        </p>
                    </div>
                </div>

                {{-- Item settings --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-3">Item Settings</p>
                    <div class="space-y-3">
                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 p-3 cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Vegetarian</p>
                                <p class="text-xs text-gray-500">Mark this item as vegetarian</p>
                            </div>
                            <input type="checkbox" name="is_vegetarian" value="1" class="w-4 h-4 accent-indigo-600 rounded">
                        </label>

                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 p-3 cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Popular Item</p>
                                <p class="text-xs text-gray-500">Highlight this item in the menu</p>
                            </div>
                            <input type="checkbox" name="is_popular" value="1" class="w-4 h-4 accent-indigo-600 rounded">
                        </label>

                        <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">Item Availability</p>
                                <p class="text-xs text-gray-500">Control whether this item can be ordered
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div id="availToggle" role="switch" aria-checked="true" tabindex="0" class="toggle-track on"
                                    onclick="toggleAvailabilityForm()"
                                    onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggleAvailabilityForm(); }">
                                    <div class="toggle-thumb"></div>
                                </div>
                                <span id="availLabel" class="text-xs font-semibold text-indigo-600">Available</span>
                            </div>
                            <input type="hidden" name="is_available" id="is_available" value="1">
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="menuDesc" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea id="menuDesc" name="description" rows="3" maxlength="1000"
                        placeholder="Describe ingredients, taste, or preparation..."
                        class="field-input resize-none text-sm"></textarea>
                </div>

                {{-- Footer --}}
                <div class="pt-3 pb-2 border-t border-gray-100">
                    <div class="flex gap-3">
                        <button onclick="closeDrawer()" type="button"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-3 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" id="saveMenuButton"
                            class="flex-[2] bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white text-sm font-bold
                                                                       py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        let editingMenuId = null;

        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const form = document.getElementById('menuForm');
        const categorySelect = document.getElementById('categorySelect');
        const saveMenuButton = document.getElementById('saveMenuButton');

        const token = localStorage.getItem('auth_token');
        const restroSlug = localStorage.getItem('restro_url');

        const MENU_SIZE_OPTIONS = {
            hf: [
                { name: 'Half', code: 'half' },
                { name: 'Full', code: 'full' }
            ],
            sml: [
                { name: 'Small', code: 'small' },
                { name: 'Medium', code: 'medium' },
                { name: 'Large', code: 'large' }
            ]
        };

        /* ───────── Helpers ───────── */

        async function apiRequest(url, options = {}) {
            if (!token) {
                throw new Error('Authentication token not found. Please log in again.');
            }

            const response = await fetch(url, {
                ...options,
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                    ...options.headers
                }
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    throw new Error(Object.values(data.errors).flat().join('\n'));
                }
                if (response.status === 401) {
                    throw new Error('Your session has expired. Please log in again.');
                }
                throw new Error(data.message || 'Something went wrong.');
            }

            return data;
        }

        function showMessage(message, type = 'success') {
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                alert(message);
            }
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[char]);
        }

        function formatPrice(price) {
            return `Rs. ${Number(price || 0).toLocaleString('en-IN')}`;
        }

        function isAvailable(item) {
            return Boolean(item.is_available ?? item.isAvailable ?? true);
        }

        function getMenuSizes(item) {
            const sizes = item.sizes || item.menu_item_sizes || [];

            return Array.isArray(sizes)
                ? [...sizes].sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0))
                : [];
        }

        function getSizeType(item) {
            if (item.size_type) return item.size_type;

            const names = getMenuSizes(item).map(size => size.name?.toLowerCase());

            if (names.includes('large') || names.includes('small') || names.includes('medium')) return 'sml';
            if (names.includes('half') || names.includes('full')) return 'hf';

            return null;
        }

        /* ───────── Load menu ───────── */

        async function fetchMenu() {
            try {
                const data = await apiRequest(
                    `/api/v1/auth/restaurant/${encodeURIComponent(restroSlug)}/menu`
                );

                allMenuItems = Array.isArray(data.menu) ? data.menu : [];

                document.getElementById('activeItemCount').textContent =
                    data.active_item_count ?? allMenuItems.filter(isAvailable).length;

                document.getElementById('categoryCount').textContent =
                    data.category_count ?? new Set(
                        allMenuItems.map(item => item.category?.id).filter(Boolean)
                    ).size;

                renderCategoryButtons();
                filterByCategory(activeCategory);
            } catch (error) {
                console.error('Failed to load menu:', error);
                showMessage(error.message, 'error');
            }
        }

        /* ───────── Category filters ───────── */

        function renderCategoryButtons() {
            const container = document.getElementById('categoryButtons');
            container.replaceChildren();

            const categories = [
                'All',
                ...new Set(allMenuItems.map(item => item.category?.name).filter(Boolean))
            ];

            // Fall back to "All" if the active category no longer exists.
            if (!categories.includes(activeCategory)) activeCategory = 'All';

            categories.forEach(category => {
                const button = document.createElement('button');

                button.type = 'button';
                button.dataset.category = category;
                button.textContent = category === 'All' ? 'All Items' : category;
                button.className =
                    'category-btn cat-not-active px-4 py-[6px] rounded-lg text-[0.85rem] font-semibold text-gray-500 transition-all duration-150 cursor-pointer border-0 bg-transparent whitespace-nowrap';

                button.addEventListener('click', () => filterByCategory(category));
                container.appendChild(button);
            });

            setActiveButton(activeCategory);
        }

        function filterByCategory(category) {
            activeCategory = category;

            const filtered = category === 'All'
                ? allMenuItems
                : allMenuItems.filter(item => item.category?.name === category);

            renderMenuTable(filtered);
            setActiveButton(category);
        }

        function setActiveButton(category) {
            document.querySelectorAll('.category-btn').forEach(button => {
                const active = button.dataset.category === category;

                button.classList.toggle('cat-active', active);
                button.classList.toggle('cat-not-active', !active);
            });
        }

        /* ───────── Menu table ───────── */

        function renderMenuPrice(item) {
            const sizes = getMenuSizes(item);
            const pricingMode = item.pricing_mode || (sizes.length ? 'sizes' : 'single');

            if (pricingMode === 'sizes' && sizes.length) {
                return `
                                            <div class="flex flex-wrap items-center gap-2 min-w-[180px]">
                                                ${sizes.map((size, index) => `
                                                    ${index > 0 ? `
                                                        <div class="h-8 border-l border-gray-200"></div>
                                                    ` : ''}

                                                    <div class="px-2.5 py-1.5">
                                                        <p class="text-sm font-bold text-gray-900">
                                                            ${formatPrice(size.price)}
                                                        </p>
                                                        <p class="text-xs font-medium text-gray-400">
                                                            ${escapeHtml(size.name || size.code || 'Size')}
                                                        </p>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        `;
            }

            return `
                                        <div>
                                            <p class="font-semibold text-gray-900">
                                                ${formatPrice(item.price)}
                                            </p>
                                            <span class="text-xs text-gray-400">Single price</span>
                                        </div>
                                    `;
        }

        function renderMenuTable(items = allMenuItems) {
            const tbody = document.getElementById('menuTableBody');
            if (!tbody) return;

            if (!items.length) {
                tbody.innerHTML = `
                                                    <tr>
                                                        <td colspan="5" class="px-6 py-12 text-center">
                                                            <p class="font-medium text-gray-500">No menu items found</p>
                                                            <p class="mt-1 text-sm text-gray-400">Add a menu item to get started.</p>
                                                        </td>
                                                    </tr>
                                                `;
                return;
            }

            tbody.innerHTML = items.map(item => {
                const available = isAvailable(item);
                const vegetarian = Boolean(item.isVegetarian ?? item.is_vegetarian);
                const popular = Boolean(item.isPopular ?? item.is_popular);
                const categoryName = item.category?.name || item.category_name || 'Uncategorized';
                const sizes = getMenuSizes(item);
                const hasSizes = item.pricing_mode === 'sizes' || sizes.length > 0;

                const imageUrl = item.image
                    ? `/storage/${item.image}`
                    : null;


                return `
                                                    <tr class="hover:bg-gray-50/70">
                                                        <td class="px-4 md:px-6 py-4 w-[30%]">
                                                            <div class="flex items-start gap-3">
                                                                <div class="menu-img flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                                                                    ${imageUrl
                        ? `<img src="${escapeHtml(imageUrl)}"
                                        alt="${escapeHtml(item.name)}"
                                        class="w-full h-full object-cover rounded-lg">`
                        : `<i class="fas fa-utensils"></i>`
                    }
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <p class="font-semibold text-gray-900">${escapeHtml(item.name)}</p>
                                                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                                                        ${vegetarian ? `<span class="rounded bg-green-50 px-2 py-0.5 text-xs text-green-700">Vegetarian</span>` : ''}
                                                                        ${popular ? `<span class="rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-700">Popular</span>` : ''}
                                                                        ${hasSizes ? `<span class="rounded bg-indigo-50 px-2 py-0.5 text-xs text-indigo-700">${sizes.length} sizes</span>` : ''}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td class="px-4 md:px-6 py-4 text-sm text-gray-600 w-[20%]">${escapeHtml(categoryName)}</td>

                                                        <td class="px-4 md:px-6 py-4 w-[30%]">${renderMenuPrice(item)}</td>

                                                        <td class="px-4 md:px-6 py-4 w-[10%]">
                                                            <button type="button"
                                                                    class="availabilityBtn rounded-full px-3 py-1 text-xs font-medium
                                                                           ${available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'}"
                                                                    data-id="${escapeHtml(item.id)}">
                                                                ${available ? 'Available' : 'Unavailable'}
                                                            </button>
                                                        </td>

                                                        <td class="px-4 md:px-6 py-4 text-right w-[10%]">
                                                            <button type="button"
                                                                    class="editBtn rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium
                                                                           text-gray-700 hover:bg-gray-100"
                                                                    data-id="${escapeHtml(item.id)}">
                                                                <i class="fas fa-pen mr-1"></i> Edit
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `;
            }).join('');

            tbody.querySelectorAll('.editBtn').forEach(button => {
                button.addEventListener('click', () => {
                    const item = allMenuItems.find(menu => String(menu.id) === button.dataset.id);
                    if (item) openDrawer(item);
                });
            });

            tbody.querySelectorAll('.availabilityBtn').forEach(button => {
                button.addEventListener('click', () => {
                    const item = allMenuItems.find(menu => String(menu.id) === button.dataset.id);
                    if (item) toggleAvailability(item.id, isAvailable(item));
                });
            });
        }

        /* ───────── Categories for the form ───────── */

        const CategoryService = {
            cache: null,

            async fetchCategories(forceRefresh = false) {
                if (this.cache && !forceRefresh) return this.cache;

                const response = await apiRequest('/api/v1/owner/restaurant/category');
                this.cache = response.data || [];
                return this.cache;
            },

            async populateSelect(selectElement, selectedId = null) {
                const categories = await this.fetchCategories();

                selectElement.replaceChildren();

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = categories.length ? 'Select a category' : 'No categories found';
                selectElement.appendChild(placeholder);

                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    option.selected = selectedId != null && String(category.id) === String(selectedId);
                    selectElement.appendChild(option);
                });
            }
        };

        /* ───────── Drawer ───────── */

        async function openDrawer(menu = null) {
            form.reset();
            clearImage();
            resetSizePrices();

            editingMenuId = menu?.id ?? null;
            document.getElementById('drawerTitle').textContent = menu ? 'Edit Menu Item' : 'Add Menu Item';

            try {
                await CategoryService.populateSelect(
                    categorySelect,
                    menu?.category?.id ?? menu?.category_id ?? null
                );

                if (menu) {
                    form.elements.name.value = menu.name || '';
                    form.elements.description.value = menu.description || '';
                    form.elements.is_vegetarian.checked = Boolean(menu.is_vegetarian ?? menu.isVegetarian);
                    form.elements.is_popular.checked = Boolean(menu.is_popular ?? menu.isPopular);

                    setAvailability(isAvailable(menu));

                    const sizes = getMenuSizes(menu);
                    const sizeType = getSizeType(menu);
                    const usesSizes = (menu.has_sizes || menu.pricing_mode === 'sizes' || sizes.length > 0) && sizeType;

                    if (usesSizes) {
                        form.querySelector('input[name="pricing_mode"][value="sizes"]').checked = true;
                        document.getElementById('sizeType').value = sizeType;
                        updatePricingMode();

                        MENU_SIZE_OPTIONS[sizeType].forEach(definition => {
                            const match = sizes.find(size =>
                                size.code === definition.code ||
                                size.name?.toLowerCase() === definition.name.toLowerCase()
                            );
                            const priceInput = document.getElementById(`size-price-${definition.code}`);

                            if (priceInput && match) priceInput.value = match.price;
                        });
                    } else {
                        form.querySelector('input[name="pricing_mode"][value="single"]').checked = true;
                        form.elements.price.value = menu.price ?? '';
                        updatePricingMode();
                    }

                    const imageUrl = menu.image_url || menu.image;
                    if (imageUrl) {
                        document.getElementById('previewImg').src = imageUrl;
                        document.getElementById('previewFileName').textContent = 'Current item photo';
                        document.getElementById('dropZone').classList.add('hidden');
                        document.getElementById('imagePreview').classList.remove('hidden');
                    }
                } else {
                    setAvailability(true);
                    updatePricingMode();
                }

                drawer.classList.add('open');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } catch (error) {
                console.error('Unable to open menu form:', error);
                showMessage(error.message, 'error');
            }
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');

            editingMenuId = null;
            form.reset();
            clearImage();
            resetSizePrices();
            updatePricingMode();
            setAvailability(true);
        }

        document.getElementById('openDrawer').addEventListener('click', () => openDrawer());
        document.getElementById('closeDrawer').addEventListener('click', closeDrawer);
        overlay.addEventListener('click', closeDrawer);

        /* ───────── Pricing and sizes ───────── */

        function updatePricingMode() {
            const mode = form.querySelector('input[name="pricing_mode"]:checked')?.value || 'single';
            const isSized = mode === 'sizes';

            document.getElementById('singlePriceSection').classList.toggle('hidden', isSized);
            document.getElementById('sizePricingSection').classList.toggle('hidden', !isSized);

            const priceInput = document.getElementById('menuPrice');
            priceInput.disabled = isSized;
            priceInput.required = !isSized;

            document.getElementById('sizeType').disabled = !isSized;

            if (isSized) {
                renderSizePrices();
            } else {
                resetSizePrices();
            }
        }

        function renderSizePrices() {
            const sizeType = document.getElementById('sizeType').value;
            const container = document.getElementById('sizePriceFields');
            const definitions = MENU_SIZE_OPTIONS[sizeType] || [];

            // Preserve any prices already typed when switching size sets.
            const oldValues = {};
            container.querySelectorAll('input[type="number"]').forEach(input => {
                oldValues[input.id] = input.value;
            });

            container.replaceChildren();

            definitions.forEach((size, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'rounded-xl border border-gray-200 p-3';

                const label = document.createElement('label');
                label.htmlFor = `size-price-${size.code}`;
                label.className = 'block text-sm font-semibold text-gray-700 mb-2';
                label.textContent = `${size.name} Price *`;

                const priceWrapper = document.createElement('div');
                priceWrapper.className = 'relative';

                const currency = document.createElement('span');
                currency.className = 'absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500';
                currency.textContent = 'Rs.';

                const priceInput = document.createElement('input');
                priceInput.id = `size-price-${size.code}`;
                priceInput.type = 'number';
                priceInput.name = `sizes[${index}][price]`;
                priceInput.min = '0';
                priceInput.step = '0.01';
                priceInput.required = true;
                priceInput.placeholder = '0.00';
                priceInput.className = 'field-input text-sm';
                priceInput.style.paddingLeft = '48px';
                priceInput.value = oldValues[priceInput.id] || '';

                const hidden = (field, value) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `sizes[${index}][${field}]`;
                    input.value = value;
                    return input;
                };

                priceWrapper.append(
                    currency,
                    priceInput,
                    hidden('name', size.name),
                    hidden('code', size.code),
                    hidden('sort_order', index + 1)
                );

                wrapper.append(label, priceWrapper);
                container.appendChild(wrapper);
            });
        }

        function resetSizePrices() {
            document.getElementById('sizePriceFields').replaceChildren();
        }

        /* ───────── Availability ───────── */

        function toggleAvailabilityForm() {
            setAvailability(document.getElementById('is_available').value !== '1');
        }

        function setAvailability(value) {
            const isOn = value === true || value === 1 || value === '1';

            const toggle = document.getElementById('availToggle');
            const label = document.getElementById('availLabel');

            toggle.classList.toggle('on', isOn);
            toggle.setAttribute('aria-checked', String(isOn));

            document.getElementById('is_available').value = isOn ? '1' : '0';

            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-indigo-600', isOn);
            label.classList.toggle('text-red-600', !isOn);
        }

        /* ───────── Image upload and preview ───────── */

        function handleImageSelect(event) {
            const file = event.target.files[0];
            if (file) validateAndPreviewImage(file);
        }

        function handleDragOver(event) {
            event.preventDefault();
            document.getElementById('dropZone').classList.add('drag-over');
        }

        function handleDragLeave() {
            document.getElementById('dropZone').classList.remove('drag-over');
        }

        function handleDrop(event) {
            event.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');

            const file = event.dataTransfer.files[0];
            if (!file) return;

            try {
                const transfer = new DataTransfer();
                transfer.items.add(file);
                document.getElementById('imageInput').files = transfer.files;
            } catch (error) {
                console.warn('Unable to assign dropped file:', error);
            }

            validateAndPreviewImage(file);
        }

        function validateAndPreviewImage(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (!allowedTypes.includes(file.type)) {
                showMessage('Please select a JPG, PNG, or WEBP image.', 'error');
                document.getElementById('imageInput').value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showMessage('Image size must not exceed 5MB.', 'error');
                document.getElementById('imageInput').value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = event => {
                document.getElementById('previewImg').src = event.target.result;
                document.getElementById('previewFileName').textContent = file.name;
                document.getElementById('dropZone').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }

        function clearImage(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            document.getElementById('imageInput').value = '';
            document.getElementById('previewImg').src = '';
            document.getElementById('previewFileName').textContent = '';

            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('dropZone').classList.remove('hidden');
        }

        /* ───────── Create / update ───────── */

        form.addEventListener('submit', async event => {
            event.preventDefault();

            if (!form.reportValidity()) return;

            const mode = form.querySelector('input[name="pricing_mode"]:checked')?.value || 'single';
            const formData = new FormData(form);

            formData.set('is_vegetarian', form.elements.is_vegetarian.checked ? '1' : '0');
            formData.set('is_popular', form.elements.is_popular.checked ? '1' : '0');
            formData.set('is_available', form.elements.is_available.value);
            formData.set('has_sizes', mode === 'sizes' ? '1' : '0');

            if (mode === 'single') {
                formData.delete('size_type');
                formData.set('price', form.elements.price.value);

                for (const key of [...formData.keys()]) {
                    if (key.startsWith('sizes[')) formData.delete(key);
                }
            } else {
                const sizeType = document.getElementById('sizeType').value;
                const baseCode = sizeType === 'hf' ? 'full' : 'medium';
                const baseInput = document.getElementById(`size-price-${baseCode}`);

                if (!baseInput || baseInput.value === '') {
                    showMessage(`Please enter the ${sizeType === 'hf' ? 'Full' : 'Medium'} price.`, 'error');
                    return;
                }

                formData.set('size_type', sizeType);
                // Base price follows the model convention (Full / Medium).
                formData.set('price', baseInput.value);
            }

            let url = '/api/v1/owner/restaurant/add-menu';

            if (editingMenuId) {
                url = `/api/v1/owner/restaurant/update-menu/${editingMenuId}`;
                formData.set('_method', 'PATCH'); // method spoofing for multipart
            }

            const originalHtml = saveMenuButton.innerHTML;

            saveMenuButton.disabled = true;
            saveMenuButton.classList.add('opacity-60', 'cursor-not-allowed');
            saveMenuButton.textContent = editingMenuId ? 'Updating...' : 'Saving...';

            try {
                const result = await apiRequest(url, { method: 'POST', body: formData });

                showMessage(
                    result.message || (editingMenuId
                        ? 'Menu item updated successfully.'
                        : 'Menu item created successfully.'),
                    'success'
                );

                closeDrawer();
                await fetchMenu();
            } catch (error) {
                console.error('Failed to save menu item:', error);
                showMessage(error.message, 'error');
            } finally {
                saveMenuButton.disabled = false;
                saveMenuButton.classList.remove('opacity-60', 'cursor-not-allowed');
                saveMenuButton.innerHTML = originalHtml;
            }
        });

        /* ───────── Toggle availability from the table ───────── */

        async function toggleAvailability(menuId, currentStatus) {
            try {
                const result = await apiRequest(
                    `/api/v1/owner/restaurant/menu/${menuId}/availability`,
                    {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ is_available: !currentStatus })
                    }
                );

                showMessage(result.message || 'Item availability updated successfully.', 'success');
                await fetchMenu();
            } catch (error) {
                console.error('Failed to update availability:', error);
                showMessage(error.message, 'error');
            }
        }

        /* ───────── CSV import ───────── */

        const importForm = document.getElementById('menuImportForm');
        const importFileInput = document.getElementById('menuCsv');
        const importButton = document.getElementById('importButton');
        const importLabel = document.getElementById('importButtonLabel');

        // Button opens the file picker; choosing a file starts the import.
        importButton.addEventListener('click', () => importFileInput.click());
        importFileInput.addEventListener('change', () => {
            if (importFileInput.files.length) importForm.requestSubmit();
        });

        importForm.addEventListener('submit', async event => {
            event.preventDefault();

            if (!importFileInput.files.length) {
                showToast(message = 'Please select a CSV file.', type = 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', importFileInput.files[0]);

            importButton.disabled = true;
            importButton.classList.remove('is-done');
            importButton.classList.add('is-loading');
            importLabel.textContent = 'Importing...';

            let succeeded = false;

            try {
                const response = await fetch('/api/v1/owner/restaurant/menu/import', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Menu import failed. Please check the file.');
                }

                console.log(data)

                showToast(
                    `Import completed. New items: ${data.imported}, Updated items: ${data.updated}, Failed rows: ${data.failed_count}`,
                    data.failed_count > 0 ? 'error' : 'success'
                );

                succeeded = data.failed_count === 0;
                await fetchMenu();
            } catch (error) {
                showToast(error.message);
            } finally {
                importFileInput.value = '';
                importButton.disabled = false;
                importButton.classList.remove('is-loading');
                importLabel.textContent = succeeded ? 'Imported' : 'Import';

                if (succeeded) {
                    importButton.classList.add('is-done');
                    setTimeout(() => {
                        importButton.classList.remove('is-done');
                        importLabel.textContent = 'Import';
                    }, 2000);
                }
            }
        });

        const availabilityFilter = document.getElementById('availabilityFilter');

        availabilityFilter.addEventListener('change', () => {
            const selectedAvailability = availabilityFilter.value;

            const filteredItems = allMenuItems.filter(item => {
                const isAvailable =
                    item.is_available === true ||
                    item.is_available === 1 ||
                    item.is_available === '1';

                if (selectedAvailability === 'available') {
                    return isAvailable;
                }

                if (selectedAvailability === 'unavailable') {
                    return !isAvailable;
                }

                return true;
            });

            renderMenuTable(filteredItems);
        });

        /* ───────── Init ───────── */

        document.addEventListener('DOMContentLoaded', () => {
            updatePricingMode();
            fetchMenu();
        });
    </script>
@endsection