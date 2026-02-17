@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')


    <header class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-extrabold text-gray-800">Restaurant Overview</h1>
        <div class="flex items-center space-x-4">
            <span
                class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold ring-1 ring-green-600">RESTAURANT
                OPEN</span>
            <button class="bg-white border p-2 rounded-lg shadow-sm">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </button>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Today's Revenue</p>
            <h3 class="text-3xl font-bold mt-2">Rs. 12,450</h3>
            <p class="text-xs text-green-600 mt-2 font-bold">↑ 12% from yesterday</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Active Orders</p>
            <h3 class="text-3xl font-bold mt-2">08</h3>
            <p class="text-xs text-blue-600 mt-2 font-bold">Live tracking active</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Occupied Tables</p>
            <h3 class="text-3xl font-bold mt-2">5/15</h3>
            <p class="text-xs text-gray-400 mt-2 font-bold">Standard Capacity</p>
        </div>
    </div>

    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Live Table Map</h2>
            <button class="text-sm text-blue-600 font-bold hover:underline">Refresh Status</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg border-2 border-blue-700">
                <div class="flex justify-between items-start">
                    <span class="text-2xl font-black">T-01</span>
                    <span
                        class="text-[10px] bg-blue-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Occupied</span>
                </div>
                <div class="mt-4">
                    <p class="text-xs opacity-80">Order Amount</p>
                    <p class="font-bold">Rs. 850</p>
                </div>
            </div>

            <div
                class="bg-white p-4 rounded-xl border border-gray-200 hover:border-blue-400 transition cursor-pointer group">
                <span class="text-2xl font-black text-gray-300 group-hover:text-blue-500">T-02</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>

            <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg border-2 border-blue-700">
                <div class="flex justify-between items-start">
                    <span class="text-2xl font-black">T-03</span>
                    <span
                        class="text-[10px] bg-blue-800 px-2 py-0.5 rounded font-bold uppercase tracking-wider">Occupied</span>
                </div>
                <div class="mt-4">
                    <p class="text-xs opacity-80">Order Amount</p>
                    <p class="font-bold">Rs. 1,200</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <span class="text-2xl font-black text-gray-300">T-04</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200">
                <span class="text-2xl font-black text-gray-300">T-05</span>
                <p class="mt-4 text-xs font-bold text-gray-400">Available</p>
            </div>
        </div>
    </section>



@endsection
