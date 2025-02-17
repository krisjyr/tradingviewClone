<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
})->name("Home");

Route::get('/chart', function () {
    return Inertia::render('chartPage', [
        'symbol' => request('symbol', 'AAPL'),
    ]);
});

require __DIR__ . "/api.php";