<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::redirect('/', '/auth');
// Route::group('', function () {

Route::get('/auth', function () {
    return view('authentication');
})->name('auth');

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


// Route::get('test', function () {
//     return view('test/test');
// })->name('test');

// });

