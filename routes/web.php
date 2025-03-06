<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('chartPage', [
        'symbol' => request('symbol', 'AAPL'),
        'timespan'=> request('timespan','day'),
        'timemultiplier'=> request('timemultiplier','1'),
        'chartType' => request('chartType', 'candlestick'),
    ]);
})->name('Home');

require __DIR__ . "/api.php";