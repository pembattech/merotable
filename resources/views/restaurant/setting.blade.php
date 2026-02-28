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

        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.65rem 0.875rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        .drop-zone {
            transition: border-color 0.2s, background 0.2s;
        }

        .drop-zone.drag-over {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
        }

        .toggle-track {
            width: 44px;
            height: 24px;
            background: #e5e7eb;
            border-radius: 9999px;
            position: relative;
            cursor: pointer;
            transition: background 0.2s;
        }

        .toggle-track.on {
            background: #3b82f6;
        }

        .toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .toggle-track.on .toggle-thumb {
            transform: translateX(20px);
        }
    </style>

    <style>
        .profile-lower {
            padding: 0 24px 24px;
            display: flex;
            align-items: flex-end;
            gap: 18px;
            flex-wrap: wrap;
        }

        .avatar-wrap {
            position: relative;
            margin-top: -40px;
            flex-shrink: 0;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 18px;
            border: 3px solid #fff;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        }

        .avatar-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            background: #22c55e;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .profile-info {
            flex: 1;
            min-width: 200px;
            padding-top: 12px;
        }

        .profile-info h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #111827;
        }

        .profile-info .tagline {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .profile-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .tag {
            background: #eff6ff;
            color: #2563eb;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
        }

        .tag.green {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        .card-header h3 {
            font-size: 0.9rem;
            font-weight: 800;
            color: #111827;
        }

        .card-body {
            padding: 18px 22px;
        }

        /* Fields */
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field-item label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.65rem 0.875rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
            font-family: inherit;
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

        /* Full width */
        .full-card {
            margin-bottom: 20px;
        }

        /* Danger */
        .danger-card {
            border-color: #fee2e2 !important;
        }

        .danger-header {
            border-bottom-color: #fef2f2 !important;
        }

        .danger-header h3 {
            color: #dc2626 !important;
        }

        .danger-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #fef2f2;
            gap: 16px;
        }

        .danger-row:last-child {
            border-bottom: none;
        }

        .danger-row-info p {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #374151;
        }

        .danger-row-info span {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .btn-danger {
            background: #fff;
            border: 1.5px solid #fca5a5;
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            font-family: inherit;
        }

        .btn-danger:hover {
            background: #fef2f2;
            border-color: #dc2626;
        }

        @media (max-width: 700px) {
            .field-row {
                grid-template-columns: 1fr;
            }
        }
    </style>


    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800">Restaurant Profile</h1>
        </div>
        <div class="flex space-x-3">
            <button id="openDrawer"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-blue-700">+
                Edit Profile</button>
        </div>
    </header>

    <div>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-4.5 pb-3.5 border-b border-gray-100">
                <h3 class="text-base font-extrabold text-gray-900">Basic Information</h3>
            </div>
            <div class="pt-[18px] pb-[18px] px-[22px]">
                <div class="field-group">
                    <div class="field-item">
                        <label>Restaurant Name</label>
                        <input class="field-input" type="text" id="fieldName" value="Himalayan Kitchen"
                            oninput="document.getElementById('profileName').textContent=this.value||'Restaurant Name'" />
                    </div>
                    <div class="field-row">
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
        </div>

        <!-- Financial Setting -->

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 pt-4.5 pb-3.5 border-b border-gray-100">

                <h3 class="text-base font-extrabold text-gray-900">Financial Settings</h3>
            </div>

            <div class="pt-[18px] pb-[18px] px-[22px]"></div>


            <!-- Tax & Service Charge -->
            <div class="field-row flex flex-wrap gap-4">
                <div class="field-item flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="tax_percentage">Tax
                        Percentage (%)</label>
                    <input type="number" id="tax_percentage" value="13"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="field-item flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="service_charge_percentage">Service
                        Charge (%)</label>
                    <input type="number" id="service_charge_percentage" value="10"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            <!-- Enable Toggles -->
            <div class="field-row flex flex-wrap gap-4">
                <div class="field-item flex items-center gap-2">
                    <input type="checkbox" id="tax_enabled" class="h-4 w-4 text-blue-600 border-gray-300 rounded" checked />
                    <label class="text-sm text-gray-700 font-medium" for="tax_enabled">Enable
                        Tax</label>
                </div>
                <div class="field-item flex items-center gap-2">
                    <input type="checkbox" id="service_charge_enabled" class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                        checked />
                    <label class="text-sm text-gray-700 font-medium" for="service_charge_enabled">Enable Service
                        Charge</label>
                </div>
            </div>

            <!-- Delivery Charge & Currency -->
            <div class="field-row flex flex-wrap gap-4">
                <div class="field-item flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="delivery_charge">Delivery
                        Charge</label>
                    <input type="number" id="delivery_charge" value="50"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="field-item flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="currency">Currency</label>
                    <input type="text" id="currency" value="NPR"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
        </div>
    </div>


    <!-- Danger Zone -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="card-header danger-header">
            <h3 class="text-base font-extrabold text-gray-900">⚠️ Danger Zone</h3>
        </div>
        <div class="card-body">
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
        // ======================================================
        // CONFIG
        // ======================================================
        const token = localStorage.getItem('auth_token');
        const restroSlug = localStorage.getItem('restro_url');


        // ======================================================
        // PAUSE RESTAURANT  →  PATCH /api/v1/owner/restaurant/status
        // ======================================================
        async function pauseRestaurant() {
            if (!confirm('Hide your restaurant from the customer app until you re-open it?')) return;
            try {
                const res = await fetch('/api/v1/owner/restaurant/status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_active: false
                    })
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Failed ❌', 'error');
                    return;
                }
                showToast('Restaurant paused ⏸', 'error');
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }

        // ======================================================
        // RESET MENU  →  DELETE /api/v1/owner/restaurant/menu/reset
        // ======================================================
        async function resetMenu() {
            const confirmed = prompt('Type RESET to confirm deleting all menu items:');
            if (confirmed !== 'RESET') {
                showToast('Reset cancelled', '');
                return;
            }
            try {
                const res = await fetch('/api/v1/owner/restaurant/menu/reset', {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Failed ❌', 'error');
                    return;
                }
                showToast('All menu items deleted ❌', 'error');
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }

        // ======================================================
        // DELETE ACCOUNT  →  DELETE /api/v1/owner/restaurant
        // ======================================================
        async function deleteAccount() {
            const confirmed = prompt('Type DELETE to permanently delete your restaurant account:');
            if (confirmed !== 'DELETE') {
                showToast('Deletion cancelled', '');
                return;
            }
            try {
                const res = await fetch('/api/v1/owner/restaurant', {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Failed ❌', 'error');
                    return;
                }
                showToast('Account deleted. Redirecting…', 'error');
                setTimeout(() => {
                    window.location.href = '/';
                }, 2000);
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }
    </script>




    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

    <!-- Drawer -->
    <div id="drawer" class="fixed top-4 right-0 w-[500px] translate-x-full transition-transform duration-300 z-50">

        <div
            class="bg-white w-full sm:max-w-lg sm:mx-4 sm:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col max-h-[94vh] shadow-xl overflow-hidden">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 rounded-xl p-2">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800 text-base leading-tight">Add Menu Item</h2>
                        <p class="text-xs text-gray-400">Fill in the details below</p>
                    </div>
                </div>
                <button id="closeDrawer"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl p-2 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="menuForm" class="modal-scroll px-5 py-5 space-y-5 overflow-y-auto">
                <!-- IMAGE UPLOAD -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Item Photo</label>

                    <div id="dropZone"
                        class="drop-zone border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 flex flex-col items-center justify-center gap-2 cursor-pointer py-8 hover:border-blue-400 hover:bg-blue-50/40"
                        onclick="document.getElementById('imageInput').click()" ondragover="handleDragOver(event)"
                        ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-gray-700">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG up to 5MB</p>
                        </div>
                    </div>

                    <div id="imagePreview"
                        class="hidden relative rounded-2xl overflow-hidden border border-gray-200 shadow-sm"
                        style="height:180px;">
                        <img id="previewImg" src="" alt="Preview" class="w-full h-full object-cover" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end justify-between px-4 py-3">
                            <span id="previewFileName" class="text-white text-xs font-medium truncate max-w-xs"></span>
                            <button onclick="clearImage(event)"
                                class="bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Change
                            </button>
                        </div>
                    </div>

                    <input type="file" id="imageInput" accept="image/*" class="hidden"
                        onchange="handleImageSelect(event)" />
                </div>

                <!-- CATEGORY -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <div class="relative">
                        <select name="category_id" id="categorySelect" class="field-input appearance-none pr-10">
                            <option value="">Loading categories...</option>
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- NAME -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Item Name</label>
                    <input id="menuName" type="text" name="name" placeholder="e.g. Chicken Momo"
                        class="field-input" />
                </div>


                <!-- PRICE + AVAILABILITY -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold select-none">Rs.</span>
                            <input id="menuPrice" type="number" name="price" min="0" placeholder="0"
                                class="field-input pl-10" style="padding-left: 40px;" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Availability</label>
                        <div class="flex items-center gap-3" style="height:42px;">
                            <div id="availToggle" class="toggle-track on" onclick="toggleAvailabilityForm()">
                                <div class="toggle-thumb"></div>
                                <input type="hidden" name="is_available" id="is_available" value="1">
                            </div>
                            <span id="availLabel" class="text-sm font-semibold text-blue-600">Available</span>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                        <span class="text-gray-400 font-normal ml-1">(optional)</span>
                    </label>
                    <textarea id="menuDesc" rows="2" placeholder="Brief description of the item..."
                        class="field-input resize-none"></textarea>
                </div>


                <!-- FOOTER -->
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/80 flex-shrink-0">
                    <div class="flex gap-3">
                        <button onclick="closeDrawer()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-[2] bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold py-3 rounded-xl transition duration-150 flex items-center justify-center gap-2 shadow-lg shadow-blue-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Menu Item
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        let allMenuItems = [];
        let activeCategory = 'All'; // Track the selected category

        async function fetchMenu() {
            try {

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
            // const container = document.getElementById('categoryButtons');

            // container.innerHTML = '';

            // // ===== 1️⃣ Add "All" Button First =====
            // const allButton = document.createElement('button');
            // allButton.textContent = 'All';
            // allButton.dataset.category = 'All';
            // allButton.className =
            //     'category-btn bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-xs font-bold hover:bg-gray-100';

            // allButton.addEventListener('click', () => filterByCategory('All'));
            // container.appendChild(allButton);

            // // Get unique categories safely
            // const categories = [
            //     ...new Set(
            //         menu
            //         .map(item => item?.category?.name)
            //         .filter(Boolean) // remove null/undefined
            //     )
            // ];

            // console.log(categories);


            // categories.forEach(cat => {
            //     const button = document.createElement('button');
            //     button.textContent = cat;
            //     button.dataset.category = cat;
            //     button.className =
            //         'category-btn bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-xs font-bold hover:bg-gray-100';

            //     button.addEventListener('click', () => filterByCategory(cat));
            //     container.appendChild(button);
            // });

            // // Set active button
            // setActiveButton(activeCategory);
        }


        function renderMenuTable(items) {
        //     const tbody = document.getElementById('menuTableBody');
        //     tbody.innerHTML = '';

        //     items.forEach(item => {
        //         const tr = document.createElement('tr');
        //         tr.className = 'hover:bg-gray-50 transition';
        //         tr.innerHTML = `
        //     <td class="px-6 py-4">
        //         <div class="font-bold text-gray-800">${item.name}</div>
        //     </td>
        //     <td class="px-6 py-4 text-sm text-gray-600">${item.category.name}</td>
        //     <td class="px-6 py-4 text-sm font-bold">Rs. ${item.price}</td>
        //     <td class="px-6 py-4">
        //         <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold ${item.isAvailable ? 'bg-green-100 text-green-700 italic' : 'bg-red-100 text-red-700 italic'}">
        //             ● ${item.isAvailable ? 'In Stock' : 'Out of Stock'}
        //         </span>
        //     </td>
        //     <td class="px-6 py-4 text-sm text-blue-600 font-bold space-x-3">
        //         <button data-menu='${JSON.stringify(item)}' class="editBtn hover:underline">Edit</button>
        //         <button data-menu-id='${item.id}' data-menu-status='${item.isAvailable ? 'true' : 'false'}' class="availabilityBtn ${item.isAvailable ? 'text-red-400' : 'text-green-500'} hover:underline">${item.isAvailable ? 'Disable' : 'Enable'}</button>
        //     </td>
        // `;
        //         tbody.appendChild(tr);


        //         // Attach listener here
        //         tr.querySelector('.editBtn').addEventListener('click', function() {
        //             const menu = JSON.parse(this.dataset.menu);
        //             openDrawer(menu);
        //         });


        //     });
        }


        // ---------- Drawer Elements ----------
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('overlay');
        const form = document.getElementById('menuForm');
        const categorySelect = document.getElementById('categorySelect');
        const openDrawerBtn = document.getElementById('openDrawer');
        const closeDrawerBtn = document.getElementById('closeDrawer');

        let editingMenuId = null; // null = Add, otherwise Edit

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
            console.log(menu)
            drawer.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');

            if (menu) {
                // Edit mode
                editingMenuId = menu.id;
                console.log(editingMenuId);
                form.name.value = menu.name;
                form.price.value = menu.price;
                setAvailability(menu.isAvailable);
                CategoryService.populateSelect(categorySelect, menu.category.id);
            } else {
                // Add mode
                editingMenuId = null;
                console.log(editingMenuId);

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


        // Image
        function handleImageSelect(e) {
            const file = e.target.files[0];
            if (file) loadImagePreview(file);
        }

        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.add('drag-over');
        }

        function handleDragLeave() {
            document.getElementById('dropZone').classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) loadImagePreview(file);
        }

        function loadImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
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
            const label = document.getElementById('availLabel');
            const hiddenInput = document.getElementById('is_available');

            const isOn = !toggle.classList.contains('on');

            toggle.classList.toggle('on', isOn);
            toggle.setAttribute('aria-pressed', isOn);

            hiddenInput.value = isOn ? 1 : 0;

            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600', !isOn);
        }

        // for edit mode
        function setAvailability(value) {
            const toggle = document.getElementById('availToggle');
            const label = document.getElementById('availLabel');
            const hiddenInput = document.getElementById('is_available');

            const isOn = value == 1 || value === true;

            toggle.classList.toggle('on', isOn);
            toggle.setAttribute('aria-pressed', isOn);

            hiddenInput.value = isOn ? 1 : 0;

            label.textContent = isOn ? 'Available' : 'Unavailable';
            label.classList.toggle('text-blue-600', isOn);
            label.classList.toggle('text-red-600', !isOn);
        }



        // ---------- Form Submit (Add/Edit) ----------
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const payload = {
                category_id: Number(form.category_id.value),
                name: form.name.value,
                price: Number(form.price.value),
                is_available: Number(form.is_available.value)
            };

            console.log(payload);

            let url = '';
            let method = '';

            if (editingMenuId) {
                url = `/api/v1/owner/restaurant/update-menu/${editingMenuId}`;
                method = 'PATCH';
            } else {
                url = '/api/v1/owner/restaurant/add-menu';
                method = 'POST';
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

                if (!data.success) {
                    // API returned an error (like duplicate name)
                    showToast(data.message || 'Something went wrong ❌', 'error');
                    return;
                }


                let message = editingMenuId ?
                    `🎉 '${data.data.name}' has been update to ${data.data.category.name}!` :
                    `🎉 '${data.data.name}' has been added to ${data.data.category.name}!`

                showToast(message, 'success');

                closeDrawer();

                fetchMenu();

            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        });


        // Toggle availability
        document.getElementById('menuTableBody').addEventListener('click', function(e) {
            if (e.target.classList.contains('availabilityBtn')) {
                toggleAvailability(e.target.dataset.menuId, e.target.dataset.menuStatus === 'true');
            }
        });


        async function toggleAvailability(menuID, currentStatus) {

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

                if (!data.success) {
                    // API returned an error (like duplicate name)
                    showToast(data.message || 'Something went wrong ❌', 'error');
                    return;
                }

                showToast(`'${data.data.name}' availability updated successfully!`, 'success');

                fetchMenu(); // refresh table
            } catch (err) {
                console.error(err);
                showToast('Something went wrong ❌', 'error');
            }
        }
    </script>


@endsection
