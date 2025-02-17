<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketDataController;

Route::get('/marketdata/{symbol}', [MarketDataController::class, 'fetch'])->name('marketData.fetch');
