<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::redirect('/', '/auth');
// Route::group('', function () {

Route::get('/auth', function () {
    return view('authentication');
})->name('auth');


// SUPER ADMIN
Route::get('/super-admin/login', fn () => view('super-admin.login'))
    ->name('super-admin.login-page');

Route::prefix('super-admin')
    ->name('web-super-admin.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('super-admin.dashboard'))->name('dashboard');
        Route::get('/restaurants', fn () => view('super-admin.restaurants'))->name('restaurants.index');
        Route::get('/plans', fn () => view('super-admin.plans'))->name('plans.index');
        Route::get('/subscriptions', fn () => view('super-admin.subscriptions'))->name('subscriptions.index');
        Route::get('/transactions', fn () => view('super-admin.transactions'))->name('transactions.index');
        Route::get('/reports', fn () => view('super-admin.reports'))->name('reports');
        Route::get('/settings', fn () => view('super-admin.settings'))->name('settings.index');
    });

Route::get('pricing', function(){
    return view('pricing');
})->name('pricing');

Route::get('/payment-success', function () {
    return view('payment-success');
});

// Restaurant Routes
Route::get('restaurant/dashboard', function () {
    return view('restaurant/dashboard');
})->name('restaurant.dashboard');

Route::get('restaurant/table', function () {
    return view('restaurant/table');
})->name('restaurant.table');

Route::get('restaurant/menu', function () {
    return view('restaurant/menu');
})->name('restaurant.menu');

Route::get('restaurant/reports', function () {
    return view('restaurant/reports');
})->name('restaurant.reports');

Route::get('restaurant/invoices', function () {
    return view('restaurant/invoices');
})->name('restaurant.invoices');

Route::get('restaurant/staff', function () {
    return view('restaurant/staff');
})->name('restaurant.staff');

Route::get('restaurant/setting', function () {
    return view('restaurant/setting');
})->name('restaurant.setting');

Route::get('restaurant/qr-stickers', function () {
    return view('restaurant/qr');
})->name('restaurant.qr-stickers');

// Staff Route
Route::get('staff/dashboard', function () {
    return view('staff/dashboard');
})->name('staff.dashboard');

Route::get('staff/billing', function () {
    return view('staff/billing');
})->name('staff.billing');

Route::get('qr/{token}', function () {
    return view('customer/dashboard');
})->name('customer.dashboard');

Route::view('/offline', 'offline');


// Route::get('test', function () {
//     return view('test/test');
// })->name('test');

// });

