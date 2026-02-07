@extends('layouts.app')

@section('title', 'Staff | ' . config('app.name'))

@section('content')

<style>
    input:focus, select:focus {
        outline: none;
    }
</style>



    <header class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Staff Members</h1>
        </div>
        <div class="flex space-x-3">
            <button onclick="openStaffModal()"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                + Add Staff
            </button>
        </div>
    </header>

    <section class="mt-12">
        <div id="staffGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Staff cards injected here -->
        </div>
    </section>


    <div id="staffModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
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
    </div>




    <script>
        function staffCard(staff) {
            return `
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    ${staff.name.charAt(0)}
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">${staff.name}</p>
                    <p class="text-xs text-gray-500">${staff.role}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-bold">
                    Active
                </span>
            </div>
        `;
        }
    </script>


    <script>
        const token = localStorage.getItem('auth_token');

        async function fetchStaff() {
            const res = await fetch('/api/v1/owner/restaurant/staff', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const json = await res.json();
            const grid = document.getElementById('staffGrid');
            grid.innerHTML = '';

            json.data.forEach(staff => {
                grid.insertAdjacentHTML('beforeend', staffCard(staff));
            });
        }

        fetchStaff();
    </script>

    <script>
        function openStaffModal() {
            document.getElementById('staffModal').classList.remove('hidden');
            document.getElementById('staffModal').classList.add('flex');
        }

        function closeStaffModal() {
            document.getElementById('staffModal').classList.add('hidden');
        }

            async function createStaff(e) {
                e.preventDefault();

                const payload = {
                    name: document.getElementById('staffName').value,
                    email: document.getElementById('staffEmail').value,
                    phone: document.getElementById('staffPhone').value,
                    password: document.getElementById('staffPassword').value,
                    password_confirmation: document.getElementById('staffPasswordConfirm').value,
                    role: document.getElementById('staffRole').value
                };

                const res = await fetch('/api/v1/owner/restaurant/staff', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    alert('Failed to create staff');
                    return;
                }

                closeStaffModal();
                fetchStaff();
            }

    </script>




@endsection
