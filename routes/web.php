<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('trading.dashboard');
})->name('dashboard');

Route::get('/wallet', function () {
    return view('trading.wallet');
})->name('wallet');

Route::get('/orders', function () {
    return view('trading.orders');
})->name('orders');
