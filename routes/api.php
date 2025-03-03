<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketDataController;

Route::get('/marketdata/{symbol}/{timespan}/{timemultiplier}', [MarketDataController::class, 'fetch'])->name('marketData.fetch');
