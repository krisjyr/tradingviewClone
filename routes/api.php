<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketDataController;
use App\Http\Controllers\SymbolController;

Route::get('/marketdata/{symbol}/{timespan}/{timemultiplier}', [MarketDataController::class, 'fetch'])->name('marketData.fetch');

Route::get('/search/{string}', [MarketDataController::class, 'search'])->name('marketData.search');


Route::get('/symbols', [SymbolController::class, 'index']);
Route::post('/symbols', [SymbolController::class, 'store']);
Route::post('/symbols/bulk', [SymbolController::class, 'addMultiple']);
Route::delete('/symbols/{symbol}', [SymbolController::class, 'destroy']);