<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use Illuminate\Http\Request;

class SymbolController extends Controller
{
    /**
     * Display a listing of the tickers.
     */
    public function index()
    {
        // Get all tickers or filter by user
        $symbols = Symbol::where('is_active', true)->get();
        return response()->json($symbols);
    }

    /**
     * Store a newly created ticker in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string|unique:symbols,symbol',
        ]);

        $symbol = Symbol::create([
            'symbol' => $request->symbol,
        ]);

        return response()->json($symbol, 201);
    }

    /**
     * Add multiple tickers at once.
     */
    public function addMultiple(Request $request)
    {
        $request->validate([
            'symbols' => 'required|array',
            'symbols.*' => 'string',
        ]);

        $added = [];
        
        foreach ($request->symbols as $symbol) {
            // Skip if ticker already exists
            if (Symbol::where('symbol', $symbol)->exists()) {
                continue;
            }
            
            $symbol = Symbol::create([
                'symbol' => $symbol,
            ]);
            
            $added[] = $symbol;
        }

        return response()->json($added, 201);
    }

    /**
     * Remove the specified ticker from storage.
     */
    public function destroy(Symbol $symbol)
    {
        // Optional: Check ownership if needed
        // if ($ticker->user_id && $ticker->user_id !== Auth::id()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $symbol->delete();
        return response()->json(null, 204);
    }
}
